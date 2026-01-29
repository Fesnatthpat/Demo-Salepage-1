<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes; // Add this line
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes; // Add SoftDeletes here

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'username', 'password', 'role', 'admin_code',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
