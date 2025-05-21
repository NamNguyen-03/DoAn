<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCategory extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'cate_post_name',
        'cate_post_slug',
        'cate_post_desc',
        'cate_post_status'
    ];
    protected $primaryKey = 'cate_post_id';
    protected $table = 'tbl_category_post';
    public function posts()
    {
        return $this->hasMany(Post::class, 'cate_post_id');
    }
    public function getRouteKeyName()
    {
        return 'cate_post_slug';
    }
}
