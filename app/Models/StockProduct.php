<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    use HasFactory;

    protected $table = 'stock_product';
    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'pd_sp_id',
        'option_id',
        'quantity',
    ];

    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_sp_id', 'pd_sp_id');
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class, 'option_id', 'id');
    }
}
