<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'customer_id'
    ];
    protected $primaryKey = 'wishlist_id';
    protected $table = 'tbl_wishlist';
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
