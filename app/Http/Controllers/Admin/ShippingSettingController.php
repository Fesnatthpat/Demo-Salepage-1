<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;

class ShippingSettingController extends Controller
{
    /**
     * Display the shipping settings page with global settings and methods.
     */
    public function index()
    {
        $globalSettings = [
            'shipping_mode' => ShippingSetting::get('shipping_mode', 'global'), // 'global' or 'methods'
            'free_shipping_threshold' => (float) ShippingSetting::get('free_shipping_threshold', 999),
            'bkk_flat_rate' => (float) ShippingSetting::get('bkk_flat_rate', 40),
            'upc_flat_rate' => (float) ShippingSetting::get('upc_flat_rate', 60),
        ];

        $methods = ShippingMethod::orderBy('sort_order')->orderBy('id', 'desc')->get();

        return view('admin.shipping.index', compact('globalSettings', 'methods'));
    }

    /**
     * Update global settings.
     */
    public function updateGlobal(Request $request)
    {
        $validated = $request->validate([
            'shipping_mode' => 'required|string|in:global,methods',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'bkk_flat_rate' => 'required|numeric|min:0',
            'upc_flat_rate' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            ShippingSetting::set($key, $value);
        }

        return response()->json([
            'success' => true, 
            'message' => 'บันทึกการตั้งค่า Global เรียบร้อยแล้ว'
        ]);
    }

    /**
     * Create or update a shipping method.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|numeric',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:50',
            'is_active' => 'required|boolean',
            'is_default' => 'required|boolean',
            'bkk_rate' => 'required|numeric|min:0',
            'upc_rate' => 'required|numeric|min:0',
            'per_item_rate' => 'required|numeric|min:0',
            'free_threshold' => 'nullable|numeric|min:0',
            'min_items_for_free_shipping' => 'nullable|integer|min:0',
        ]);

        $id = $request->id;

        // If this method is set to default, unset others
        if ($validated['is_default']) {
            ShippingMethod::where('id', '!=', $id)->update(['is_default' => false]);
        }

        if ($id) {
            $method = ShippingMethod::findOrFail($id);
            $method->update($validated);
        } else {
            $method = ShippingMethod::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกบริษัทขนส่งเรียบร้อยแล้ว',
            'method' => $method
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Request $request, ShippingMethod $method)
    {
        $method->update(['is_active' => $request->is_active]);
        return response()->json(['success' => true]);
    }

    /**
     * Remove a shipping method.
     */
    public function destroy(ShippingMethod $method)
    {
        $method->delete();
        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลบริษัทขนส่งเรียบร้อยแล้ว'
        ]);
    }
}
