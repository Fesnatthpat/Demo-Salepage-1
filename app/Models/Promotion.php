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
        'condition_type', // ✅ เพิ่มฟิลด์นี้
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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
