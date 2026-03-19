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
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_value' => 'float',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function giftProduct()
    {
        return $this->belongsTo(ProductSalepage::class, 'gift_product_id', 'pd_sp_id');
    }
}
