<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'image_product'; // Specify the table name
    protected $primaryKey = 'img_pd_id'; // Specify the primary key
    
    protected $fillable = [
        'product_id',
        'image_name',
        'image_path',
        'image_alt',
        'image_size',
        'image_type',
        'is_primary',
        'sort_order',
        'storage',
    ];

    /**
     * Get the product salepage that owns the image.
     */
    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'product_id', 'pd_sp_id');
    }
}
