<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'order_id',
        'user_id',
        'code_used',
        'discount_amount',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
