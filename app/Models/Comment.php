<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'comment_name',
        'comment_date',
        'comment',
        'parent_comment_id',
        'comment_status',
        'product_id',
        'customer_id'
    ];
    protected $primaryKey = 'comment_id';
    protected $table = 'tbl_comment';
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id')->orderBy('comment_id', 'asc');
    }
    public function rating()
    {
        return $this->hasOne(Rating::class, 'comment_id', 'comment_id');
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
