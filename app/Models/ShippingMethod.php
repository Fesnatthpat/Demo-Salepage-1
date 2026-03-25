<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active',
        'is_default',
        'bkk_rate',
        'upc_rate',
        'per_item_rate',
        'free_threshold',
        'min_items_for_free_shipping',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'bkk_rate' => 'float',
        'upc_rate' => 'float',
        'per_item_rate' => 'float',
        'free_threshold' => 'float',
        'min_items_for_free_shipping' => 'integer',
        'sort_order' => 'integer'
    ];
}
