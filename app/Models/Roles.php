<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'role_id';
    protected $fillable = ['role_name'];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_role');
    }
}
