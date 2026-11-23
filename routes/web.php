<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PageController,CustomerController,UserController,ProductController,SaleController};
use App\Http\Middleware\EnsureStaff;
Route::get('/',[PageController::class,'index'])->name('home');
Route::get('/about',[PageController::class,'about'])->name('about');
Route::middleware(['auth',EnsureStaff::class])->group(function(){
 Route::view('/dashboard','dashboard')->name('dashboard');
 Route::resource('products',ProductController::class);
 Route::resource('customers',CustomerController::class);
 Route::resource('users',UserController::class)->middleware('can:manage-staff');
 Route::get('/avatars/{filename}',[UserController::class,'avatar'])->where('filename','[a-f0-9-]+\.jpg')->name('avatars.show');
 Route::get('/product-images/{filename}',[ProductController::class,'image'])->where('filename','[a-f0-9-]+\.jpg')->name('product-images.show');
 Route::resource('sales',SaleController::class)->only(['index','create','store']);
});
require __DIR__.'/auth.php';
