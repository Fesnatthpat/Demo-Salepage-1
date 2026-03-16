<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutVideo extends Model
{
    protected $fillable = [
        'title',
        'video_url',
        'thumbnail_path',
        'thumbnail_url',
        'embed_html',
        'duration',
        'is_active',
        'sort_order',
    ];
}
