<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSalepage;
// use App\Models\ProductImage; // ถ้ามี Model นี้ให้เปิดใช้งาน
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductSalepage::with('images')->orderBy('pd_sp_id', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pd_sp_name', 'like', $searchTerm)
                    ->orWhere('pd_sp_code', 'like', $searchTerm); // แก้: pd_code -> pd_sp_code
            });
        }

        // Status Filter
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        // Type Filter
        if ($request->filled('type')) {
            switch ($request->type) {
                case 'recommended':
                    $query->where('is_recommended', true);
                    break;
                case 'promotion':
                    $query->where('pd_sp_discount', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('pd_sp_stock', '=', 0);
                    break;
                case 'general':
                    $query->where('is_recommended', false)->where(function ($q) {
                        $q->where('pd_sp_discount', '=', 0)->orWhereNull('pd_sp_discount');
                    });
                    break;
            }
        }

        $products = $query->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = ProductSalepage::orderBy('pd_sp_name')->get();

        return view('admin.products.create', [
            'productSalepage' => new ProductSalepage,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล
        $this->validateSalePage($request);

        // 2. สร้างรหัสสินค้าอัตโนมัติ (Auto Generate)
        $lastProduct = ProductSalepage::latest('pd_sp_id')->first();
        $nextId = $lastProduct ? ($lastProduct->pd_sp_id + 1) : 1;
        $generatedCode = 'P-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // 3. เตรียมข้อมูลบันทึก (Map ชื่อตัวแปรให้ตรง DB เป๊ะๆ)
        $dataToSave = [
            'pd_sp_code' => $generatedCode,
            'pd_sp_name' => $request->pd_sp_name,
            'pd_sp_description' => $request->pd_sp_details,     // แก้: details -> description
            'pd_sp_price' => $request->pd_sp_price,
            'pd_sp_discount' => $request->pd_sp_discount ?? 0,

            // ✅ [แก้ไขจุดที่ 1] บันทึกจำนวนสินค้า (Stock) ลงฐานข้อมูล
            'pd_sp_stock' => $request->pd_sp_stock,

            'pd_sp_active' => $request->boolean('pd_sp_active'),

            // คอลัมน์เพิ่มเติม
            'is_recommended' => $request->boolean('is_recommended'),
            'is_bogo_active' => $request->boolean('is_bogo_active'),
            'pd_sp_display_location' => $request->pd_sp_display_location ?? 'general',
        ];

        // บันทึกสินค้า
        $salePage = ProductSalepage::create($dataToSave);

        // จัดการ Relations (ถ้ามี)
        if ($request->has('options')) {
            $salePage->options()->attach($request->options);
        }
        if ($request->has('bogo_options')) {
            $salePage->bogoFreeOptions()->attach($request->bogo_options);
        }

        // 4. จัดการอัปโหลดรูปภาพ
        if ($request->hasFile('images')) {
            $isFirst = true;
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');

                // แก้: ใช้ img_path และ img_sort
                $salePage->images()->create([
                    'img_path' => $path,        // image_path -> img_path
                    'img_sort' => $isFirst ? 1 : 0, // is_primary -> img_sort (1=ปก)
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว (รหัส '.$generatedCode.')');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // แก้: ใช้ pd_sp_id หรือ findOrFail($id) ถ้า Model set primaryKey แล้ว
        $productSalepage = ProductSalepage::with(['images', 'options'])->where('pd_sp_id', $id)->firstOrFail();
        $products = ProductSalepage::where('pd_sp_id', '!=', $id)->orderBy('pd_sp_name')->get();

        return view('admin.products.edit', [
            'productSalepage' => $productSalepage,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();

        // 1. ตรวจสอบข้อมูล
        $this->validateSalePage($request, $productSalepage);

        // 2. อัปเดตข้อมูล (Map ให้ตรง DB)
        $productSalepage->pd_sp_name = $request->pd_sp_name;
        $productSalepage->pd_sp_price = $request->pd_sp_price;
        $productSalepage->pd_sp_discount = $request->pd_sp_discount ?? 0;
        $productSalepage->pd_sp_description = $request->pd_sp_details; // แก้: details -> description

        // ✅ [แก้ไขจุดที่ 2] อัปเดตจำนวนสินค้า (Stock)
        $productSalepage->pd_sp_stock = $request->pd_sp_stock;

        $productSalepage->pd_sp_active = $request->boolean('pd_sp_active');
        $productSalepage->is_recommended = $request->boolean('is_recommended');
        $productSalepage->is_bogo_active = $request->boolean('is_bogo_active');
        $productSalepage->pd_sp_display_location = $request->pd_sp_display_location;

        $productSalepage->save();

        // Sync relationships
        $productSalepage->options()->sync($request->options ?? []);
        $productSalepage->bogoFreeOptions()->sync($request->bogo_options ?? []);

        // 3. จัดการรูปภาพใหม่ (ถ้ามี)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');
                $productSalepage->images()->create([
                    'img_path' => $path,  // แก้: img_path
                    'img_sort' => 0,       // รูปใหม่มาทีหลัง ไม่ใช่ปก
                ]);
            }
        }

        // 4. จัดการเลือกรูปหลัก (Primary Image)
        if ($request->has('is_primary')) { // is_primary คือค่า ID ของรูปที่จะตั้งเป็นปก
            $newPrimaryId = $request->is_primary;

            // รีเซ็ตทุกรูปเป็น 0
            $productSalepage->images()->update(['img_sort' => 0]);

            // ตั้งค่ารูปที่เลือกเป็น 1
            // หมายเหตุ: ต้องตรวจสอบว่า Model Image ชื่อคอลัมน์ PK คืออะไร (สมมติ img_id)
            DB::table('product_images') // ใช้ DB table ตรงๆ เพื่อความชัวร์ หรือใช้ Model ก็ได้
                ->where('img_id', $newPrimaryId)
                ->update(['img_sort' => 1]);
        }

        return redirect()->route('admin.products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $productSalepage = ProductSalepage::where('pd_sp_id', $id)->firstOrFail();
        $productSalepage->delete(); // รูปควรลบ Auto ถ้าตั้ง Cascade ไว้ใน DB

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }

    // ฟังก์ชันสำหรับลบรูปภาพ (AJAX)
    public function destroyImage($imageId)
    {
        // ใช้ DB Facade ดึง path มาลบไฟล์ก่อน
        $image = DB::table('product_images')->where('img_id', $imageId)->first();

        if ($image) {
            if (Storage::disk('public')->exists($image->img_path)) {
                Storage::disk('public')->delete($image->img_path);
            }

            DB::table('product_images')->where('img_id', $imageId)->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Image not found']);
    }

    /**
     * Validation Rules
     */
    private function validateSalePage(Request $request, ?ProductSalepage $salePage = null): array
    {
        return $request->validate([
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_price' => 'required|numeric|min:0',
            'pd_sp_discount' => 'nullable|numeric|min:0',

            // ✅ [แก้ไขจุดที่ 3] เพิ่ม Validation สำหรับ Stock
            'pd_sp_stock' => 'required|integer|min:0',

            'pd_sp_details' => 'nullable|string', // รับค่าจากฟอร์มชื่อ details
            'pd_sp_active' => 'required|boolean', // หรือบางทีส่งมาเป็น 1/0
            'is_recommended' => 'nullable', // รับ nullable เพราะ checkbox ถ้าไม่ติ๊กจะไม่ส่งค่ามา
            'is_bogo_active' => 'nullable',
            'pd_sp_display_location' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:65536',
            'options' => 'nullable|array',
            'bogo_options' => 'nullable|array',
        ]);
    }
}
