<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReviewImage extends Model
{
    protected $fillable = [
        'product_salepage_id',
        'image_url',
        'sort_order',
    ];
}
