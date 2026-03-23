<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Http\Controllers\Admin\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    use LogsActivity;

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');
        $credentials['is_active'] = true; // บังคับว่าต้องสถานะ Active เท่านั้น
        $remember = $request->filled('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // บันทึกเวลาที่เข้าระบบล่าสุด
            $admin = Auth::guard('admin')->user();
            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // บันทึก Log กิจกรรม
            $this->logActivity($admin, 'logged_in', null, ['ip' => $request->ip()]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withInput($request->only('username'))
            ->withErrors([
                'username' => 'ชื่อผู้ใช้งาน รหัสผ่านไม่ถูกต้อง หรือบัญชีของคุณถูกระงับ',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        return redirect('/admin/login');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'site_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        // บันทึก Log การแก้ไขตั้งค่า
        $logModel = SiteSetting::first() ?? new SiteSetting();
        $this->logActivity($logModel, 'updated_settings', null, ['inputs' => $request->except(['_token', 'site_logo', 'site_cover_image'])]);

        // --- 1 & 2. Logo & Cover Image (SiteSetting) ---
        if ($request->hasFile('site_logo')) {
            $oldLogoPath = SiteSetting::get('site_logo');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
            $logoPath = $request->file('site_logo')->store('uploads/settings', 'public');
            SiteSetting::set('site_logo', $logoPath);
        }

        if ($request->hasFile('site_cover_image')) {
            $oldCoverImagePath = SiteSetting::get('site_cover_image');
            if ($oldCoverImagePath && Storage::disk('public')->exists($oldCoverImagePath)) {
                Storage::disk('public')->delete($oldCoverImagePath);
            }
            $coverImagePath = $request->file('site_cover_image')->store('uploads/settings', 'public');
            SiteSetting::set('site_cover_image', $coverImagePath);
        }

        // --- 3. Hero Sliders (Homepage) ---
        if ($request->has('hero_banners')) {
            $heroKeepIds = [];
            foreach ($request->hero_banners as $index => $bannerData) {
                $id = !empty($bannerData['id']) ? $bannerData['id'] : null;
                $updateData = [
                    'type' => 'hero',
                    'location' => 'homepage',
                    'sort_order' => $index,
                    'link_url' => $bannerData['link_url'] ?? null,
                    'is_active' => true,
                ];

                if ($request->hasFile("hero_banners.$index.image")) {
                    $updateData['image_path'] = $request->file("hero_banners.$index.image")->store('uploads/banners', 'public');
                } elseif (!empty($bannerData['existing_path'])) {
                    $updateData['image_path'] = $bannerData['existing_path'];
                }

                if (isset($updateData['image_path'])) {
                    $banner = \App\Models\Banner::updateOrCreate(['id' => $id], $updateData);
                    $heroKeepIds[] = $banner->id;
                }
            }
            \App\Models\Banner::location('homepage')->hero()->whereNotIn('id', $heroKeepIds)->delete();
        }

        // --- 4. Allergy/Info Banner ---
        if ($request->remove_allergy_image == '1') {
            SiteSetting::set('allergy_image', null);
            \App\Models\Banner::location('homepage')->where('type', 'info')->update(['is_active' => false, 'image_path' => '']);
        } elseif ($request->hasFile('allergy_image')) {
            $path = $request->file('allergy_image')->store('uploads/banners', 'public');
            SiteSetting::set('allergy_image', $path);
            
            \App\Models\Banner::updateOrCreate(
                ['type' => 'info', 'location' => 'homepage'],
                ['image_path' => $path, 'is_active' => true]
            );
        }

        // --- 5. Secondary Sliders (Homepage) ---
        if ($request->has('secondary_banners')) {
            $secKeepIds = [];
            foreach ($request->secondary_banners as $index => $bannerData) {
                $id = !empty($bannerData['id']) ? $bannerData['id'] : null;
                $updateData = [
                    'type' => 'secondary',
                    'location' => 'homepage',
                    'sort_order' => $index,
                    'link_url' => $bannerData['link_url'] ?? null,
                    'is_active' => true,
                ];

                if ($request->hasFile("secondary_banners.$index.image")) {
                    $updateData['image_path'] = $request->file("secondary_banners.$index.image")->store('uploads/banners', 'public');
                } elseif (!empty($bannerData['existing_path'])) {
                    $updateData['image_path'] = $bannerData['existing_path'];
                }

                if (isset($updateData['image_path'])) {
                    $banner = \App\Models\Banner::updateOrCreate(['id' => $id], $updateData);
                    $secKeepIds[] = $banner->id;
                }
            }
            \App\Models\Banner::location('homepage')->secondary()->whereNotIn('id', $secKeepIds)->delete();
        }

        // --- 5.1. All Products Hero Sliders ---
        if ($request->has('all_products_hero_banners')) {
            $allProdKeepIds = [];
            foreach ($request->all_products_hero_banners as $index => $bannerData) {
                $id = !empty($bannerData['id']) ? $bannerData['id'] : null;
                $updateData = [
                    'type' => 'hero',
                    'location' => 'all_products',
                    'sort_order' => $index,
                    'link_url' => $bannerData['link_url'] ?? null,
                    'is_active' => true,
                ];

                if ($request->hasFile("all_products_hero_banners.$index.image")) {
                    $updateData['image_path'] = $request->file("all_products_hero_banners.$index.image")->store('uploads/banners', 'public');
                } elseif (!empty($bannerData['existing_path'])) {
                    $updateData['image_path'] = $bannerData['existing_path'];
                }

                if (isset($updateData['image_path'])) {
                    $banner = \App\Models\Banner::updateOrCreate(['id' => $id], $updateData);
                    $allProdKeepIds[] = $banner->id;
                }
            }
            \App\Models\Banner::location('all_products')->hero()->whereNotIn('id', $allProdKeepIds)->delete();
        }

        // --- 5.2. Category Management ---
        if ($request->has('categories')) {
            $catKeepIds = [];
            foreach ($request->categories as $index => $catData) {
                $id = !empty($catData['id']) ? $catData['id'] : null;
                $updateData = [
                    'name' => $catData['name'] ?? 'Untitled Category',
                    'icon' => $catData['icon'] ?? 'fas fa-th',
                    'link_url' => $catData['link_url'] ?? null,
                    'linked_product_id' => $catData['linked_product_id'] ?? null,
                    'sort_order' => $index,
                    'is_active' => true,
                ];

                if ($request->hasFile("categories.$index.image")) {
                    $updateData['image_path'] = $request->file("categories.$index.image")->store('uploads/categories', 'public');
                } elseif (!empty($catData['existing_path'])) {
                    $updateData['image_path'] = $catData['existing_path'];
                }

                $category = \App\Models\Category::updateOrCreate(['id' => $id], $updateData);
                $catKeepIds[] = $category->id;
            }
            \App\Models\Category::whereNotIn('id', $catKeepIds)->delete();
        }

        // --- 6. Service Bar (Dynamic Array) ---
        if ($request->has('services') && is_array($request->services)) {
            // ลบข้อมูลเก่าที่ไม่ได้อยู่ในรายการใหม่
            \App\Models\Service::query()->delete();
            
            foreach ($request->services as $index => $item) {
                if (!empty($item['title'])) {
                    \App\Models\Service::create([
                        'icon' => $item['icon'] ?? 'fas fa-star',
                        'title' => $item['title'],
                        'sort_order' => $index + 1,
                        'is_active' => true
                    ]);
                }
            }
        }

        // --- 7. 6 Reasons (Features) (Dynamic Array) ---
        if ($request->has('reasons') && is_array($request->reasons)) {
            \App\Models\Feature::query()->delete();
            
            foreach ($request->reasons as $index => $item) {
                if (!empty($item['title'])) {
                    \App\Models\Feature::create([
                        'icon' => $item['icon'] ?? 'fas fa-check',
                        'title' => $item['title'],
                        'description' => $item['description'] ?? '',
                        'sort_order' => $index + 1,
                        'is_active' => true
                    ]);
                }
            }
        }

        // --- 8. Customer Review Images (Dynamic Array) ---
        if ($request->has('review_images')) {
            $keepReviewIds = [];
            foreach ($request->review_images as $index => $reviewData) {
                $id = !empty($reviewData['id']) ? $reviewData['id'] : null;
                $updateData = [
                    'sort_order' => $index,
                ];

                if ($request->hasFile("review_images.$index.image")) {
                    $updateData['image_url'] = $request->file("review_images.$index.image")->store('uploads/reviews', 'public');
                } elseif (!empty($reviewData['existing_path'])) {
                    $updateData['image_url'] = $reviewData['existing_path'];
                }

                if (isset($updateData['image_url'])) {
                    // ใช้ updateOrCreate หากต้องการคง id เดิม หรือ create ใหม่
                    $review = \App\Models\ProductReviewImage::updateOrCreate(['id' => $id], array_merge($updateData, ['product_salepage_id' => null]));
                    $keepReviewIds[] = $review->id;
                }
            }
            // ลบรูปที่ไม่ได้อยู่ในรายการที่ส่งมา (เฉพาะที่เป็น site-wide reviews)
            \App\Models\ProductReviewImage::whereNull('product_salepage_id')->whereNotIn('id', $keepReviewIds)->delete();
        }

        // Handle generic settings array if exists
        if ($request->has('settings') && is_array($request->settings)) {
            foreach ($request->settings as $key => $value) {
                SiteSetting::set(trim($key), $value);
            }
        }

        // ล้าง Cache เพื่อให้หน้าแรกและหน้าอื่นๆ อัปเดตทันที
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // ข้ามหากไม่มีสิทธิ์รันคำสั่ง
        }

        return Redirect::back()->with('success', 'Settings updated successfully!');
    }

    public function index()
    {
        // ดึง Settings ทั้งหมด
        $settings = SiteSetting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->value];
        })->toArray();

        // ดึงข้อมูล CMS จากตารางใหม่ (Source of Truth)
        $heroBanners = \App\Models\Banner::location('homepage')->hero()->get();
        $secondaryBanners = \App\Models\Banner::location('homepage')->secondary()->get();
        $infoBanner = \App\Models\Banner::location('homepage')->info()->first();
        
        // All Products Page Content
        $allProductsHeroBanners = \App\Models\Banner::location('all_products')->hero()->get();
        $categories = \App\Models\Category::orderBy('sort_order')->get();
        $products = \App\Models\ProductSalepage::where('pd_sp_active', 1)->orderBy('pd_sp_name')->get();

        $services = \App\Models\Service::orderBy('sort_order')->get();
        $features = \App\Models\Feature::orderBy('sort_order')->get();
        $reviewImages = \App\Models\ProductReviewImage::whereNull('product_salepage_id')->orderBy('sort_order', 'asc')->get();

        return view('admin.settings.index', compact(
            'settings', 
            'heroBanners', 
            'secondaryBanners', 
            'infoBanner', 
            'allProductsHeroBanners',
            'categories',
            'products',
            'services', 
            'features',
            'reviewImages'
        ));
    }
}
