<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens;
    public $timestamps = false;
    protected $fillable = [
        'admin_name',
        'admin_email',
        'admin_phone',
        'admin_password'
    ];
    protected $primaryKey = 'admin_id';
    protected $table = 'tbl_admin';
    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'admin_role', 'admin_admin_id', 'roles_id');
    }


    public function getAuthPassword()
    {
        return $this->admin_password;
    }

    public function hasRole($role)
    {
        return $this->roles()->where('role_name', $role)->exists();
    }
}
