<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'envelope',
        'subject',
        'content',
        'created_at',
        'updated_at'

    ];
    protected $primaryKey = 'id';
    protected $table = 'tbl_promotional_content';
}
