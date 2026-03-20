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
        $currentPath = \Request::path() == '/' ? 'home' : \Request::path();
        
        $activePopups = \App\Models\HomepagePopup::activeForToday()
            ->where(function($query) use ($currentRoute, $currentPath) {
                // แสดงทุกหน้า (display_pages เป็น null)
                $query->whereNull('display_pages')
                      // หรือมีชื่อ route ปัจจุบันใน display_pages (SQL JSON)
                      ->orWhereJsonContains('display_pages', $currentRoute)
                      // หรือมี path ปัจจุบันใน display_pages (SQL JSON)
                      ->orWhereJsonContains('display_pages', $currentPath);
            })
            ->orderBy('sort_order', 'asc')
            ->get();

        // กรองหน้าสินค้าเฉพาะอัน (product.show) เพิ่มเติมในระดับ PHP
        if ($currentRoute === 'product.show') {
            $activePopups = $activePopups->filter(function($popup) use ($view) {
                if (empty($popup->display_pages) || !in_array('product.show', $popup->display_pages)) {
                    return true;
                }
                
                $viewData = $view->getData();
                $currentProductId = null;
                
                if (isset($viewData['product'])) {
                    $currentProductId = $viewData['product']->pd_sp_id ?? $viewData['product']->id;
                } elseif (\Route::current()) {
                    $currentProductId = \Route::current()->parameter('id');
                }

                if ($popup->product_id) {
                    return $popup->product_id == $currentProductId;
                }
                return true;
            });
        }

        // Debug Log (ดูผลลัพธ์ใน storage/logs/laravel.log)
        \Log::info("Popup Final Check:", [
            'route' => $currentRoute,
            'path' => $currentPath,
            'active_popups_count' => $activePopups->count(),
            'found_ids' => $activePopups->pluck('id')->toArray(),
            'raw_query' => \App\Models\HomepagePopup::activeForToday()
                ->where(function($query) use ($currentRoute, $currentPath) {
                    $query->whereNull('display_pages')
                          ->orWhereJsonContains('display_pages', $currentRoute)
                          ->orWhereJsonContains('display_pages', $currentPath);
                })->toSql()
        ]);

        $view->with('settings', $settings)
             ->with('activePopups', $activePopups);    }
}
