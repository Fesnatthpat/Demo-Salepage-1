<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalepage extends Model
{
    use HasFactory;

    // ระบุชื่อตารางให้ตรงกับใน Database (ตามที่เคยใช้ใน Query เดิม)
    protected $table = 'product_salepage';

    // ระบุ Primary Key (ถ้าไม่ใช่ id)
    // protected $primaryKey = 'id';

    // ถ้าในตารางนี้ไม่มี column created_at, updated_at ให้ uncomment บรรทัดล่างนี้
    // public $timestamps = false;

    protected $guarded = [];
}
