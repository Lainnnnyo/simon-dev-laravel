<?php
use Illuminate\Support\Facades\{Artisan,Hash,Validator};
use App\Models\User;
Artisan::command('pos:create-admin {--sql : Generate an INSERT file for Wasmer instead of changing this database}',function(){
 $name=$this->ask('Full name');$username=$this->ask('New unique username');$email=$this->ask('New unique email (lowercase)');
 $password=$this->secret('Password (at least 12 characters)');$confirmation=$this->secret('Confirm password');
 $v=Validator::make(['full_name'=>$name,'username'=>$username,'email'=>$email,'password'=>$password,'password_confirmation'=>$confirmation],[
  'full_name'=>'required|string|max:255','username'=>'required|string|max:255','email'=>'required|email|lowercase|max:255',
  'password'=>['required','confirmed',\Illuminate\Validation\Rules\Password::min(12)],
 ]);
 if($v->fails()){foreach($v->errors()->all() as $error){$this->error($error);}return 1;}
 $data=['name'=>$name,'full_name'=>$name,'username'=>$username,'email'=>$email,'password'=>Hash::make($password),'is_staff'=>true,'is_admin'=>true];
 if($this->option('sql')){
  $hex=fn($s)=>"CONVERT(0x".bin2hex($s)." USING utf8mb4)";
  $sql='INSERT INTO users (name,full_name,username,email,password,is_staff,is_admin,created_at,updated_at) VALUES ('.implode(',',array_map($hex,[$name,$name,$username,$email,$data['password']])).',1,1,NOW(),NOW());';
  $dir=storage_path('app/private');if(!is_dir($dir)){mkdir($dir,0700,true);}
  $file=$dir.'/admin-bootstrap.sql';if(file_exists($file)){$this->error('admin-bootstrap.sql already exists. Move or remove it before generating another.');return 1;}
  file_put_contents($file,$sql.PHP_EOL);$this->info('SQL saved privately to '.$file.'. Import it in Wasmer Database Explorer once. Do not commit it.');return 0;
 }
 if(User::withTrashed()->where('email',$email)->orWhere('username',$username)->exists()){$this->error('Email or username already exists. Choose new values.');return 1;}
 User::create($data);$this->info('Administrator created. Login uses your email.');return 0;
})->purpose('Create the first administrator without public registration or a default password');
