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
   'avatar'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:2048','dimensions:max_width=4096,max_height=4096'],
  ]);
 }
 public function store(Request $request) {
  $data=$this->validated($request);
  unset($data['avatar']);
  $data['name']=$data['full_name'];
  $data['email']=Str::uuid().'@pos.example.test';
  $data['password']=Hash::make(Str::random(48));
  User::create($data);
  return redirect()->route('users')->with('status','User created. You can add an avatar using Edit.');
 }
 public function update(Request $request, User $user, AvatarService $avatars) {
  $data=$this->validated($request,$user);
  unset($data['avatar']);
  $data['name']=$data['full_name'];
  $newPath=null; $oldPath=$user->avatar;
  if ($request->hasFile('avatar')) { $newPath=$avatars->store($request->file('avatar')); $data['avatar']=$newPath; }
  try { $user->update($data); }
  catch (\Throwable $e) { if ($newPath) { Storage::disk('public')->delete($newPath); } throw $e; }
  if ($newPath && $oldPath && preg_match('/^avatars\/[a-f0-9-]+\.jpg$/D',$oldPath)) {
   try { Storage::disk('public')->delete($oldPath); } catch (\Throwable $e) { report($e); }
  }
  return redirect()->route('users')->with('status','User updated.');
 }
 public function avatar(string $filename) {
  $disk=Storage::disk('public'); $path='avatars/'.$filename;
  if (!$disk->exists($path)) { return response()->file(public_path('images/avatar-placeholder.svg')); }
  return $disk->response($path,null,['Content-Type'=>'image/jpeg','X-Content-Type-Options'=>'nosniff','Cache-Control'=>'public, max-age=86400']);
 }
}
