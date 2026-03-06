<?php

namespace App\Http\View\Composers;

use App\Models\SiteSetting;
use Illuminate\View\View;

class SettingsComposer
{
    public function compose(View $view)
    {
        // ดึงข้อมูลจาก SiteSetting และจัดการ JSON Decoding
        $rawSettings = SiteSetting::all();
        $settings = [];
        
        foreach ($rawSettings as $s) {
            $value = $s->value;
            $decoded = json_decode($value, true);
            $settings[$s->key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }
        
        $view->with('settings', $settings);
    }
}
