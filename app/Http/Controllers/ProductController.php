<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Services\{ProductImageService,Money};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage,DB};
class ProductController extends Controller {
 public function index(){return view('products.index',['products'=>Product::orderBy('is_archived')->orderBy('name')->get()]);}
 public function create(){return view('products.form',['product'=>new Product]);}
 public function edit(Product $product){return view('products.form',compact('product'));}
 public function show(Product $product){return view('products.show',compact('product'));}
 private function validated(Request $request,?Product $product=null): array {
  return $request->validate([
   'name'=>['required','string','max:255'],
   'price'=>['required','regex:/^\d{1,8}(?:\.\d{1,2})?$/D','numeric','min:0.01','max:99999999.99'],
   'stock_quantity'=>['required','integer','min:0','max:1000000'],
   'image'=>[$product?'nullable':'required','image','mimes:jpg,jpeg,png,webp','max:2048','dimensions:max_width=4096,max_height=4096'],
   'is_archived'=>['sometimes','boolean'],
   'expected_stock'=>[$product?'required':'nullable','integer','min:0'],
  ]);
 }
 public function store(Request $request,ProductImageService $images){
  $data=$this->validated($request);unset($data['expected_stock']);
  $data['price']=Money::decimal(Money::cents((string)$data['price']));
  $path=$images->store($request->file('image'));$data['image']=$path;
  try{Product::create($data);}catch(\Throwable $e){Storage::disk('public')->delete($path);throw $e;}
  return redirect()->route('products.index')->with('status','Product created.');
 }
 public function update(Request $request,Product $product,ProductImageService $images){
  $data=$this->validated($request,$product);unset($data['image']);$data['price']=Money::decimal(Money::cents((string)$data['price']));
  $path=$request->hasFile('image')?$images->store($request->file('image')):null;$old=null;
  try{
   DB::transaction(function()use($product,$data,$path,&$old){
    $locked=Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
    if($locked->stock_quantity!==(int)$data['expected_stock']){throw \Illuminate\Validation\ValidationException::withMessages(['stock_quantity'=>'Stock changed since you opened this form. Reload before editing.']);}
    $old=$locked->image;unset($data['expected_stock']);if($path){$data['image']=$path;}
    $locked->update($data);
   });
  }catch(\Throwable $e){if($path){Storage::disk('public')->delete($path);}throw $e;}
  if($path && $old && preg_match('#^products/[a-f0-9-]+\.jpg$#D',$old)){try{Storage::disk('public')->delete($old);}catch(\Throwable $e){report($e);}}
  return redirect()->route('products.index')->with('status','Product updated.');
 }
 public function destroy(Product $product){$product->update(['is_archived'=>true]);return redirect()->route('products.index')->with('status','Product archived. Sales history was preserved.');}
 public function image(string $filename){return Storage::disk('public')->response('products/'.$filename,null,['Content-Type'=>'image/jpeg','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);}
}
