<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// สั่งให้คำสั่งกวาดออเดอร์หมดเวลาทำงานทุกๆ 1 นาที
Schedule::command('orders:cancel-expired')->everyMinute();