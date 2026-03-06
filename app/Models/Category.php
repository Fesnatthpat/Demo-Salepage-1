<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'icon', 'image_path', 'sort_order', 'is_active'
    ];

    public function products()
    {
        return $this->hasMany(ProductSalepage::class, 'category_id', 'pd_sp_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
