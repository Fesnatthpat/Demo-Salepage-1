<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReviewImage extends Model
{
    protected $fillable = [
        'image_url',
        'sort_order',
    ];
}
