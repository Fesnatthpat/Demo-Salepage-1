<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    protected $table = 'product_salepage';

    protected $primaryKey = 'pd_sp_id';

    // ✅ เพิ่มฟิลด์ทั้งหมดที่สามารถบันทึกข้อมูลได้
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

    // ✅ เพิ่ม 'display_price' เข้าไปเพื่อให้เรียกใช้งานในหน้า Blade ได้ง่ายๆ (เช่น {{ $product->display_price }})
    protected $appends = ['cover_image_url', 'pd_sp_stock', 'display_price'];

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

    // ✅ Logic ดึงราคาสำหรับแสดงผล (อิงตามตัวเลือกสินค้า)
    public function getDisplayPriceAttribute()
    {
        // ถ้าสินค้ามีตัวเลือก (Options)
        if ($this->options()->exists()) {
            // ดึงราคาน้อยสุด และ มากสุด
            $minPrice = $this->options()->min('option_price');
            $maxPrice = $this->options()->max('option_price');

            // ถ้าราคาไม่เท่ากัน ให้แสดงช่วงราคา (เช่น 199-299)
            if ($minPrice != $maxPrice) {
                return (int)$minPrice . '-' . (int)$maxPrice;
            }

            // ถ้าราคาเท่ากันทุกตัวเลือก ให้แสดงราคาเดียว
            return (int)$minPrice;
        }

        // ถ้าสินค้า "ไม่มี" ตัวเลือก ให้ดึงราคาจากตารางหลักมาแสดง
        return (int)$this->pd_sp_price;
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

    // ความสัมพันธ์ดึงข้อมูลสต็อกของ "สินค้าหลัก" (กรณีที่ไม่มีตัวเลือก)
    public function stock()
    {
        return $this->hasOne(StockProduct::class, 'pd_sp_id', 'pd_sp_id')->whereNull('option_id');
    }

    // ✅ Logic ดึงจำนวนสต็อกที่ถูกต้อง
    public function getPdSpStockAttribute()
    {
        // เช็คว่าสินค้านี้มีตัวเลือกย่อยหรือไม่
        if ($this->options()->exists()) {
            // ถ้า "มี" ตัวเลือก: ให้หาผลรวมสต็อกเฉพาะของตัวเลือกย่อยทั้งหมด (ข้ามสต็อกของสินค้าหลัก)
            return (int) \App\Models\StockProduct::where('pd_sp_id', $this->pd_sp_id)
                ->whereNotNull('option_id')
                ->sum('quantity');
        }

        // ถ้า "ไม่มี" ตัวเลือก: ให้ดึงสต็อกของสินค้าหลักมาแสดง
        return (int) ($this->stock ? $this->stock->quantity : 0);
    }

    public function reviewImages()
    {
        return $this->hasMany(ProductReviewImage::class, 'product_salepage_id', 'pd_sp_id')->orderBy('sort_order', 'asc');
    }
}
