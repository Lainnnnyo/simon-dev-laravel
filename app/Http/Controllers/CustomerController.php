<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class CustomerController extends Controller {
 public function index() { return view('customers.index', ['customers'=>Customer::orderBy('id')->get()]); }
 public function create() { return view('customers.form', ['customer'=>new Customer]); }
 public function edit(Customer $customer) { return view('customers.form', compact('customer')); }
 private function validated(Request $request, ?Customer $customer = null): array {
  return $request->validate([
   'full_name'=>['required','string','max:255'],
   'email'=>['required','email','max:255',Rule::unique('customers','email')->ignore($customer)],
   'phone'=>['required','string','max:30'],
  ]);
 }
 public function store(Request $request) { Customer::create($this->validated($request)); return redirect()->route('customers.index')->with('status','Customer created.'); }
 public function update(Request $request, Customer $customer) { $customer->update($this->validated($request,$customer)); return redirect()->route('customers.index')->with('status','Customer updated.'); }
 public function show(Customer $customer) { return view('customers.show', compact('customer')); }
 public function destroy(Customer $customer) {
  $customer->delete();
  return redirect()->route('customers.index')->with('status', 'Customer deleted.');
 }
}
