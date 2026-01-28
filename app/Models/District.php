<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    protected $fillable = ['amphure_id', 'name', 'zip_code'];

    public $timestamps = false; // ปรับตามตารางจริง
}
