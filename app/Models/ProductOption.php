<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $table = 'product_options';

    protected $primaryKey = 'option_id';

    // ✅ เพิ่ม options_img_id ลงในนี้แล้ว เพื่อให้ Laravel ยอมให้บันทึกข้อมูล
    protected $fillable = [
        'parent_id',
        'option_name',
        'option_SKU',
        'option_price',
        'option_price2',
        'options_img_id',
        'option_active',
    ];

    protected $appends = ['option_stock', 'option_image_url'];

    public $timestamps = true;

    // Relationship to the parent product
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'parent_id', 'pd_sp_id');
    }

    // Relationship to the stock
    public function stock()
    {
        return $this->hasOne(StockProduct::class, 'option_id', 'option_id');
    }

    // Attribute สำหรับดึงจำนวน Stock
    protected function optionStock(): Attribute
    {
        return Attribute::get(fn (): int => $this->stock ? (int) $this->stock->quantity : 0);
    }

    // Attribute สำหรับจัดการ URL ของรูปภาพ
    protected function optionImageUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->image && $this->image->img_path) {
                $rawPath = $this->image->img_path;

                return filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('storage/'.ltrim($rawPath, '/'));
            }

            // ถ้าไม่มีรูปประจำ Option ให้ใช้รูปหลักของสินค้าแทน
            return $this->product ? $this->product->cover_image_url : null;
        });
    }

    // Attribute สำหรับคำนวณราคาสุทธิ
    protected function finalPrice(): Attribute
    {
        return Attribute::get(function (): float {
            // The 'product' relationship should be loaded before calling this.
            $parentDiscount = $this->product ? (float) $this->product->pd_sp_discount : 0;

            return max(0, (float) $this->option_price - $parentDiscount);
        });
    }
}