<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'image_path',
        'card_image_path',
        'link_url',
        'discount_code',
        'gift_product_id',
        'discount_value',
        'promotion_id',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'float',
    ];

    /**
     * Scope for active campaigns today.
     * Priority: Special (dated) campaigns first, then fallback (no dates) campaigns.
     */
    public function scopeActiveForToday($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                // 1. เฉพาะเจาะจงวันที่และเวลา
                $q->where(function ($sub) use ($now) {
                    $sub->whereNotNull('start_date')
                        ->where('start_date', '<=', $now)
                        ->where(fn($sq) => $sq->whereNull('end_date')->orWhere('end_date', '>=', $now));
                })
                // 2. หรือเป็นแคมเปญพื้นฐาน (ไม่มีวันที่)
                ->orWhere(function ($sub) {
                    $sub->whereNull('start_date')
                        ->whereNull('end_date');
                });
            })
            // เรียงลำดับเอาตัวที่มีวันทีก่อน (Priority สูงกว่า)
            ->orderByRaw('start_date IS NULL ASC')
            ->orderBy('start_date', 'desc');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function giftProduct()
    {
        return $this->belongsTo(ProductSalepage::class, 'gift_product_id', 'pd_sp_id');
    }
}
