<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product; // ถ้าไม่ได้ใช้ model นี้แล้ว สามารถลบออกได้
use App\Models\ProductSalepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
                    ->orWhere('pd_code', 'like', $searchTerm);
            });
        }

        // Status Filter
        if ($request->has('status') && in_array($request->status, ['0', '1'])) {
            $query->where('pd_sp_active', $request->status);
        }

        $products = $query->paginate(10); // เปลี่ยนชื่อตัวแปรให้สื่อความหมาย (products)

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create', [
            'productSalepage' => new ProductSalepage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล (ตัด pd_code ออก)
        $validatedData = $this->validateSalePage($request);

        // 2. ระบบรันรหัสสินค้าอัตโนมัติ (Auto Generate)
        // หา ID ล่าสุด เพื่อนำมา +1
        $lastProduct = ProductSalepage::latest('pd_sp_id')->first();
        $nextId = $lastProduct ? ($lastProduct->pd_sp_id + 1) : 1;
        // สร้างรหัส P-00001, P-00002, ...
        $validatedData['pd_code'] = 'P-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // 3. บันทึกข้อมูลสินค้าลงฐานข้อมูล
        $salePage = ProductSalepage::create($validatedData);

        // 4. จัดการอัปโหลดรูปภาพ
        if ($request->hasFile('images')) {
            foreach($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');
                $salePage->images()->create([
                    'image_path' => $path,
                    'is_primary' => false // ค่าเริ่มต้นไม่ใช่รูปหลัก
                ]);
            }
            
            // ตั้งรูปแรกเป็นรูปหลักถ้ายังไม่มี
            if($salePage->images()->exists()) {
                $firstImage = $salePage->images()->first();
                $firstImage->update(['is_primary' => true]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'สร้างสินค้าใหม่เรียบร้อยแล้ว (รหัส ' . $validatedData['pd_code'] . ')');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $productSalepage = ProductSalepage::with('images')->findOrFail($id);
        
        return view('admin.products.edit', [
            'productSalepage' => $productSalepage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $productSalepage = ProductSalepage::findOrFail($id);
        
        // 1. ตรวจสอบข้อมูล
        $validatedData = $this->validateSalePage($request, $productSalepage);
        
        // 2. อัปเดตข้อมูล
        $productSalepage->update($validatedData);

        // 3. จัดการรูปภาพใหม่ (ถ้ามี)
        if ($request->hasFile('images')) {
            foreach($request->file('images') as $file) {
                $path = $file->store('product_images', 'public');
                $productSalepage->images()->create([
                    'image_path' => $path
                ]);
            }
        }
        
        // 4. จัดการเลือกรูปหลัก (Primary Image)
        if ($request->has('is_primary')) {
            // รีเซ็ตทุกรูปให้ไม่เป็นหลักก่อน
            $productSalepage->images()->update(['is_primary' => false]);
            // ตั้งค่ารูปที่เลือกเป็นหลัก
            $productSalepage->images()->where('img_pd_id', $request->is_primary)->update(['is_primary' => true]);
        }

        return redirect()->route('admin.products.index')->with('success', 'อัปเดตข้อมูลสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $productSalepage = ProductSalepage::findOrFail($id);
        
        // ลบรูปภาพใน Storage (Optional: ถ้าต้องการลบไฟล์จริงด้วย)
        /*
        foreach($productSalepage->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        */

        $productSalepage->delete();

        return back()->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
    
    // ฟังก์ชันสำหรับลบรูปภาพ (AJAX)
    public function destroyImage($imageId)
    {
        // โค้ดส่วนนี้ต้องสร้าง Model Image หรือเรียกผ่าน Relation ก็ได้
        // สมมติว่ามี Model ProductImage
        $image = \App\Models\ProductImage::find($imageId); 
        // หรือถ้าไม่มี Model แยก ใช้ DB::table ก็ได้ แต่แนะนำให้สร้าง Model ครับ
        
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
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
            // ตัด pd_code ออก เพราะเราสร้างเอง
            'pd_sp_name' => 'required|string|max:255',
            'pd_sp_price' => 'required|numeric|min:0',
            'pd_sp_discount' => 'nullable|numeric|min:0', // ไม่ต้องมี lte:price ก็ได้เผื่อแจกฟรี
            'pd_sp_details' => 'nullable|string',
            'pd_sp_active' => 'required|boolean',
            'pd_sp_display_location' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:65536' // 64MB per file
        ]);
    }
}