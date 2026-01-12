<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_detail'; // ชื่อตารางใน DB (ไม่มี s)
    protected $primaryKey = 'ordd_id'; // ระบุ PK ให้ตรง
    protected $guarded = [];

    /**
     * Get the product associated with the order detail.
     */
    public function productSalepage()
    {
        return $this->belongsTo(ProductSalepage::class, 'pd_id', 'pd_sp_id');
    }
}