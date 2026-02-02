<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    protected $table = 'product_salepage';

    protected $primaryKey = 'pd_sp_id';

    // ✅ ต้องมีบรรทัดนี้เพื่อแก้ Error 1364 (Field doesn't have default value)
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

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'pd_sp_id', 'pd_sp_id')->orderBy('img_sort', 'asc');
    }

    // ✅ แก้ไข: เปลี่ยนเป็น hasMany เพื่อเชื่อมกับ ProductOption
    public function options()
    {
        return $this->hasMany(ProductOption::class, 'parent_id', 'pd_sp_id');
    }

    public function bogoFreeOptions()
    {
        return $this->belongsToMany(ProductSalepage::class, 'product_bogo_options', 'parent_id', 'child_id');
    }
}
