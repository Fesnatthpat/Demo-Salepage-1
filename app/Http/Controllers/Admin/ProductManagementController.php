<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductSalepage::with(['images' => function($query) {
            $query->where('is_primary', true);
        }])->orderBy('pd_sp_id', 'desc');

        // Basic search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('pd_sp_name', 'like', $searchTerm)
                  ->orWhere('pd_sp_details', 'like', $searchTerm);
        }

        $products = $query->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productSalepage = new ProductSalepage();
        return view('admin.products.create', compact('productSalepage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_details' => 'nullable|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Each image
        ]);

        // --- Generate Sequential Product Code ---
        $lastProduct = ProductSalepage::orderBy('pd_sp_id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->pd_sp_id + 1 : 1;
        $productCode = 'P-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        // --- End Generation ---

        $productSalepage = ProductSalepage::create([
            'pd_sp_name' => $validatedData['pd_sp_name'],
            'pd_sp_details' => $validatedData['pd_sp_details'],
            'pd_code' => $productCode,
            'pd_id' => 1,
            'pd_sp_price' => 0,
            'pd_sp_active' => false,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product_images', 'public');

                ProductImage::create([
                    'product_id' => $productSalepage->pd_sp_id,
                    'image_name' => $image->getClientOriginalName(),
                    'image_path' => $path,
                    'image_alt' => $validatedData['pd_sp_name'] . ' image ' . ($index + 1),
                    'image_size' => $image->getSize(),
                    'image_type' => $image->getMimeType(),
                    'is_primary' => ($index === 0),
                    'sort_order' => $index,
                    'storage' => 'public',
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'สินค้าถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('admin.products.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     * แก้ไข: รับ $id โดยตรงแล้วค้นหา เพื่อป้องกันปัญหา Binding
     */
    public function edit($id)
    {
        // ค้นหาด้วย pd_sp_id ถ้าไม่เจอก็จะ Error 404 ซึ่งถูกต้องแล้ว
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        
        $productSalepage->load('images');
        return view('admin.products.edit', compact('productSalepage'));
    }

    /**
     * Update the specified resource in storage.
     * แก้ไข: รับ $id โดยตรง
     */
    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();

        $validatedData = $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_details' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_primary' => 'nullable|numeric',
        ]);

        $productSalepage->update([
            'pd_sp_name' => $validatedData['pd_sp_name'],
            'pd_sp_details' => $validatedData['pd_sp_details'],
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('product_images', 'public');

                ProductImage::create([
                    'product_id' => $productSalepage->pd_sp_id,
                    'image_name' => $image->getClientOriginalName(),
                    'image_path' => $path,
                    'image_alt' => $validatedData['pd_sp_name'] . ' image ' . ($productSalepage->images->count() + $index + 1),
                    'image_size' => $image->getSize(),
                    'image_type' => $image->getMimeType(),
                    'is_primary' => false,
                    'sort_order' => $productSalepage->images->count() + $index,
                    'storage' => 'public',
                ]);
            }
        }

        // Update primary image status
        if ($request->has('is_primary')) {
            $productSalepage->images()->update(['is_primary' => false]);
            ProductImage::where('img_pd_id', $validatedData['is_primary'])
                        ->where('product_id', $productSalepage->pd_sp_id)
                        ->update(['is_primary' => true]);
        } else {
            if ($productSalepage->images()->where('is_primary', true)->doesntExist() && $productSalepage->images()->exists()) {
                $productSalepage->images()->oldest()->first()->update(['is_primary' => true]);
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'สินค้าถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     * แก้ไข: รับ $id โดยตรง
     */
    public function destroy($id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();

        foreach ($productSalepage->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $productSalepage->delete();

        return redirect()->route('admin.products.index')->with('success', 'สินค้าถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Handle AJAX request to delete a single product image.
     */
    public function deleteImage(ProductImage $image)
    {
        // ส่วนนี้มักจะใช้ Route Binding ที่เป็น Default (id) อยู่แล้ว ถ้าใช้งานได้ปกติก็ไม่ต้องแก้ครับ
        try {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();

            $productSalepage = $image->productSalepage;
            if ($productSalepage && $productSalepage->images()->where('is_primary', true)->doesntExist() && $productSalepage->images()->exists()) {
                $productSalepage->images()->oldest()->first()->update(['is_primary' => true]);
            }

            return response()->json(['success' => true, 'message' => 'รูปภาพถูกลบเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}