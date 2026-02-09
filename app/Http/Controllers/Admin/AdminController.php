<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Other validations can be added here
        ]);

        // Handle site_logo upload
        if ($request->hasFile('site_logo')) {
            // Delete old logo if it exists
            $oldLogoPath = SiteSetting::get('site_logo');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
            $logoPath = $request->file('site_logo')->store('uploads/settings', 'public');
            SiteSetting::set('site_logo', $logoPath);
        }

        // Handle site_cover_image upload
        if ($request->hasFile('site_cover_image')) {
            // Delete old cover image if it exists
            $oldCoverImagePath = SiteSetting::get('site_cover_image');
            if ($oldCoverImagePath && Storage::disk('public')->exists($oldCoverImagePath)) {
                Storage::disk('public')->delete($oldCoverImagePath);
            }
            $coverImagePath = $request->file('site_cover_image')->store('uploads/settings', 'public');
            SiteSetting::set('site_cover_image', $coverImagePath);
        }

        // JSON array inputs
        $jsonKeys = [
            'hero_slider_items',
            'reasons_section_items',
            'second_slider_items',
            'category_menu_items',
            'small_slider_allproducts_items',
            'service_bar_items',
        ];

        foreach ($jsonKeys as $key) {
            if ($request->has($key)) {
                $jsonString = $request->input($key);
                $items = json_decode($jsonString, true);

                if (is_array($items)) {
                    foreach ($items as &$item) { // Use & for reference to modify original array
                        if (isset($item['image']) && is_string($item['image'])) {
                            $imagePath = trim($item['image'], '"'); // Remove surrounding quotes

                            // Attempt to make path relative to public directory if it's an absolute path
                            $publicPath = str_replace('/', DIRECTORY_SEPARATOR, public_path()); // Ensure correct directory separator
                            if (str_starts_with($imagePath, $publicPath)) {
                                $imagePath = substr($imagePath, strlen($publicPath) + 1); // +1 for the directory separator
                            }
                            $item['image'] = str_replace(DIRECTORY_SEPARATOR, '/', $imagePath); // Convert backslashes to forward slashes
                        }
                    }
                    $jsonString = json_encode($items);
                }
                SiteSetting::set($key, $jsonString);
            }
        }

        // Text inputs
        $textKeys = [
            'site_description',
            'allergy_info_content',
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                SiteSetting::set($key, $request->input($key));
            }
        }

        return Redirect::back()->with('success', 'Settings updated successfully!');
    }

    public function index()
    {
        $settings = SiteSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();

        return view('admin.settings.index', compact('settings'));
    }
}
