<?php
namespace App\Services;
use App\Models\{Product,Sale,User,Customer};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class SaleService {
 public function record(array $data,User $actor): Sale {
  return DB::transaction(function() use($data,$actor){
   // Consistent lock order: staff, product, then optional customer.
   $staff=User::whereKey($actor->id)->lockForUpdate()->first();
   abort_unless($staff && $staff->is_staff,403);
   $product=Product::whereKey($data['product_id'])->lockForUpdate()->firstOrFail();
   // Repeated POSTs with the same key return the original sale without another deduction.
   $existing=Sale::where('request_key',$data['request_key'])->first();
   if($existing){
    if($existing->sold_by!==$staff->id || $existing->product_id!==$product->id || $existing->quantity!==(int)$data['quantity'] || $existing->customer_id!=($data['customer_id']??null)){
     throw ValidationException::withMessages(['quantity'=>'This submission key was already used. Open a new sale form.']);
    }
    return $existing;
   }
   if($product->is_archived){throw ValidationException::withMessages(['product_id'=>'This product is archived.']);}
   $quantity=(int)$data['quantity'];
   if($quantity<1 || $quantity>10000 || $quantity>$product->stock_quantity){throw ValidationException::withMessages(['quantity'=>'Insufficient stock or invalid quantity. Available: '.$product->stock_quantity]);}
   $customer=null;
   if(!empty($data['customer_id'])){
    $customer=Customer::whereKey($data['customer_id'])->lockForUpdate()->first();
    if(!$customer){throw ValidationException::withMessages(['customer_id'=>'This customer is no longer available.']);}
   }
   $total=Money::decimal(Money::cents($product->price)*$quantity);
   $product->decrement('stock_quantity',$quantity);
   return Sale::create([
    'product_id'=>$product->id,'customer_id'=>$customer?->id,'sold_by'=>$staff->id,'quantity'=>$quantity,
    'unit_price'=>$product->price,'total_price'=>$total,'product_name'=>$product->name,
    'customer_name'=>$customer?->full_name,'staff_name'=>$staff->full_name ?: $staff->name,'request_key'=>$data['request_key'],
   ]);
  },5);
 }
}
