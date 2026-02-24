<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;

class RestoreUserCart
{
    public function __construct()
    {
        //
    }

    public function handle(UserLoggedIn $event)
    {
        // Logic to restore cart for the user
    }
}
