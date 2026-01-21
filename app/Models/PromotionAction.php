<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionAction extends Model
{
    protected $fillable = ['promotion_id', 'type', 'actions'];

    protected $casts = [
        'actions' => 'array',
    ];

    // --- Accessor: ช่วยดึง ID สินค้าของแถม ($action->product_id) ---
    public function getProductIdAttribute()
    {
        return $this->actions['product_id_to_get'] ?? null;
    }

    // --- Accessor: ช่วยดึงจำนวนของแถม ($action->quantity) ---
    public function getQuantityAttribute()
    {
        return $this->actions['quantity_to_get'] ?? 0;
    }

    // --- Relation: ดึงข้อมูลสินค้าของแถม (กรณีระบุเจาะจง) ---
    public function productToGet()
    {
        return $this->belongsTo(ProductSalepage::class, 'actions->product_id_to_get', 'pd_sp_id');
    }

    // --- Relation: กลับไปหาโปรโมชั่นแม่ ---
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * ความสัมพันธ์กับสินค้าของแถมที่เลือกได้ (Many-to-Many)
     * ใช้สำหรับกรณีที่ลูกค้าสามารถเลือกของแถมได้เองจากรายการที่กำหนด
     */
    public function giftableProducts()
    {
        return $this->belongsToMany(
            ProductSalepage::class,
            'promotion_action_gifts', // ชื่อตาราง Pivot
            'promotion_action_id',    // FK ของตารางนี้
            'product_salepage_id'     // FK ของตารางสินค้า
        );
    }
}
