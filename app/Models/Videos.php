<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'video_title',
        'video_slug',
        'video_desc',
        'video_link',
        'video_status',
        'video_thumb'

    ];
    protected $primaryKey = 'video_id';
    protected $table = 'tbl_videos';
    public function getRouteKeyName()
    {
        return 'video_slug';
    }
}
