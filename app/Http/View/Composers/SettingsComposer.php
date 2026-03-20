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

        // ดึง Popup ที่กำลังใช้งาน (Active และอยู่ในช่วงเวลา) กรองตามหน้าปัจจุบัน
        $currentRoute = \Route::currentRouteName();
        $activePopups = \App\Models\HomepagePopup::activeForToday()
            ->orderBy('sort_order', 'asc')
            ->get()
            ->filter(function($popup) use ($currentRoute) {
                // ถ้าไม่ระบุหน้าเลย ให้แสดงทุกหน้า
                if (empty($popup->display_pages)) {
                    return true;
                }
                // ถ้าเป็น array และมีชื่อ route ปัจจุบันอยู่
                return is_array($popup->display_pages) && in_array($currentRoute, $popup->display_pages);
            });

        $view->with('settings', $settings)
             ->with('activePopups', $activePopups);    }
}
