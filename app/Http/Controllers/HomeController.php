<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = ProductSalepage::with('images')
            ->where('pd_sp_active', 1)
            ->where('is_recommended', 1) // Using the correct field for recommended products
            ->orderBy('pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        $settings = SiteSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => SiteSetting::get($setting->key)]; // Use SiteSetting::get() to decode JSON
        })->toArray();

        return view('index', compact('recommendedProducts', 'settings'));
    }

    public function about()
    {
        return view('about');
    }
}
