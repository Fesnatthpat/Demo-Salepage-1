<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'condition_type',
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'usage_limit',
        'used_count',
        'is_discount_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'float',
        'min_order_value' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_discount_code' => 'boolean',
    ];

    /**
     * ความสัมพันธ์กับกฎเกณฑ์ (เงื่อนไขการซื้อ)
     */
    public function rules()
    {
        return $this->hasMany(PromotionRule::class);
    }

    /**
     * ความสัมพันธ์กับการกระทำ (ของแถม)
     */
    public function actions()
    {
        return $this->hasMany(PromotionAction::class);
    }
}
