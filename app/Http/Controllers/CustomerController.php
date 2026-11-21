<?php
namespace App\Http\Controllers;
use Illuminate\View\View;
class CustomerController extends Controller
{
    public function index(): View
    {
        // Temporary sample records required by TFA5; no database query.
        $customers = [
            ['id' => 1, 'full_name' => 'Alex Santos', 'email' => 'alex@example.test', 'phone' => '0900-000-0001'],
            ['id' => 2, 'full_name' => 'Jamie Reyes', 'email' => 'jamie@example.test', 'phone' => '0900-000-0002'],
            ['id' => 3, 'full_name' => 'Casey Cruz', 'email' => 'casey@example.test', 'phone' => '0900-000-0003'],
            ['id' => 4, 'full_name' => 'Morgan Garcia', 'email' => 'morgan@example.test', 'phone' => '0900-000-0004'],
            ['id' => 5, 'full_name' => 'Taylor Ramos', 'email' => 'taylor@example.test', 'phone' => '0900-000-0005'],
        ];
        return view('customers.index', compact('customers'));
    }
}
