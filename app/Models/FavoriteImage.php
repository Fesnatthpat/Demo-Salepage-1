<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteImage extends Model
{
    protected $fillable = ['favorite_id', 'image_path'];

    public function favorite()
    {
        return $this->belongsTo(Favorite::class);
    }
}
