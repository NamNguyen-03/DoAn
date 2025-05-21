<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'rating',
        'product_id',
        'comment_id'

    ];
    protected $primaryKey = 'rating_id';
    protected $table = 'tbl_rating';
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'comment_id');
    }
}
