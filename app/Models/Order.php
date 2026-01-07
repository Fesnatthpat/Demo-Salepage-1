<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'ord_id'; // ระบุ PK ให้ตรงกับตาราง orders

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'ord_id', 'ord_id');
    }

    /**
     * Get the formatted order date.
     *
     * @return string
     */
    public function getFormattedOrdDateAttribute()
    {
        return Carbon::parse($this->ord_date)->format('d/m/Y H:i');
    }
}