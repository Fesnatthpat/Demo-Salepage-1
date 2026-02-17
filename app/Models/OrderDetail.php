<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_detail';

    protected $primaryKey = 'ordd_id';

    protected $fillable = [
        'ord_id',
        'pd_id',
        'option_name',
        'ordd_price',
        'ordd_original_price',
        'ordd_count',
        'ordd_discount',
        'ordd_create_date',
    ];

    public $timestamps = true;

    // แก้ไขปัญหา Call to undefined relationship [product]
    public function product()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }

    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'ord_id', 'ord_id');
    }
}
