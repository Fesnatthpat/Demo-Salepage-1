<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    protected $table = 'product_options';

    protected $fillable = [
        'parent_id',
        'option_name',
        'option_price',
        'option_price2',
        'option_stock',
        'option_active',
    ];

    public $timestamps = true;

    // Relationship to the parent product
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'parent_id', 'pd_sp_id');
    }
}
