<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PosSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PosPagesTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_pages_and_navigation(): void {
        $this->seed(PosSeeder::class);
        foreach (['/', '/about', '/customers', '/users'] as $path) {
            $response = $this->get($path)->assertOk();
            foreach (['home', 'about', 'customers', 'users'] as $name) {
                $response->assertSee(route($name));
            }
        }
    }
    public function test_seed_is_repeatable_and_preserves_existing_edits(): void {
        $this->seed(PosSeeder::class);
        Customer::where('email', 'alex@example.test')->update(['full_name' => 'Edited Customer']);
        $this->seed(PosSeeder::class);
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('customers', ['full_name' => 'Edited Customer']);
    }
    public function test_pages_read_new_and_updated_database_rows(): void {
        $this->seed(PosSeeder::class);
        $customer = Customer::create(['full_name' => 'Database Only Customer', 'email' => 'new@example.test', 'phone' => '0900-000-0099']);
        $this->get('/customers')->assertOk()->assertSee('Database Only Customer');
        $customer->update(['full_name' => 'Updated Database Customer']);
        $this->get('/customers')->assertSee('Updated Database Customer')->assertDontSee('Database Only Customer');
        User::where('username', 'admin01')->update(['full_name' => 'Updated Database User']);
        $this->get('/users')->assertOk()->assertSee('Updated Database User');
    }
    public function test_empty_account_tables_render(): void {
        $this->get('/customers')->assertOk()->assertSee('No accounts available.');
        $this->get('/users')->assertOk()->assertSee('No accounts available.');
    }
    public function test_database_enforces_unique_usernames(): void {
        $this->seed(PosSeeder::class);
        $this->expectException(QueryException::class);
        User::where('username', 'staff01')->update(['username' => 'admin01']);
    }
}
