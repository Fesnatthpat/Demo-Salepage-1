<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tambon extends Model
{
    protected $table = 'tambons';

    protected $fillable = [
        'tambon',
        'amphoe',
        'province',
        'zipcode',
        'tambon_code',
        'amphoe_code',
        'province_code',
    ];
}
