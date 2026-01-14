<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    // ระบุชื่อตารางให้ตรงกับใน Database (ตามที่เคยใช้ใน Query เดิม)
    protected $table = 'product_salepage';

    // ระบุ Primary Key (ถ้าไม่ใช่ id)
    protected $primaryKey = 'pd_sp_id'; // Corrected based on provided schema

    // ถ้าในตารางนี้ไม่มี column created_at, updated_at ให้ uncomment บรรทัดล่างนี้
    // public $timestamps = false;

    protected $guarded = [];

    protected $fillable = [
        'pd_id',
        'pd_code',
        'pd_sp_name',
        'pd_sp_price',
        'pd_sp_discount',
        'pd_sp_details',
        'pd_sp_active',
        'is_recommended',
        'pd_sp_display_location',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'pd_code', 'pd_code');
    }

    /**
     * Get the product images for the salepage.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'pd_sp_id');
    }
}
