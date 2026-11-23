<?php
namespace App\Http\Controllers;
use App\Models\{Product,Customer,Sale};
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class SaleController extends Controller {
 public function index(){return view('sales.index',['sales'=>Sale::latest('id')->paginate(25)]);}
 public function create(){return view('sales.create',['products'=>Product::where('is_archived',false)->orderBy('name')->get(),'customers'=>Customer::orderBy('full_name')->get(),'requestKey'=>(string)Str::uuid()]);}
 public function store(Request $request,SaleService $service){
  $data=$request->validate([
   'product_id'=>['required','integer','exists:products,id'],
   'customer_id'=>['nullable','integer',Rule::exists('customers','id')->whereNull('deleted_at')],
   'quantity'=>['required','integer','min:1','max:10000'],'request_key'=>['required','uuid'],
  ]);
  $sale=$service->record($data,$request->user());
  return redirect()->route('sales.index')->with('status','Sale #'.$sale->id.' recorded. Total: PHP '.$sale->total_price.'. Stock updated.');
 }
}
