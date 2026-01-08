<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product'; // ตรวจสอบชื่อตารางให้ตรงกับ DB จริง

    protected $primaryKey = 'pd_id';

    protected $fillable = [
        'pd_name',
        'pd_code',
        'pd_details',
        'pd_price',
        'pd_full_price',
        'pd_status',
        'pd_img',
    ];

    // Relationship ไปยังตาราง SalePage (ส่วนลด)
    // หมายเหตุ: คุณควรสร้าง Model ชื่อ ProductSalepage หรือใช้ชื่อ Table ให้ถูกต้อง
    public function salePage()
    {
        // สมมติว่ามี Model ProductSalepage ถ้าไม่มีให้สร้าง หรือระบุ Table เอง
        return $this->hasOne(ProductSalepage::class, 'pd_code', 'pd_code')
            ->where('pd_sp_active', 1);
    }

    // Accessor: คำนวณราคาขายจริง (Real Price)
    public function getRealPriceAttribute()
    {
        $normalPrice = $this->pd_price;
        $discount = $this->salePage ? $this->salePage->pd_sp_discount : 0;

        return max(0, $normalPrice - $discount);
    }

    // Accessor: ดึงส่วนลด
    public function getDiscountAmountAttribute()
    {
        return $this->salePage ? $this->salePage->pd_sp_discount : 0;
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
