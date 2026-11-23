<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class PosAuthTest extends TestCase {
 use RefreshDatabase;
 public function test_guests_cannot_read_or_write_any_management_route(): void {
  $u=User::factory()->create();
  $c=Customer::create(['full_name'=>'Guest','email'=>'guest@example.test','phone'=>'123']);
  foreach (['customers'=>$c->id,'users'=>$u->id] as $resource=>$id) {
   foreach (["/$resource","/$resource/create","/$resource/$id","/$resource/$id/edit"] as $url) {
    $this->get($url)->assertRedirect(route('login'));
   }
   $this->post("/$resource",[])->assertRedirect(route('login'));
   $this->put("/$resource/$id",[])->assertRedirect(route('login'));
   $this->patch("/$resource/$id",[])->assertRedirect(route('login'));
   $this->delete("/$resource/$id")->assertRedirect(route('login'));
  }
  $this->get('/dashboard')->assertRedirect(route('login'));
  $this->get('/avatars/abc.jpg')->assertRedirect(route('login'));
  $this->assertDatabaseHas('users',['id'=>$u->id]);
  $this->assertDatabaseHas('customers',['id'=>$c->id]);
 }
 public function test_registration_hashes_password_and_populates_pos_fields(): void {
  $this->get('/register')->assertOk();
  $this->post('/register',[
   'username'=>'barista','full_name'=>'Coffee Operator','email'=>'coffee@example.test',
   'password'=>'Secret-coffee-2468','password_confirmation'=>'Secret-coffee-2468',
  ])->assertRedirect(route('dashboard',absolute:false));
  $u=User::where('email','coffee@example.test')->firstOrFail();
  $this->assertAuthenticatedAs($u);
  $this->assertSame('Coffee Operator',$u->name);
  $this->assertSame('Coffee Operator',$u->full_name);
  $this->assertSame('barista',$u->username);
  $this->assertTrue(Hash::check('Secret-coffee-2468',$u->password));
 }
 public function test_registration_rejects_duplicate_username_and_email(): void {
  User::factory()->create(['username'=>'taken','email'=>'taken@example.test']);
  $this->post('/register',['username'=>'taken','full_name'=>'Another','email'=>'taken@example.test','password'=>'password123','password_confirmation'=>'password123'])
   ->assertSessionHasErrors(['username','email']);
  $this->assertGuest(); $this->assertDatabaseCount('users',1);
 }
 public function test_breeze_login_intended_redirect_logout_and_invalid_password(): void {
  $u=User::factory()->create(['password'=>Hash::make('Coffee-password-2468')]);
  $this->get('/login')->assertOk();
  $this->post('/login',['email'=>$u->email,'password'=>'wrong'])->assertSessionHasErrors('email');
  $this->assertGuest();
  $this->get('/customers')->assertRedirect(route('login'));
  $this->post('/login',['email'=>$u->email,'password'=>'Coffee-password-2468'])->assertRedirect('/customers');
  $this->assertAuthenticatedAs($u);
  $this->get('/users')->assertOk();
  $this->post('/logout')->assertRedirect('/'); $this->assertGuest();
  $this->get('/users')->assertRedirect(route('login'));
 }
 public function test_login_rate_limit_blocks_repeated_attempts(): void {
  $u=User::factory()->create(['email'=>'throttle@example.test','password'=>Hash::make('Good-password-123')]);
  for ($i=0;$i<5;$i++) { $this->post('/login',['email'=>$u->email,'password'=>'wrong'])->assertSessionHasErrors('email'); }
  $this->post('/login',['email'=>$u->email,'password'=>'Good-password-123'])->assertSessionHasErrors('email');
  $this->assertGuest();
 }
 public function test_all_resource_methods_are_registered_once_and_protected(): void {
  foreach (['customers','users'] as $resource) {
   foreach (['index','create','store','show','edit','update','destroy'] as $method) {
    $route=app('router')->getRoutes()->getByName("$resource.$method");
    $this->assertNotNull($route);
    $this->assertContains('auth',$route->gatherMiddleware());
   }
  }
 }
 public function test_signed_in_operator_can_view_and_delete_with_avatar_cleanup(): void {
  Storage::fake('public');
  $operator=User::factory()->create(); $other=User::factory()->create(['avatar'=>'avatars/abc.jpg']);
  Storage::disk('public')->put('avatars/abc.jpg','test image');
  $c=Customer::create(['full_name'=>'Delete Guest','email'=>'delete@example.test','phone'=>'123']);
  $this->actingAs($operator);
  $this->get('/customers/'.$c->id)->assertOk()->assertSee('Delete Guest');
  $this->get('/users/'.$other->id)->assertOk();
  $this->delete('/customers/'.$c->id)->assertRedirect('/customers');
  $this->assertDatabaseMissing('customers',['id'=>$c->id]);
  $this->delete('/users/'.$other->id)->assertRedirect('/users');
  $this->assertDatabaseMissing('users',['id'=>$other->id]);
  Storage::disk('public')->assertMissing('avatars/abc.jpg');
 }
 public function test_self_deletion_is_rejected_on_server(): void {
  $u=User::factory()->create(); $this->actingAs($u);
  $this->delete('/users/'.$u->id)->assertSessionHasErrors('delete');
  $this->assertDatabaseHas('users',['id'=>$u->id]);
 }
 public function test_unknown_records_return_404_to_authenticated_users(): void {
  $this->actingAs(User::factory()->create());
  $this->get('/customers/99999')->assertNotFound();
  $this->delete('/users/99999')->assertNotFound();
 }
 public function test_csrf_is_required_outside_test_bypass(): void {
  $this->app['env']='local';
  try { $this->post('/login',['email'=>'a@example.test','password'=>'wrong'])->assertStatus(419); }
  finally { $this->app['env']='testing'; }
 }
}
