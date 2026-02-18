<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_detail';

    // กำหนด Primary Key ถูกต้องแล้ว
    protected $primaryKey = 'ordd_id';

    protected $fillable = [
        'ord_id',
        'pd_id',
        'option_name',
        'ordd_price',
        'ordd_original_price',
        'ordd_count',
        'ordd_discount',
        'ordd_create_date',
    ];

    // ✅ แก้เป็น false ป้องกัน Laravel บังคับหาคอลัมน์ created_at และ updated_at
    public $timestamps = false;

    // Relation ไปหาสินค้า
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }

    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }

    // Relation ไปหาบิลหลัก
    public function order()
    {
        // ✅ เปลี่ยนตัวแปรที่ 3 เป็น 'id' ซึ่งเป็น Primary Key ของตาราง orders
        return $this->belongsTo(Order::class, 'ord_id', 'id');
    }
}
