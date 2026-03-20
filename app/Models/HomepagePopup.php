<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepagePopup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'product_id',
        'link_url',
        'is_active',
        'start_date',
        'end_date',
        'display_type',
        'display_pages',
        'sort_order',
        'display_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'display_pages' => 'array',
    ];

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'product_id', 'pd_sp_id');
    }

    /**
     * Get the final URL for the popup
     */
    public function getFinalUrlAttribute()
    {
        if ($this->product_id && $this->product) {
            return route('product.show', $this->product_id);
        }
        return $this->link_url;
    }

    /**
     * Scope for active popups today.
     */
    public function scopeActiveForToday($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->where(function ($sub) use ($now) {
                    $sub->whereNotNull('start_date')
                        ->where('start_date', '<=', $now)
                        ->where(fn($sq) => $sq->whereNull('end_date')->orWhere('end_date', '>=', $now));
                })
                ->orWhere(function ($sub) {
                    $sub->whereNull('start_date')
                        ->whereNull('end_date');
                });
            })
            ->orderBy('created_at', 'desc');
    }
}
