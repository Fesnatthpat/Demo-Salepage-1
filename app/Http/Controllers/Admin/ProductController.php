<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\LogsActivity;
use App\Http\Controllers\Controller;
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->orderBy('pd_sp_id', 'desc');

        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pd_sp_name', 'like', $searchTerm)
                    ->orWhere('pd_sp_code', 'like', $searchTerm);
            });
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        $products = $query->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();

        return view('admin.products.create', [
            'productSalepage' => new ProductSalepage,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $this->validateSalePage($request);

        return DB::transaction(function () use ($request) {
            // 1. สร้างรหัสสินค้า
            $lastProduct = ProductSalepage::latest('pd_sp_id')->first();
            $nextId = $lastProduct ? ($lastProduct->pd_sp_id + 1) : 1;
            $generatedCode = 'P-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

            // 2. บันทึกข้อมูลหลัก
            $salePage = ProductSalepage::create([
                'pd_sp_code' => $generatedCode,
                'pd_sp_name' => $request->pd_sp_name,
                'pd_sp_description' => $request->pd_sp_details,
                'pd_sp_price' => $request->pd_sp_price,
                'pd_sp_discount' => $request->pd_sp_discount ?? 0,
                'pd_sp_stock' => $request->pd_sp_stock,
                'pd_sp_active' => $request->boolean('pd_sp_active'),
                'is_recommended' => $request->boolean('is_recommended'),
                'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
            ]);

            // 3. บันทึกรูปภาพ (พร้อมกำหนด Sort Order) [แก้ตรงนี้]
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('product_images', 'public');
                    $salePage->images()->create([
                        'img_path' => $path,
                        'img_sort' => $index, // เริ่มต้นที่ 0, 1, 2...
                    ]);
                }
            }

            // 4. บันทึกตัวเลือกสินค้า
            if ($request->has('product_options')) {
                foreach ($request->product_options as $option) {
                    if (! empty($option['option_name'])) {
                        $salePage->options()->create([
                            'option_name' => $option['option_name'],
                            'option_price' => $option['option_price'] ?? $salePage->pd_sp_price,
                            'option_stock' => $option['option_stock'] ?? 0,
                            'option_active' => 1,
                        ]);
                    }
                }
            }

            $this->logActivity($salePage, 'created');

            return redirect()->route('admin.products.index')
                ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว');
        });
    }

    public function edit($id)
    {
        $productSalepage = ProductSalepage::with(['images', 'options'])->where('pd_sp_id', $id)->firstOrFail();
        $products = ProductSalepage::where('pd_sp_id', '!=', $id)->orderBy('pd_sp_name')->get();

        return view('admin.products.edit', [
            'productSalepage' => $productSalepage,
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        $this->validateSalePage($request, $productSalepage);

        return DB::transaction(function () use ($request, $productSalepage) {
            $originalData = $productSalepage->toArray();

            // 1. อัปเดตข้อมูลหลัก
            $productSalepage->update([
                'pd_sp_name' => $request->pd_sp_name,
                'pd_sp_price' => $request->pd_sp_price,
                'pd_sp_discount' => $request->pd_sp_discount ?? 0,
                'pd_sp_description' => $request->pd_sp_details,
                'pd_sp_stock' => $request->pd_sp_stock,
                'pd_sp_active' => $request->boolean('pd_sp_active'),
                'is_recommended' => $request->boolean('is_recommended'),
                'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
            ]);

            // 2. รูปภาพ (อัปโหลดเพิ่ม)
            if ($request->hasFile('images')) {
                // หาค่า sort สูงสุดเดิมก่อน
                $maxSort = $productSalepage->images()->max('img_sort') ?? -1;

                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('product_images', 'public');
                    $productSalepage->images()->create([
                        'img_path' => $path,
                        'img_sort' => $maxSort + 1 + $index, // ต่อท้ายอันเดิม
                    ]);
                }
            }

            // 3. จัดการตัวเลือกสินค้า
            $productSalepage->options()->delete();

            if ($request->has('product_options')) {
                foreach ($request->product_options as $option) {
                    if (! empty($option['option_name'])) {
                        $productSalepage->options()->create([
                            'option_name' => $option['option_name'],
                            'option_price' => $option['option_price'] ?? $productSalepage->pd_sp_price,
                            'option_stock' => $option['option_stock'] ?? 0,
                            'option_active' => 1,
                        ]);
                    }
                }
            }

            $this->logActivity($productSalepage, 'updated', $originalData, $productSalepage->toArray());

            return redirect()->route('admin.products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
        });
    }

    public function destroy($id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        $this->logActivity($productSalepage, 'deleted');

        foreach ($productSalepage->images as $img) {
            Storage::disk('public')->delete($img->img_path);
        }

        $productSalepage->options()->delete();
        $productSalepage->delete();

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    public function destroyImage($imageId)
    {
        $image = DB::table('product_images')->where('img_id', $imageId)->first();
        if ($image) {
            Storage::disk('public')->delete($image->img_path);
            DB::table('product_images')->where('img_id', $imageId)->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    // ✅ Method สำหรับตั้งรูปหลัก (Self-Healing Logic)
    public function setMainImage($imageId)
    {
        return DB::transaction(function () use ($imageId) {
            $image = \App\Models\ProductImage::find($imageId);

            if (! $image) {
                return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
            }

            // 1. รีเซ็ตรูปทั้งหมดของสินค้านี้ให้เป็นค่าสูงๆ ไว้ก่อน (เช่น 99)
            \App\Models\ProductImage::where('pd_sp_id', $image->pd_sp_id)
                ->update(['img_sort' => 99]);

            // 2. ตั้งรูปที่เลือกให้เป็น 0 (หลัก)
            $image->img_sort = 0;
            $image->save();

            // 3. เรียงลำดับรูปที่เหลือใหม่ (Clean Data)
            \App\Models\ProductImage::where('pd_sp_id', $image->pd_sp_id)
                ->where('img_id', '!=', $imageId)
                ->orderBy('img_id')
                ->each(function ($img, $index) {
                    $img->update(['img_sort' => $index + 1]);
                });

            return response()->json(['success' => true]);
        });
    }

    private function validateSalePage(Request $request, ?ProductSalepage $salePage = null): array
    {
        return $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_price' => 'required|numeric|min:0',
            'pd_sp_stock' => 'required|integer|min:0',
            'pd_sp_display_location' => 'nullable|string',
            'product_options' => 'nullable|array',
            'product_options.*.option_name' => 'nullable|string|max:255',
            'product_options.*.option_price' => 'nullable|numeric|min:0',
            'product_options.*.option_stock' => 'nullable|integer|min:0',
        ]);
    }
}
