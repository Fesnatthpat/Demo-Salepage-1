<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutSocialLink extends Model
{
    protected $fillable = [
        'title',
        'url',
        'image_path',
        'icon_class',
        'icon_color',
        'bg_color',
        'is_active',
        'sort_order',
    ];
}
