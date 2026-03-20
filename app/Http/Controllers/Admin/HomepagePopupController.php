<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepagePopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepagePopupController extends Controller
{
    public function index()
    {
        $popups = HomepagePopup::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.popups.index', compact('popups'));
    }

    public function create()
    {
        return view('admin.popups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_type' => 'required|in:once_per_session,always,once_per_day',
        ]);

        $data = $request->only(['name', 'link_url', 'is_active', 'start_date', 'end_date', 'display_type']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('popups', 'public');
        }

        HomepagePopup::create($data);

        return redirect()->route('admin.popups.index')->with('success', 'สร้าง Popup เรียบร้อยแล้ว');
    }

    public function edit(HomepagePopup $popup)
    {
        return view('admin.popups.edit', compact('popup'));
    }

    public function update(Request $request, HomepagePopup $popup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_type' => 'required|in:once_per_session,always,once_per_day',
        ]);

        $data = $request->only(['name', 'link_url', 'is_active', 'start_date', 'end_date', 'display_type']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($popup->image_path) {
                Storage::disk('public')->delete($popup->image_path);
            }
            $data['image_path'] = $request->file('image')->store('popups', 'public');
        }

        $popup->update($data);

        return redirect()->route('admin.popups.index')->with('success', 'อัปเดต Popup เรียบร้อยแล้ว');
    }

    public function destroy(HomepagePopup $popup)
    {
        if ($popup->image_path) {
            Storage::disk('public')->delete($popup->image_path);
        }
        $popup->delete();

        return redirect()->route('admin.popups.index')->with('success', 'ลบ Popup เรียบร้อยแล้ว');
    }

    public function toggleStatus(HomepagePopup $popup)
    {
        $popup->is_active = !$popup->is_active;
        $popup->save();

        return response()->json([
            'success' => true,
            'is_active' => $popup->is_active,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
        ]);
    }
}
