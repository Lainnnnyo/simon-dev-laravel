<?php
namespace Tests\Feature;
use App\Models\{User,Product,Customer,Sale};
use App\Services\{SaleService,Money};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Hash,Storage};
use Illuminate\Support\Str;
use Tests\TestCase;
class FinalPosTest extends TestCase {
 use RefreshDatabase;
 private function staff(bool $admin=false): User {return User::factory()->create(['is_staff'=>true,'is_admin'=>$admin]);}
 private function product(int $stock=5): Product{return Product::create(['name'=>'Cyber Latte','price'=>'125.50','stock_quantity'=>$stock]);}
 private function saleData(Product $p,int $qty=2): array{return ['product_id'=>$p->id,'quantity'=>$qty,'request_key'=>(string)Str::uuid()];}
 public function test_guest_management_protection_and_registration_disabled(): void {
  $p=$this->product();$u=$this->staff();$c=Customer::create(['full_name'=>'Test','email'=>'test@example.test']);
  foreach(['/dashboard','/products','/products/create','/products/'.$p->id,'/customers','/users','/sales','/sales/create'] as $url){$this->get($url)->assertRedirect(route('login'));}
  foreach(['products'=>$p->id,'users'=>$u->id,'customers'=>$c->id] as $resource=>$id){
   $this->post('/'.$resource,[])->assertRedirect(route('login'));
   $this->put('/'.$resource.'/'.$id,[])->assertRedirect(route('login'));
   $this->delete('/'.$resource.'/'.$id)->assertRedirect(route('login'));
  }
  $this->post('/sales',$this->saleData($p))->assertRedirect(route('login'));
  $this->get('/register')->assertNotFound();$this->post('/register',[])->assertNotFound();
 }
 public function test_nonstaff_and_regular_staff_cannot_manage_accounts(): void {
  $u=User::factory()->create(['is_staff'=>false]);$this->actingAs($u)->get('/products')->assertRedirect('/login');$this->assertGuest();
  $this->actingAs($this->staff())->get('/users')->assertForbidden();
  $this->post('/users',[])->assertForbidden();
 }
 public function test_login_logout_and_throttling(): void {
  $u=$this->staff();$this->get('/products')->assertRedirect('/login');
  $this->post('/login',['email'=>$u->email,'password'=>'password'])->assertRedirect('/products');
  $this->assertAuthenticatedAs($u);$this->post('/logout')->assertRedirect('/');$this->assertGuest();
  for($i=0;$i<5;$i++){$this->post('/login',['email'=>$u->email,'password'=>'bad'])->assertSessionHasErrors('email');}
  $this->post('/login',['email'=>$u->email,'password'=>'password'])->assertSessionHasErrors('email');$this->assertGuest();
 }
 public function test_money_calculation_uses_integer_cents(): void {
  $this->assertSame(12550,Money::cents('125.50'));$this->assertSame('0.30',Money::decimal(Money::cents('0.10')*3));
 }
 public function test_successful_sale_uses_server_price_and_authenticated_staff(): void {
  $u=$this->staff();$p=$this->product();$this->actingAs($u);
  $this->post('/sales',$this->saleData($p)+['total_price'=>'0.01','sold_by'=>999])->assertRedirect('/sales');
  $this->assertSame(3,$p->fresh()->stock_quantity);
  $this->assertDatabaseHas('sales',['sold_by'=>$u->id,'quantity'=>2,'customer_id'=>null]);
  $this->assertSame('251.00',Sale::first()->total_price);
  $this->get('/sales')->assertOk()->assertSee('Cyber Latte')->assertSee('251.00')->assertSee('Walk-in');
 }
 public function test_overselling_zero_negative_and_fractional_quantities_do_not_write(): void {
  $this->actingAs($this->staff());$p=$this->product(1);
  foreach([2,0,-1,1.5] as $qty){$d=$this->saleData($p);$d['quantity']=$qty;$this->post('/sales',$d)->assertSessionHasErrors('quantity');}
  $this->assertDatabaseCount('sales',0);$this->assertSame(1,$p->fresh()->stock_quantity);
 }
 public function test_archive_and_bad_customer_rejected(): void {
  $this->actingAs($this->staff());$p=$this->product();$p->update(['is_archived'=>true]);
  $this->post('/sales',$this->saleData($p))->assertSessionHasErrors('product_id');
  $p->update(['is_archived'=>false]);$this->post('/sales',$this->saleData($p)+['customer_id'=>999])->assertSessionHasErrors('customer_id');
  $this->assertDatabaseCount('sales',0);$this->assertSame(5,$p->fresh()->stock_quantity);
 }
 public function test_duplicate_submit_deducts_once_and_second_sale_cannot_oversell(): void {
  $this->actingAs($this->staff());$p=$this->product(2);$data=$this->saleData($p);
  $this->post('/sales',$data)->assertRedirect('/sales');$this->post('/sales',$data)->assertRedirect('/sales');
  $this->assertDatabaseCount('sales',1);$this->assertSame(0,$p->fresh()->stock_quantity);
  $this->post('/sales',$this->saleData($p,1))->assertSessionHasErrors('quantity');$this->assertDatabaseCount('sales',1);
 }
 public function test_failed_sale_insert_rolls_back_stock(): void {
  $u=$this->staff();$p=$this->product();
  Sale::creating(function(){throw new \RuntimeException('Simulated insert failure');});
  try{app(SaleService::class)->record($this->saleData($p),$u);$this->fail('Expected insert failure');}
  catch(\RuntimeException $e){$this->assertSame('Simulated insert failure',$e->getMessage());}
  finally{Sale::flushEventListeners();}
  $this->assertSame(5,$p->fresh()->stock_quantity);$this->assertDatabaseCount('sales',0);
 }
 public function test_product_image_validation_create_update_and_archive(): void {
  Storage::fake('public');$this->actingAs($this->staff());
  $data=['name'=>'Mocha','price'=>'150.00','stock_quantity'=>10];
  $this->post('/products',$data+['image'=>UploadedFile::fake()->create('bad.txt',10)])->assertSessionHasErrors('image');
  $this->post('/products',$data+['image'=>UploadedFile::fake()->image('large.jpg')->size(2049)])->assertSessionHasErrors('image');
  $this->post('/products',$data+['image'=>UploadedFile::fake()->image('coffee.png',1000,800)])->assertRedirect('/products');
  $p=Product::firstOrFail();Storage::disk('public')->assertExists($p->image);
  $size=getimagesizefromstring(Storage::disk('public')->get($p->image));$this->assertSame([800,800],[$size[0],$size[1]]);
  $this->get('/products/'.$p->id)->assertOk()->assertSee('Mocha');
  $this->get('/product-images/'.basename($p->image))->assertOk();
  $this->put('/products/'.$p->id,$data+['expected_stock'=>10])->assertRedirect('/products');
  $p->update(['stock_quantity'=>9]);$this->put('/products/'.$p->id,$data+['expected_stock'=>10])->assertSessionHasErrors('stock_quantity');
  $this->assertSame(9,$p->fresh()->stock_quantity);
  $this->delete('/products/'.$p->id)->assertRedirect('/products');$this->assertTrue($p->fresh()->is_archived);
 }
 public function test_customer_crud_optional_phone_and_history_preservation(): void {
  $u=$this->staff(true);$this->actingAs($u);
  $this->post('/customers',['full_name'=>'Guest','email'=>'invalid'])->assertSessionHasErrors('email');
  $this->post('/customers',['full_name'=>'Guest','email'=>'guest@example.test'])->assertRedirect('/customers');
  $c=Customer::firstOrFail();$this->assertNull($c->phone);
  $this->put('/customers/'.$c->id,['full_name'=>'Updated Guest','email'=>'guest@example.test','phone'=>'123'])->assertRedirect('/customers');
  $p=$this->product();$this->post('/sales',$this->saleData($p)+['customer_id'=>$c->id])->assertRedirect('/sales');
  $this->delete('/customers/'.$c->id)->assertRedirect('/customers');$this->assertSoftDeleted('customers',['id'=>$c->id]);
  $this->get('/sales')->assertSee('Updated Guest');$this->assertNotNull(Sale::first()->customer);
 }
 public function test_admin_manages_staff_password_avatar_and_self_protection(): void {
  Storage::fake('public');$admin=$this->staff(true);$this->actingAs($admin);
  $data=['username'=>'newstaff','full_name'=>'New Staff','email'=>'staff@example.test','password'=>'Coffee-password-123','password_confirmation'=>'Coffee-password-123','is_staff'=>1,'is_admin'=>0];
  $this->post('/users',$data)->assertRedirect('/users');$u=User::where('username','newstaff')->firstOrFail();
  $this->assertTrue(Hash::check($data['password'],$u->password));
  $this->post('/users',$data)->assertSessionHasErrors(['email','username']);
  $data['password']='';$data['password_confirmation']='';$hash=$u->password;
  $this->put('/users/'.$u->id,$data+['avatar'=>UploadedFile::fake()->image('photo.png')])->assertRedirect('/users');
  $this->assertSame($hash,$u->fresh()->password);$path=$u->fresh()->avatar;Storage::disk('public')->assertExists($path);
  $p=$this->product();app(SaleService::class)->record($this->saleData($p),$u);
  $this->delete('/users/'.$u->id)->assertRedirect('/users');$this->assertSoftDeleted('users',['id'=>$u->id]);Storage::disk('public')->assertMissing($path);
  $this->assertNotNull(Sale::first()->staff);$this->delete('/users/'.$admin->id)->assertSessionHasErrors('delete');
 }
 public function test_csrf_protection(): void {
  $this->app['env']='local';try{$this->post('/login',[])->assertStatus(419);}finally{$this->app['env']='testing';}
 }
}
