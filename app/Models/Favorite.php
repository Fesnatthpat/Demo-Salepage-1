<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        // 'image_path', // Removed as images are now handled by FavoriteImage model
        'is_active',
        'sort_order',
    ];

    /**
     * Get the images for the favorite item.
     */
    public function images()
    {
        return $this->hasMany(FavoriteImage::class);
    }
}
