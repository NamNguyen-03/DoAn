<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'post_title',
        'post_slug',
        'post_desc',
        'post_content',
        'post_image',
        'cate_post_id',
        'post_status',
    ];
    protected $primaryKey = 'post_id';
    protected $table = 'tbl_post';
    public function cate_post()
    {
        return $this->belongsTo(PostCategory::class, 'cate_post_id');
    }
    public function getRouteKeyName()
    {
        return 'post_slug';
    }
}
