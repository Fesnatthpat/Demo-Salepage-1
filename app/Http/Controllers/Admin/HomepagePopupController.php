<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepagePopup;
use App\Models\ProductSalepage;
use App\Http\Controllers\Admin\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepagePopupController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $popups = HomepagePopup::with('product')->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.popups.index', compact('popups'));
    }

    public function create()
    {
        $products = ProductSalepage::where('pd_sp_active', true)->orderBy('pd_sp_name')->get();
        return view('admin.popups.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'product_id' => 'nullable|exists:product_salepage,pd_sp_id',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_type' => 'required|in:once_per_session,always,once_per_day',
            'display_pages' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['name', 'product_id', 'link_url', 'is_active', 'start_date', 'end_date', 'display_type', 'display_pages', 'sort_order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('popups', 'public');
        }

        $popup = HomepagePopup::create($data);
        $this->logActivity($popup, 'created');

        return redirect()->route('admin.popups.index')->with('success', 'สร้าง Popup เรียบร้อยแล้ว');
    }

    public function edit(HomepagePopup $popup)
    {
        $products = ProductSalepage::where('pd_sp_active', true)->orderBy('pd_sp_name')->get();
        return view('admin.popups.edit', compact('popup', 'products'));
    }

    public function update(Request $request, HomepagePopup $popup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'product_id' => 'nullable|exists:product_salepage,pd_sp_id',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'display_type' => 'required|in:once_per_session,always,once_per_day',
            'display_pages' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $originalData = $popup->toArray();
        $data = $request->only(['name', 'product_id', 'link_url', 'is_active', 'start_date', 'end_date', 'display_type', 'display_pages', 'sort_order']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            if ($popup->image_path) {
                Storage::disk('public')->delete($popup->image_path);
            }
            $data['image_path'] = $request->file('image')->store('popups', 'public');
        }

        $popup->update($data);

        $this->logActivity($popup, 'updated', $originalData, $popup->toArray());

        return redirect()->route('admin.popups.index')->with('success', 'อัปเดต Popup เรียบร้อยแล้ว');
    }

    public function destroy(HomepagePopup $popup)
    {
        $this->logActivity($popup, 'deleted');
        if ($popup->image_path) {
            Storage::disk('public')->delete($popup->image_path);
        }
        $popup->delete();

        return redirect()->route('admin.popups.index')->with('success', 'ลบ Popup เรียบร้อยแล้ว');
    }

    public function toggleStatus(HomepagePopup $popup)
    {
        $originalData = $popup->toArray();
        $popup->is_active = !$popup->is_active;
        $popup->save();

        $this->logActivity($popup, 'updated', $originalData, $popup->toArray());

        return response()->json([
            'success' => true,
            'is_active' => $popup->is_active,
            'message' => 'อัปเดตสถานะเรียบร้อยแล้ว',
        ]);
    }
}
