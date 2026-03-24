<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    protected $table = 'product_salepage';

    protected $primaryKey = 'pd_sp_id';

    // ✅ เพิ่มฟิลด์ทั้งหมดที่สามารถบันทึกข้อมูลได้
    protected $fillable = [
        'category_id',
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

    protected function coverImageUrl(): Attribute
    {
        return Attribute::get(function () {
            // 1. ตรวจสอบข้อมูลรูปจาก collection ที่โหลดมาแล้ว (เพื่อลดการ Query ใหม่)
            $image = $this->relationLoaded('images')
                ? $this->images->sortBy('img_sort')->first()
                : $this->images()->orderBy('img_sort', 'asc')->first();

            if (! $image || ! $image->img_path) {
                $fallbackId = ($this->pd_sp_id % 8) + 1;
                return asset("images/pd-{$fallbackId}.png");
            }

            $rawPath = $image->img_path;

            // 2. ถ้าเป็น URL สมบูรณ์ ให้ใช้ทันที
            if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
                return $rawPath;
            }

            // 3. แสดงผลจาก Storage (ลดการใช้ file_exists บน Production เพื่อความเร็ว)
            return asset('storage/'.ltrim($rawPath, '/'));
        });
    }

    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn (): float => max(0, (float) $this->pd_sp_price - (float) $this->pd_sp_discount));
    }

    // ✅ Logic ดึงราคาสำหรับแสดงผล (Optimize: ใช้ Collection ถ้าโหลดมาแล้ว)
    protected function displayPrice(): Attribute
    {
        return Attribute::get(function () {
            if ($this->relationLoaded('options')) {
                if ($this->options->isEmpty()) {
                    return (int)$this->pd_sp_price;
                }

                $prices = $this->options->pluck('option_price');
                $minPrice = $prices->min();
                $maxPrice = $prices->max();

                return ($minPrice != $maxPrice) ? (int)$minPrice . '-' . (int)$maxPrice : (int)$minPrice;
            }

            // Fallback: กรณีไม่ได้ Eager Load มา (อาจเกิด N+1 ได้)
            if ($this->options()->exists()) {
                $minPrice = $this->options()->min('option_price');
                $maxPrice = $this->options()->max('option_price');

                return ($minPrice != $maxPrice) ? (int)$minPrice . '-' . (int)$maxPrice : (int)$minPrice;
            }

            return (int)$this->pd_sp_price;
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
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

    // ✅ Logic ดึงจำนวนสต็อกที่ถูกต้อง (Optimize: ใช้ Collection ถ้าโหลดมาแล้ว)
    protected function pdSpStock(): Attribute
    {
        return Attribute::get(function (): int {
            if ($this->relationLoaded('options') && $this->options->isNotEmpty()) {
                return (int) $this->options->sum('option_stock');
            }

            if ($this->relationLoaded('stock')) {
                return (int) ($this->stock ? $this->stock->quantity : 0);
            }

            // Fallback: Query (เพื่อความเข้ากันได้ย้อนหลัง แต่แนะนำให้ใช้ with() ใน Controller)
            if ($this->options()->exists()) {
                return (int) \App\Models\StockProduct::where('pd_sp_id', $this->pd_sp_id)
                    ->whereNotNull('option_id')
                    ->sum('quantity');
            }

            return (int) ($this->stock ? $this->stock->quantity : 0);
        });
    }

    public function reviewImages()
    {
        return $this->hasMany(ProductReviewImage::class, 'product_salepage_id', 'pd_sp_id')->orderBy('sort_order', 'asc');
    }
}
