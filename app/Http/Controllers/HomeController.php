<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\ProductSalepage;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = ProductSalepage::with(['images', 'stock', 'options', 'options.stock'])
            ->where('pd_sp_active', 1)
            ->where('is_recommended', 1)
            ->orderBy('pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        // ดึงโปรโมชั่นที่กำลังเปิดใช้งานอยู่ (สำหรับแสดงผลที่หน้าบ้าน)
        $now = now();
        $promotions = \App\Models\Promotion::with(['rules', 'actions.giftableProducts'])
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', $now))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $now))
            ->get();

        // ดึงข้อมูล CMS จากตารางใหม่
        $heroSlides = \App\Models\Banner::location('homepage')->hero()->active()->get();
        $infoBanner = \App\Models\Banner::location('homepage')->info()->active()->first();
        $secSlides = \App\Models\Banner::location('homepage')->secondary()->active()->get();
        $services = \App\Models\Service::where('is_active', true)->orderBy('sort_order')->get();
        $reasons = \App\Models\Feature::where('is_active', true)->orderBy('sort_order')->get();
        $reviewImages = \App\Models\ProductReviewImage::whereNull('product_salepage_id')->orderBy('sort_order', 'asc')->get();

        $settings = SiteSetting::all()->pluck('key')->mapWithKeys(function ($key) {
            return [$key => SiteSetting::get($key)];
        })->toArray();

        return view('index', compact(
            'recommendedProducts', 
            'promotions',
            'heroSlides', 
            'infoBanner', 
            'secSlides', 
            'services', 
            'reasons',
            'reviewImages',
            'settings'
        ));
    }

    public function about()
    {
        // 1. ดึงข้อมูลหน้า "เกี่ยวกับติดใจ"
        $favorites = Favorite::when(! auth('admin')->check(), function ($query) {
            return $query->where('is_active', 1);
        })->orderBy('sort_order', 'asc')->get();

        $settings = SiteSetting::all()->pluck('key')->mapWithKeys(function ($key) {
            return [$key => SiteSetting::get($key)];
        })->toArray();

        return view('about', compact('favorites', 'settings'));
    }
}
