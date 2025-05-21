<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'product_name',
        'product_quantity',
        'product_slug',
        'category_id',
        'brand_id',
        'product_desc',
        'product_tags',
        'product_sold',
        'product_content',
        'product_price',
        'product_price_in',
        'product_image',
        'product_status'

    ];
    protected $primaryKey = 'product_id';
    protected $table = 'tbl_product';
    public function comment()
    {
        return $this->hasMany(Comment::class, 'product_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'product_id');
    }
    public function getRouteKeyName()
    {
        return 'product_slug';
    }
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id');
    }
    public function getAverageRatingAttribute()
    {
        return round($this->ratings->avg('rating'), 1);
    }
    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class, 'product_id');
    }
    public function order_details()
    {
        return $this->hasMany(OrderDetails::class, 'product_id');
    }
}
