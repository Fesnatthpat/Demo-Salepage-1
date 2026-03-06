<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'location', 'title', 'description', 'image_path', 'link_url', 'sort_order', 'is_active'
    ];

    // Scopes for easy retrieval in Controller
    public function scopeActive(Builder $query) {
        return $query->where('is_active', true);
    }

    public function scopeLocation(Builder $query, $location = 'homepage') {
        return $query->where('location', $location);
    }

    public function scopeHero(Builder $query) {
        return $query->where('type', 'hero')->orderBy('sort_order');
    }

    public function scopeSecondary(Builder $query) {
        return $query->where('type', 'secondary')->orderBy('sort_order');
    }

    public function scopeInfo(Builder $query) {
        return $query->where('type', 'info')->orderBy('sort_order');
    }
}
