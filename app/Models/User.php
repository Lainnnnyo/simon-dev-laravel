<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable, \Illuminate\Database\Eloquent\SoftDeletes;
    protected $fillable = ['username', 'full_name', 'name', 'email', 'password', 'avatar', 'is_staff', 'is_admin'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_staff'=>'boolean', 'is_admin'=>'boolean'];
    }
    public function sales(){return $this->hasMany(Sale::class,'sold_by');}
}
