<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutGalleryImage extends Model
{
    protected $fillable = [
        'about_gallery_id',
        'image_path',
    ];

    public function gallery()
    {
        return $this->belongsTo(AboutGallery::class, 'about_gallery_id');
    }
}
