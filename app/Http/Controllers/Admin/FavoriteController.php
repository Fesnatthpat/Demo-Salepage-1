<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\FavoriteImage;
use App\Models\AboutVideo;
use App\Models\AboutGallery;
use App\Models\AboutSocialLink;
use App\Models\Contact;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Favorite::with('images')->orderBy('sort_order', 'asc')->get();
        $videos = AboutVideo::orderBy('sort_order', 'asc')->get();
        $galleries = AboutGallery::with('images')->orderBy('sort_order', 'asc')->get();
        $socialLinks = AboutSocialLink::orderBy('sort_order', 'asc')->get();
        $contacts = Contact::orderBy('sort_order', 'asc')->get();
        
        $settings = SiteSetting::all()->pluck('key')->mapWithKeys(function ($key) {
            return [$key => SiteSetting::get($key)];
        })->toArray();

        return view('admin.favorites.index', compact('favorites', 'videos', 'galleries', 'socialLinks', 'contacts', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.favorites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'content', 'sort_order');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        
        $favorite = Favorite::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('favorites', 'public');
                $favorite->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.favorites.index')
                         ->with('success', 'สร้าง "เกี่ยวกับติดใจ" เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Favorite $favorite)
    {
        return redirect()->route('admin.favorites.edit', $favorite);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Favorite $favorite)
    {
        $favorite->load('images');
        return view('admin.favorites.edit', compact('favorite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Favorite $favorite)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sort_order' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'content', 'sort_order');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        $favorite->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('favorites', 'public');
                $favorite->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('admin.favorites.index')
                         ->with('success', 'อัปเดต "เกี่ยวกับติดใจ" เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        // Eager load images to ensure they are available
        $favorite->load('images');

        // Delete all associated image files from storage
        foreach ($favorite->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        // Deleting the favorite will trigger the 'onDelete('cascade')' 
        // for the favorite_images table records.
        $favorite->delete();

        return redirect()->route('admin.favorites.index')
                         ->with('success', 'ลบ "เกี่ยวกับติดใจ" เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified image from storage and database.
     */
    public function destroyImage(FavoriteImage $image)
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
    }

    // --- Video Management ---
    public function storeVideo(Request $request)
    {
        $request->validate([
            'video_url' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'video_url', 'duration', 'sort_order');
        $data['is_active'] = $request->boolean('is_active', true);

        // ดึงข้อมูลจาก TikTok ถ้าเป็นลิงก์ TikTok
        if (str_contains($request->video_url, 'tiktok.com')) {
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://www.tiktok.com/oembed', [
                    'url' => $request->video_url
                ]);
                
                if ($response->successful()) {
                    $tiktokData = $response->json();
                    if (empty($data['title'])) $data['title'] = $tiktokData['title'] ?? null;
                    $data['thumbnail_url'] = $tiktokData['thumbnail_url'] ?? null;
                    $data['embed_html'] = $tiktokData['html'] ?? null;
                }
            } catch (\Exception $e) {
                // ข้ามหากดึงข้อมูลไม่ได้
            }
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('about/videos', 'public');
        }

        AboutVideo::create($data);

        return back()->with('success', 'เพิ่มวิดีโอเรียบร้อยแล้ว');
    }

    public function updateVideo(Request $request, AboutVideo $video)
    {
        $request->validate([
            'video_url' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'video_url', 'duration', 'sort_order');
        $data['is_active'] = $request->boolean('is_active', true);

        // ดึงข้อมูลจาก TikTok ถ้าเป็นลิงก์ TikTok และลิงก์เปลี่ยนไป
        if (str_contains($request->video_url, 'tiktok.com') && $request->video_url !== $video->video_url) {
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://www.tiktok.com/oembed', [
                    'url' => $request->video_url
                ]);
                
                if ($response->successful()) {
                    $tiktokData = $response->json();
                    if (empty($data['title'])) $data['title'] = $tiktokData['title'] ?? null;
                    $data['thumbnail_url'] = $tiktokData['thumbnail_url'] ?? null;
                    $data['embed_html'] = $tiktokData['html'] ?? null;
                }
            } catch (\Exception $e) {
                // ข้ามหากดึงข้อมูลไม่ได้
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('about/videos', 'public');
        }

        $video->update($data);

        return back()->with('success', 'อัปเดตวิดีโอเรียบร้อยแล้ว');
    }

    public function destroyVideo(AboutVideo $video)
    {
        if ($video->thumbnail_path) {
            Storage::disk('public')->delete($video->thumbnail_path);
        }
        $video->delete();
        return back()->with('success', 'ลบวิดีโอเรียบร้อยแล้ว');
    }

    // --- Gallery Management ---
    public function storeGallery(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gallery = AboutGallery::create([
            'title' => $request->title,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('about/galleries', 'public');
                $gallery->images()->create(['image_path' => $path]);
            }
        }

        return back()->with('success', 'เพิ่มอัลบั้มเรียบร้อยแล้ว');
    }

    public function updateGallery(Request $request, AboutGallery $gallery)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $gallery->update([
            'title' => $request->title,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('about/galleries', 'public');
                $gallery->images()->create(['image_path' => $path]);
            }
        }

        return back()->with('success', 'อัปเดตอัลบั้มเรียบร้อยแล้ว');
    }

    public function destroyGallery(AboutGallery $gallery)
    {
        foreach ($gallery->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $gallery->delete();
        return back()->with('success', 'ลบอัลบั้มเรียบร้อยแล้ว');
    }

    public function destroyGalleryImage(\App\Models\AboutGalleryImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return response()->json(['success' => true]);
    }

    // --- Social Link Management ---
    public function storeSocialLink(Request $request)
    {
        Log::info('Storing social link', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:1024',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            Log::info('Image found, uploading...');
            $data['image_path'] = $request->file('image')->store('about/socials', 'public');
            $data['icon_class'] = null; // ถ้าใช้รูป ให้ล้างค่า icon class
        }

        AboutSocialLink::create($data);

        return back()->with('success', 'เพิ่มโซเชียลมีเดียเรียบร้อยแล้ว');
    }

    public function updateSocialLink(Request $request, AboutSocialLink $socialLink)
    {
        Log::info('Updating social link ' . $socialLink->id, $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:1024',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            Log::info('New image found, uploading...');
            // Delete old image if exists
            if ($socialLink->image_path && Storage::disk('public')->exists($socialLink->image_path)) {
                Storage::disk('public')->delete($socialLink->image_path);
            }
            $data['image_path'] = $request->file('image')->store('about/socials', 'public');
            $data['icon_class'] = null; // ถ้าเปลี่ยนมาใช้รูป ให้ล้างค่า icon class
        } else {
            // ถ้ามีการส่งค่า icon_class มา และไม่มีการอัปโหลดรูปใหม่ 
            // ให้เช็คว่าผู้ใช้ต้องการสลับกลับไปใช้ FontAwesome หรือไม่
            // (ในระบบนี้ถ้ากรอก icon_class มา เราจะถือว่าใช้ FontAwesome)
            if ($request->filled('icon_class')) {
                // ลบรูปเก่าถ้ามี
                if ($socialLink->image_path && Storage::disk('public')->exists($socialLink->image_path)) {
                    Storage::disk('public')->delete($socialLink->image_path);
                }
                $data['image_path'] = null;
            }
        }

        $socialLink->update($data);

        return back()->with('success', 'อัปเดตโซเชียลมีเดียเรียบร้อยแล้ว');
    }

    public function destroySocialLink(AboutSocialLink $socialLink)
    {
        if ($socialLink->image_path && \Storage::disk('public')->exists($socialLink->image_path)) {
            \Storage::disk('public')->delete($socialLink->image_path);
        }
        $socialLink->delete();
        return back()->with('success', 'ลบโซเชียลมีเดียเรียบร้อยแล้ว');
    }

    // --- Contact Management ---
    public function storeContact(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);
        
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('about/contacts', 'public');
        }

        Contact::create($data);

        return back()->with('success', 'เพิ่มข้อมูลติดต่อเรียบร้อยแล้ว');
    }

    public function updateContact(Request $request, Contact $contact)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($contact->image_path) {
                Storage::disk('public')->delete($contact->image_path);
            }
            $data['image_path'] = $request->file('image')->store('about/contacts', 'public');
        }

        $contact->update($data);

        return back()->with('success', 'อัปเดตข้อมูลติดต่อเรียบร้อยแล้ว');
    }

    public function destroyContact(Contact $contact)
    {
        if ($contact->image_path) {
            Storage::disk('public')->delete($contact->image_path);
        }
        $contact->delete();
        return back()->with('success', 'ลบข้อมูลติดต่อเรียบร้อยแล้ว');
    }
}
