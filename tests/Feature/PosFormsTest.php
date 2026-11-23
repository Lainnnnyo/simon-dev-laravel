<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class PosFormsTest extends TestCase {
 use RefreshDatabase;
 protected function setUp(): void {
  parent::setUp();
  $this->actingAs(User::factory()->create(['username'=>'testoperator','full_name'=>'Test Operator']));
 }
 public function test_customer_validation_and_create_update(): void {
  $this->from('/customers/create')->post('/customers',['full_name'=>'Test','email'=>'bad','phone'=>'123'])->assertSessionHasErrors('email')->assertSessionHasInput('full_name','Test');
  $this->post('/customers',['full_name'=>'Test','email'=>'test@example.test','phone'=>'123'])->assertRedirect('/customers');
  $customer=Customer::firstOrFail();
  $this->get('/customers/'.$customer->id.'/edit')->assertOk()->assertSee('test@example.test');
  $this->put('/customers/'.$customer->id,['full_name'=>'Updated','email'=>'test@example.test','phone'=>'456'])->assertRedirect('/customers');
  $this->assertDatabaseHas('customers',['full_name'=>'Updated']);
 }
 public function test_user_unique_validation_and_updates(): void {
  $this->seed(PosSeeder::class);
  $this->post('/users',['username'=>'admin01','full_name'=>'Duplicate'])->assertSessionHasErrors('username');
  $this->post('/users',['username'=>'newuser','full_name'=>'New User'])->assertRedirect('/users');
  $u=User::where('username','newuser')->firstOrFail();
  $this->put('/users/'.$u->id,['username'=>'newuser','full_name'=>'Renamed'])->assertRedirect('/users');
  $this->assertSame('Renamed',$u->fresh()->full_name);
  $this->put('/users/'.$u->id,['username'=>'admin01','full_name'=>'Invalid'])->assertSessionHasErrors('username');
 }
 public function test_avatar_preparation_replacement_and_validation(): void {
  Storage::fake('public'); $this->seed(PosSeeder::class); $u=User::firstOrFail();
  $data=['username'=>$u->username,'full_name'=>$u->full_name];
  $url='/users/'.$u->id;
  $this->put($url,$data+['avatar'=>UploadedFile::fake()->create('bad.txt',10,'text/plain')])->assertSessionHasErrors('avatar');
  $this->put($url,$data+['avatar'=>UploadedFile::fake()->image('large.jpg')->size(2049)])->assertSessionHasErrors('avatar');
  $this->put($url,$data+['avatar'=>UploadedFile::fake()->image('photo.jpg',640,480)])->assertRedirect('/users');
  $path=$u->fresh()->avatar; Storage::disk('public')->assertExists($path);
  $size=getimagesizefromstring(Storage::disk('public')->get($path));
  $this->assertSame([256,256],[$size[0],$size[1]]);
  $this->get('/avatars/'.basename($path))->assertOk()->assertHeader('Content-Type','image/jpeg');
  $this->put($url,$data)->assertRedirect('/users'); $this->assertSame($path,$u->fresh()->avatar);
  $this->put($url,$data+['avatar'=>UploadedFile::fake()->image('replacement.png')])->assertRedirect('/users');
  Storage::disk('public')->assertMissing($path);
  $this->get('/users')->assertOk()->assertSee(basename($u->fresh()->avatar));
 }
 public function test_missing_records_and_placeholder(): void {
  $this->get('/customers/999/edit')->assertNotFound();
  $this->get('/users/999/edit')->assertNotFound();
  $this->seed(PosSeeder::class);
  $this->get('/users')->assertOk()->assertSee('avatar-placeholder.svg');
 }
}
