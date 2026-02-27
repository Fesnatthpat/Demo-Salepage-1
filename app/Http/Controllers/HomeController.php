<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\ProductSalepage;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = ProductSalepage::with(['images', 'stock'])
            ->where('pd_sp_active', 1)
            ->where('is_recommended', 1)
            ->orderBy('pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        $settings = SiteSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => SiteSetting::get($setting->key)];
        })->toArray();

        return view('index', compact('recommendedProducts', 'settings'));
    }

    public function about()
    {
        // 1. ดึงข้อมูลหน้า "เกี่ยวกับติดใจ"
        $favorites = Favorite::when(! auth('admin')->check(), function ($query) {
            return $query->where('is_active', 1);
        })->orderBy('sort_order', 'asc')->get();

        // 2. ✅ ดึงข้อมูล Settings (พวกข้อความส่วนหัว และเบอร์โทร/อีเมล)
        $settings = SiteSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => SiteSetting::get($setting->key)];
        })->toArray();

        // ส่งทั้ง 2 ตัวแปรไปที่หน้า View
        return view('about', compact('favorites', 'settings'));
    }
}
