<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
    Route::get('/avatars/{filename}', [UserController::class, 'avatar'])
        ->where('filename', '[a-f0-9-]+\.jpg')->name('avatars.show');
});
require __DIR__.'/auth.php';
