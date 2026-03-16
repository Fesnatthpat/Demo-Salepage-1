<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutGallery extends Model
{
    protected $fillable = [
        'title',
        'is_active',
        'sort_order',
    ];

    public function images()
    {
        return $this->hasMany(AboutGalleryImage::class);
    }
}
