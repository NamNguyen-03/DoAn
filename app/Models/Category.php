<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Category extends Model
// {
//     public $timestamps = false;
//     protected $fillable = [
//         'category_name',
//         'category_slug',
//         'category_desc',
//         'category_parent',
//         'category_status'
//     ];
//     protected $primaryKey = 'category_id';
//     protected $table = 'tbl_category';
//     public function products()
//     {
//         return $this->hasMany(Product::class, 'category_id', 'category_id');
//     }
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category_name',
        'category_slug',
        'category_desc',
        'category_parent',
        'category_status',
        'category_order'
    ];

    protected $primaryKey = 'category_id';
    protected $table = 'tbl_category';

    // Quan hệ đến danh mục cha
    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_parent', 'category_id');
    }

    // Quan hệ đến các danh mục con
    public function children()
    {
        return $this->hasMany(Category::class, 'category_parent', 'category_id');
    }

    // Quan hệ với sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
    public function getRouteKeyName()
    {
        return 'category_slug';
    }
}
