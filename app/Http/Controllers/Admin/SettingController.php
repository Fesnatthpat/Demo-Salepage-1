<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->map(function ($value) {
            $decoded = json_decode($value, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        })->toArray();

        // ดึงข้อมูลสำหรับ Homepage
        $heroBanners = \App\Models\Banner::location('homepage')->hero()->active()->get();
        $infoBanner = \App\Models\Banner::location('homepage')->info()->active()->first();
        $secondaryBanners = \App\Models\Banner::location('homepage')->secondary()->active()->get();
        $services = \App\Models\Service::where('is_active', true)->orderBy('sort_order')->get();
        $features = \App\Models\Feature::where('is_active', true)->orderBy('sort_order')->get();
        $reviewImages = \App\Models\ProductReviewImage::whereNull('product_salepage_id')->orderBy('sort_order', 'asc')->get();

        // ดึงข้อมูลสำหรับ All Products
        $allProductsHeroBanners = \App\Models\Banner::location('allproducts')->hero()->active()->get();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.settings.index', compact(
            'settings',
            'heroBanners',
            'infoBanner',
            'secondaryBanners',
            'services',
            'features',
            'reviewImages',
            'allProductsHeroBanners',
            'categories'
        ));
    }

    public function update(Request $request)
    {
        // Whitelist of allowed setting keys
        $allowedKeys = [
            'about_title', 'about_subtitle', 'life_title', 'life_subtitle',
            'team_title', 'team_subtitle', 'team_phone', 'team_email',
            'social_title', 'footer_slogan', 'faq_title', 'home_recommended_title'
        ];

        // Support bulk update from settings array
        if ($request->has('settings') && is_array($request->settings)) {
            foreach ($request->settings as $key => $value) {
                if (in_array($key, $allowedKeys)) {
                    SiteSetting::set($key, $value);
                }
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
                ]);
            }
            
            return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
        }

        $validated = $request->validate([
            'site_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'site_cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'site_description' => 'nullable|string|max:500',
            'site_menu' => 'nullable|json',
            'hero_section_tagline' => 'nullable|string|max:100',
            'hero_section_title_prefix' => 'nullable|string|max:100',
            'hero_section_title_highlight' => 'nullable|string|max:100',
            'hero_section_title_suffix' => 'nullable|string|max:100',
            'hero_section_description' => 'nullable|string|max:500',
            'hero_section_small_text' => 'nullable|string|max:500',
            'service_bar_items' => 'nullable|json',
            'hero_slider_items' => 'nullable|json',
            'allergy_info_content' => 'nullable|string',
            'reasons_section_items' => 'nullable|json',
            'second_slider_items' => 'nullable|json',
            'category_menu_items' => 'nullable|json',
            'small_slider_allproducts_items' => 'nullable|json',
        ]);

        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            // Handle file uploads
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                // Delete old file if it exists
                $oldPath = SiteSetting::get($key);
                if ($oldPath && !is_array($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                
                $path = $request->file($key)->store('settings', 'public');
                $value = $path;
            }

            // Don't save null values for non-file inputs if they are empty
            if (! $request->hasFile($key) && $value === null) {
                continue;
            }

            SiteSetting::set($key, $value);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว'
            ]);
        }

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    public function destroy($key)
    {
        $setting = SiteSetting::where('key', $key)->first();

        if ($setting) {
            // If the setting is an image, delete it from storage
            if (in_array($key, ['site_logo', 'site_cover_image'])) {
                Storage::disk('public')->delete($setting->value);
            }
            
            $setting->delete();
            
            return response()->json(['success' => true, 'message' => 'ลบการตั้งค่าเรียบร้อยแล้ว']);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบการตั้งค่าดังกล่าว'], 404);
    }
}

