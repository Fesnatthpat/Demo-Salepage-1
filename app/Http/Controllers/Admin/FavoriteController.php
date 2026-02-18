<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Favorite::orderBy('sort_order', 'asc')->get();
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'content', 'sort_order');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['image_path'] = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('favorites', 'public');
            $data['image_path'] = $path;
        }

        Favorite::create($data);

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only('title', 'content', 'sort_order');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($favorite->image_path) {
                Storage::disk('public')->delete($favorite->image_path);
            }
            $path = $request->file('image')->store('favorites', 'public');
            $data['image_path'] = $path;
        }

        $favorite->update($data);

        return redirect()->route('admin.favorites.index')
                         ->with('success', 'อัปเดต "เกี่ยวกับติดใจ" เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        if ($favorite->image_path) {
            Storage::disk('public')->delete($favorite->image_path);
        }
        
        $favorite->delete();

        return redirect()->route('admin.favorites.index')
                         ->with('success', 'ลบ "เกี่ยวกับติดใจ" เรียบร้อยแล้ว');
    }
}
