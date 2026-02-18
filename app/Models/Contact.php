<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'title',
        'content',
        'address',
        'phone',
        'email',
        'map_url',
        'is_active',
        'sort_order',
    ];
}
