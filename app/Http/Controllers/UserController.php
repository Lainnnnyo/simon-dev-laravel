<?php
namespace App\Http\Controllers;
use Illuminate\View\View;
class UserController extends Controller
{
    public function index(): View
    {
        // Temporary sample records required by TFA5; no database query.
        $users = [
            ['id' => 1, 'full_name' => 'Admin Demo', 'username' => 'admin01', 'role' => 'Administrator'],
            ['id' => 2, 'full_name' => 'Cashier One', 'username' => 'cashier01', 'role' => 'Cashier'],
            ['id' => 3, 'full_name' => 'Cashier Two', 'username' => 'cashier02', 'role' => 'Cashier'],
            ['id' => 4, 'full_name' => 'Manager Demo', 'username' => 'manager01', 'role' => 'Manager'],
            ['id' => 5, 'full_name' => 'Staff Demo', 'username' => 'staff01', 'role' => 'Staff'],
        ];
        return view('users.index', compact('users'));
    }
}
