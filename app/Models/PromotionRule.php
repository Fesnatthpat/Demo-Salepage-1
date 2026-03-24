<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PromotionRule extends Model
{
    protected $fillable = ['promotion_id', 'type', 'rules'];

    protected $casts = [
        'rules' => 'array',
    ];

    /**
     * Accessors: ช่วยดึงค่า product_id จาก JSON
     */
    protected function productId(): Attribute
    {
        return Attribute::get(fn () => $this->rules['product_id'] ?? null);
    }

    /**
     * Accessors: ช่วยดึงค่า quantity จาก JSON
     */
    protected function quantity(): Attribute
    {
        return Attribute::get(fn () => $this->rules['quantity_to_buy'] ?? 0);
    }

    // Relation: เชื่อมไปยังสินค้า
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'rules->product_id', 'pd_sp_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
