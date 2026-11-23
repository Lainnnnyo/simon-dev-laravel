<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class UserController extends Controller {
 public function index() { return view('users.index',['users'=>User::orderBy('id')->get()]); }
 public function create() { return view('users.form',['user'=>new User]); }
 public function edit(User $user) { return view('users.form',compact('user')); }
 private function validated(Request $request, ?User $user = null): array {
  return $request->validate([
   'username'=>['required','string','max:255',Rule::unique('users','username')->ignore($user)],
   'full_name'=>['required','string','max:255'],
   'email'=>['required','email','lowercase','max:255',Rule::unique('users','email')->ignore($user)],
   'password'=>[$user?'nullable':'required','confirmed',\Illuminate\Validation\Rules\Password::min(12)],
   'is_staff'=>['required','boolean'],'is_admin'=>['required','boolean'],
   'avatar'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048','dimensions:max_width=4096,max_height=4096'],
  ]);
 }
 public function store(Request $request) {
  $data=$this->validated($request);
  unset($data['avatar']);
  $data['name']=$data['full_name'];
  $data['password']=Hash::make($data['password']);
  User::create($data);
  return redirect()->route('users.index')->with('status','User created. You can add an avatar using Edit.');
 }
 public function update(Request $request, User $user, AvatarService $avatars) {
  $data=$this->validated($request,$user);
  unset($data['avatar']);
  $data['name']=$data['full_name'];
  if($request->user()->is($user) && (!$data['is_staff'] || !$data['is_admin'])){return back()->withErrors(['is_admin'=>'You cannot remove your own staff/admin access.'])->withInput($request->except(['password','password_confirmation']));}
  if(empty($data['password'])){unset($data['password']);}else{$data['password']=Hash::make($data['password']);$data['remember_token']=null;}
  $newPath=null; $oldPath=$user->avatar;
  if ($request->hasFile('avatar')) { $newPath=$avatars->store($request->file('avatar')); $data['avatar']=$newPath; }
  try { $user->update($data); }
  catch (\Throwable $e) { if ($newPath) { Storage::disk('public')->delete($newPath); } throw $e; }
  if ($newPath && $oldPath && preg_match('/^avatars\/[a-f0-9-]+\.jpg$/D',$oldPath)) {
   try { Storage::disk('public')->delete($oldPath); } catch (\Throwable $e) { report($e); }
  }
  return redirect()->route('users.index')->with('status','User updated.');
 }
 public function avatar(string $filename) {
  $disk=Storage::disk('public'); $path='avatars/'.$filename;
  if (!$disk->exists($path)) { return response()->file(public_path('images/avatar-placeholder.svg')); }
  return $disk->response($path,null,['Content-Type'=>'image/jpeg','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'private, no-store']);
 }
 public function show(User $user) { return view('users.show', compact('user')); }
 public function destroy(Request $request, User $user) {
  if ($request->user()->is($user)) {
   return back()->withErrors(['delete'=>'You cannot delete your own signed-in account.']);
  }
  $avatar=$user->avatar;
  \Illuminate\Support\Facades\DB::transaction(function()use($user){
   $locked=User::whereKey($user->id)->lockForUpdate()->firstOrFail();
   $locked->update(['is_staff'=>false,'remember_token'=>null,'avatar'=>null]);
   $locked->delete();
  });
  if ($avatar && preg_match('/^avatars\/[a-f0-9-]+\.jpg$/D', $avatar)) {
   try {
    if (!Storage::disk('public')->delete($avatar)) {
     return redirect()->route('users.index')->with('status', 'User deleted. The avatar file could not be removed; check storage permissions.');
    }
   } catch (\Throwable $e) {
    report($e);
    return redirect()->route('users.index')->with('status', 'User deleted. The avatar file could not be removed; check storage permissions.');
   }
  }
  return redirect()->route('users.index')->with('status', 'User and avatar deleted.');
 }
}
