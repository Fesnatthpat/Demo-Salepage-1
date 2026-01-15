<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $table = 'product_options';      // ชื่อตาราง
    protected $primaryKey = 'option_id';       // PK ของตารางนี้

    // ระบุฟิลด์ที่ให้บันทึกได้
    protected $fillable = [
        'pd_sp_id',
        'option_name',
        'option_price',
        'option_stock',
        'option_active'
    ];
    
    // ปิด Timestamp ถ้าในตารางไม่มี created_at, updated_at
    // แต่ถ้ามีแล้วก็ลบบรรทัดข้างล่างนี้ออกได้เลยครับ
    public $timestamps = false; 

    // เชื่อมกลับไปหาสินค้าหลัก
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_sp_id', 'pd_sp_id');
    }
}