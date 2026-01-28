<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    protected $table = 'product_salepage';

    protected $primaryKey = 'pd_sp_id';

    protected $fillable = [
        'pd_sp_code',
        'pd_sp_name',
        'pd_sp_description',
        'pd_sp_price',
        'pd_sp_discount',
        'pd_sp_stock',
        'pd_sp_active',
        'pd_sp_display_location',
        'is_recommended',
        'is_bogo_active',
    ];

    protected $appends = ['cover_image_url'];

    // ACCESSOR for getting a clean cover image URL
    public function getCoverImageUrlAttribute()
    {
        $placeholder = 'https://via.placeholder.com/150?text=No+Image';

        if ($this->images->isEmpty()) {
            return $placeholder;
        }

        // The relationship is already ordered by 'img_sort', so the first item is the intended cover.
        $image = $this->images->first();

        if (! $image || ! $image->img_path) {
            return $placeholder;
        }

        $rawPath = $image->img_path;

        if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
            return $rawPath;
        }

        return asset('storage/'.ltrim($rawPath, '/'));
    }

    // 1. รูปภาพ
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'pd_sp_id', 'pd_sp_id')->orderBy('img_sort', 'asc');
    }

    // 2. ✅ แก้ไข: เปลี่ยนกลับเป็น belongsToMany (เพื่อให้ใช้ attach/sync ได้)
    public function options()
    {
        // เชื่อมตัวเอง (ProductSalepage) กับ ตัวเอง ผ่านตารางกลาง product_options
        return $this->belongsToMany(ProductSalepage::class, 'product_options', 'parent_id', 'child_id')
            ->withPivot('price_modifier');
    }

    // 3. ของแถม (BOGO)
    public function bogoFreeOptions()
    {
        return $this->belongsToMany(ProductSalepage::class, 'product_bogo_options', 'parent_id', 'child_id');
    }
}
