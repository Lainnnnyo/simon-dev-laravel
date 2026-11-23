<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class EnsureStaff {
 public function handle(Request $request,Closure $next){
  if (!$request->user()?->is_staff) {
   Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();
   return redirect()->route('login')->withErrors(['email'=>'Staff access is required. Ask your administrator for an account.']);
  }
  return $next($request);
 }
}
