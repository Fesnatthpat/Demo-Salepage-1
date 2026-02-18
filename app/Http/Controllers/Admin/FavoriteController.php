<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\FavoriteImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Favorite::with('images')->orderBy('sort_order', 'asc')->get();
        return view('admin.favorites.index', compact('favorites'));
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
}
