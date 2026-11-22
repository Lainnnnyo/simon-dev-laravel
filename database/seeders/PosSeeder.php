<?php
namespace Database\Seeders;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class PosSeeder extends Seeder
{
    public function run(): void {
        DB::transaction(function () {
            $customers = [
                ['full_name' => 'Alex Santos', 'email' => 'alex@example.test', 'phone' => '0900-000-0001'],
                ['full_name' => 'Jamie Reyes', 'email' => 'jamie@example.test', 'phone' => '0900-000-0002'],
                ['full_name' => 'Casey Cruz', 'email' => 'casey@example.test', 'phone' => '0900-000-0003'],
                ['full_name' => 'Morgan Garcia', 'email' => 'morgan@example.test', 'phone' => '0900-000-0004'],
                ['full_name' => 'Taylor Ramos', 'email' => 'taylor@example.test', 'phone' => '0900-000-0005'],
            ];
            foreach ($customers as $customer) {
                Customer::firstOrCreate(['email' => $customer['email']], $customer);
            }
            $users = [
                ['username' => 'admin01', 'full_name' => 'Admin Demo'],
                ['username' => 'cashier01', 'full_name' => 'Cashier One'],
                ['username' => 'cashier02', 'full_name' => 'Cashier Two'],
                ['username' => 'manager01', 'full_name' => 'Manager Demo'],
                ['username' => 'staff01', 'full_name' => 'Staff Demo'],
            ];
            foreach ($users as $user) {
                // Supply Laravel's retained authentication fields, without a shared password.
                User::firstOrCreate(['username' => $user['username']], [
                    'full_name' => $user['full_name'],
                    'name' => $user['full_name'],
                    'email' => $user['username'].'@pos.example.test',
                    'password' => Hash::make(Str::random(48)),
                ]);
            }
        });
    }
}
