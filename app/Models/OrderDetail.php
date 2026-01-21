<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_detail';

    protected $primaryKey = 'ordd_id';

    // ✅ ปรับชื่อคอลัมน์ให้ตรงกับฐานข้อมูลในรูปภาพ 100%
    protected $fillable = [
        'ord_id',
        'pd_id',
        'ordd_price',           // ราคาขาย
        'ordd_original_price',  // ราคาเต็ม
        'ordd_count',           // จำนวน
        'ordd_discount',        // ส่วนลด
        'ordd_create_date',     // วันที่สร้าง
    ];

    public $timestamps = true; // ในรูปมี created_at, updated_at ดังนั้นต้องเป็น true

    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }
}
