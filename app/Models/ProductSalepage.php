<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    protected $table = 'product_salepage';

    protected $primaryKey = 'pd_sp_id';

    // ✅ เพิ่มฟิลด์ใหม่ทั้งหมด (น้ำหนัก, ขนาด, จัดส่งฟรี) ป้องกัน Error
    protected $fillable = [
        'pd_sp_price2',
        'pd_sp_code',
        'pd_sp_SKU',
        'pd_sp_name',
        'pd_sp_description',
        'pd_sp_price',
        'pd_sp_discount',
        'pd_sp_active',
        'pd_sp_display_location',
        'is_recommended',
        'is_bogo_active',
        'pd_sp_weight',
        'pd_sp_width',
        'pd_sp_length',
        'pd_sp_height',
        'pd_sp_free_shipping',
        'pd_sp_free_cod',
    ];

    protected $appends = ['cover_image_url', 'pd_sp_stock'];

    public function getCoverImageUrlAttribute()
    {
        $placeholder = 'https://via.placeholder.com/150?text=No+Image';
        if ($this->images->isEmpty()) {
            return $placeholder;
        }

        $image = $this->images->sortBy('img_sort')->first();
        if (! $image || ! $image->img_path) {
            return $placeholder;
        }

        $rawPath = $image->img_path;

        return filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('storage/'.ltrim($rawPath, '/'));
    }

    public function getFinalPriceAttribute(): float
    {
        return max(0, (float) $this->pd_sp_price - (float) $this->pd_sp_discount);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'pd_sp_id', 'pd_sp_id')->orderBy('img_sort', 'asc');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class, 'parent_id', 'pd_sp_id');
    }

    public function bogoFreeOptions()
    {
        return $this->belongsToMany(ProductSalepage::class, 'product_bogo_options', 'parent_id', 'child_id');
    }

    // ความสัมพันธ์เชื่อมไปหาสต็อกของสินค้าหลัก
    public function stock()
    {
        return $this->hasOne(StockProduct::class, 'pd_sp_id', 'pd_sp_id')->whereNull('option_id');
    }

    // ✅ แก้ไข: Logic คืนค่าจำนวนสต็อกรวมทั้งหมด (แก้ปัญหาสินค้าที่มี Option โชว์ว่า 0 ชิ้น)
    public function getPdSpStockAttribute()
    {
        // เช็คว่าสินค้านี้มีตัวเลือกย่อยหรือไม่
        $hasOptions = $this->options()->exists();

        if ($hasOptions) {
            // ถ้ามีตัวเลือก: ให้หาผลรวมของสต็อกทุกตัวเลือกในตาราง stock_product
            return \App\Models\StockProduct::where('pd_sp_id', $this->pd_sp_id)
                ->whereNotNull('option_id')
                ->sum('quantity');
        }

        // ถ้าไม่มีตัวเลือก: ให้คืนค่าสต็อกของสินค้าหลัก
        return $this->stock ? $this->stock->quantity : 0;
    }

    public function reviewImages()
    {
        return $this->hasMany(ProductReviewImage::class, 'product_salepage_id', 'pd_sp_id')->orderBy('sort_order', 'asc');
    }
}