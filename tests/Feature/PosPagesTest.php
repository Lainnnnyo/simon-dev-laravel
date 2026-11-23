<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PosPagesTest extends TestCase {
 use RefreshDatabase;
 public function test_public_pages_and_protected_navigation(): void {
  $this->get('/')->assertOk()->assertSee('BYTE')->assertSee(route('login'));
  $this->get('/about')->assertOk();
  $this->seed(PosSeeder::class); $this->actingAs(User::firstOrFail());
  foreach (['/dashboard','/customers','/users'] as $url) {
   $this->get($url)->assertOk()->assertSee(route('customers.index'))->assertSee(route('users.index'));
  }
 }
 public function test_seed_preserves_edits_and_does_not_duplicate(): void {
  $this->seed(PosSeeder::class);
  Customer::where('email','alex@example.test')->update(['full_name'=>'Edited Customer']);
  $this->seed(PosSeeder::class);
  $this->assertDatabaseCount('customers',5); $this->assertDatabaseCount('users',5);
  $this->assertDatabaseHas('customers',['full_name'=>'Edited Customer']);
 }
 public function test_database_records_display_and_empty_customers_render(): void {
  $this->actingAs(User::factory()->create());
  $this->get('/customers')->assertOk()->assertSee('No accounts available.');
  $c=Customer::create(['full_name'=>'New Guest','email'=>'guest@example.test','phone'=>'123']);
  $this->get('/customers')->assertSee('New Guest');
  $c->update(['full_name'=>'Updated Guest']);
  $this->get('/customers')->assertSee('Updated Guest')->assertDontSee('New Guest');
 }
}
