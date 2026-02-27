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
        'option_price',
        'option_price2',
        'option_active',
    ];

    protected $appends = ['option_stock'];

    public $timestamps = true;

    // Relationship to the parent product
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'parent_id', 'pd_sp_id');
    }

    public function stock()
    {
        return $this->hasOne(StockProduct::class, 'option_id', 'option_id');
    }

    public function getOptionStockAttribute()
    {
        return $this->stock ? $this->stock->quantity : 0;
    }

    public function getFinalPriceAttribute(): float
    {
        // The 'product' relationship should be loaded before calling this.
        $parentDiscount = $this->product ? (float) $this->product->pd_sp_discount : 0;
        
        return max(0, (float) $this->option_price - $parentDiscount);
    }
}
