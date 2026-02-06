<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
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
                $oldPath = Setting::where('key', $key)->value('value');
                if ($oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
                
                $path = $request->file($key)->store('settings', 'public');
                $value = $path;
            }

            // Don't save null values for non-file inputs if they are empty
            if (! $request->hasFile($key) && $value === null) {
                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    public function destroy($key)
    {
        $setting = Setting::where('key', $key)->first();

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

