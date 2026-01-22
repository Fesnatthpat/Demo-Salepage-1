<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    // 1. ระบุชื่อตารางให้ตรงกับใน Database
    protected $table = 'product_images';

    // 2. ระบุ Primary Key
    protected $primaryKey = 'img_id';

    // 3. ✅ ระบุฟิลด์ที่อนุญาตให้บันทึก (Fillable)
    // ใส่ img_path และ img_sort เพื่อให้ Create ข้อมูลได้
    protected $fillable = [
        'pd_sp_id',
        'img_path', 
        'img_sort'
    ];

    // 4. ✅ แก้ปัญหา Error 'Unknown column updated_at'
    // เนื่องจากตารางคุณมีแค่ created_at แต่ไม่มี updated_at
    // เราจึงต้องบอก Laravel ว่าไม่ต้องอัปเดต updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; 

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if (!$this->img_path) {
            return 'https://via.placeholder.com/150?text=No+Image';
        }

        if (filter_var($this->img_path, FILTER_VALIDATE_URL)) {
            return $this->img_path;
        }
        
        return asset('storage/' . ltrim($this->img_path, '/'));
    }

    // (ทางเลือก) หรือถ้าไม่อยากเก็บเวลาเลย ให้ใช้: public $timestamps = false;

    // ความสัมพันธ์ย้อนกลับไปหาสินค้า
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_sp_id', 'pd_sp_id');
    }
}