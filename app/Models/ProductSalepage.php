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
        'is_bogo_active',
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

    // ในไฟล์ app/Models/ProductSalepage.php

    public function options()
    {
        return $this->belongsToMany(
            ProductSalepage::class,
            'product_salepage_options', // ชื่อตารางกลางที่เราเพิ่งสร้าง
            'product_salepage_id',      // Foreign Key ของตัวตั้งต้น
            'option_product_salepage_id' // Foreign Key ของตัวเลือก
        );
    }

    /**
     * The parent products that this product is an option for.
     */
    public function parentProducts()
    {
        return $this->belongsToMany(
            ProductSalepage::class,
            'product_salepage_options',
            'option_product_salepage_id',
            'product_salepage_id'
        );
    }

    /**
     * The eligible free items for this product's BOGO promotion.
     */
    public function bogoFreeOptions()
    {
        return $this->belongsToMany(
            ProductSalepage::class,
            'bogo_promotion_options',
            'product_salepage_id',
            'free_option_product_id'
        );
    }
}
