<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_date',
        'quantity',
        'total_order',
        'sales',
        'profit'
    ];
    protected $primaryKey = 'id_statistic';
    protected $table = 'tbl_statistic';
}
