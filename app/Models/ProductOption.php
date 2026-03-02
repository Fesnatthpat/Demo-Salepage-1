<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $table = 'product_options';

    protected $primaryKey = 'option_id';

    protected $fillable = [
        'parent_id',
        'option_name',
        'option_SKU',
        'option_price',
        'option_price2',
        'option_active',
        'options_img_id',
    ];

    protected $appends = ['option_stock', 'option_image_url'];

    public $timestamps = true;

    // Relationship to the parent product
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'parent_id', 'pd_sp_id');
    }

    public function image()
    {
        return $this->belongsTo(ProductImage::class, 'options_img_id', 'img_id');
    }

    public function stock()
    {
        return $this->hasOne(StockProduct::class, 'option_id', 'option_id');
    }

    public function getOptionStockAttribute()
    {
        return $this->stock ? (int) $this->stock->quantity : 0;
    }

    public function getOptionImageUrlAttribute()
    {
        if ($this->image && $this->image->img_path) {
            $rawPath = $this->image->img_path;
            return filter_var($rawPath, FILTER_VALIDATE_URL) ? $rawPath : asset('storage/' . ltrim($rawPath, '/'));
        }
        
        // ถ้าไม่มีรูปประจำ Option ให้ใช้รูปหลักของสินค้าแทน
        return $this->product ? $this->product->cover_image_url : null;
    }

    public function getFinalPriceAttribute(): float
    {
        // The 'product' relationship should be loaded before calling this.
        $parentDiscount = $this->product ? (float) $this->product->pd_sp_discount : 0;
        
        return max(0, (float) $this->option_price - $parentDiscount);
    }
}
