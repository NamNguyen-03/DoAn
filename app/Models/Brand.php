<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'brand_name',
        'brand_slug',
        'brand_desc',
        'brand_parent',
        'brand_status',
        'brand_order'
    ];
    protected $primaryKey = 'brand_id';
    protected $table = 'tbl_brand';

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }

    public function getRouteKeyName()
    {
        return 'brand_slug';
    }
}
