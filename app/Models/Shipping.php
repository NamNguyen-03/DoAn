<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'customer_name',
        'order_id',
        'shipping_address',
        'shipping_phone',
        'shipping_email',
        'shipping_method',
        'shipping_note'
    ];
    protected $primaryKey = 'shipping_id';
    protected $table = 'tbl_shipping';
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
