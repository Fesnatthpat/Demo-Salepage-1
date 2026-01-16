<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;

class ProductController extends Controller
{
    /**
     * แสดงหน้ารายละเอียดสินค้า
     *
     * @param  int|string  $id  รหัสสินค้า (pd_sp_id)
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        // 1. ดึงข้อมูลสินค้า (ใช้ pd_sp_id เป็นหลัก)
        // พร้อมโหลดข้อมูลรูปภาพ, ตัวเลือกสินค้า, และของแถม
        $salePageProduct = ProductSalepage::with([
            'images',
            'options.images',       // โหลดรูปของตัวเลือกด้วย
            'bogoFreeOptions.images', // โหลดรูปของแถมด้วย
        ])->where('pd_sp_id', $id)->first();

        // 2. ถ้าไม่เจอสินค้า ให้เด้งกลับหน้าแรก
        if (! $salePageProduct) {
            return redirect('/')->with('error', 'ไม่พบสินค้านี้');
        }

        // 3. Logic หา "รูปปก" (Cover Image)
        // ค้นหารูปที่มี img_sort = 1 ก่อน ถ้าไม่มีให้เอารูปแรกสุด
        $primaryImage = $salePageProduct->images->where('img_sort', 1)->first();
        $imagePath = $primaryImage ? $primaryImage->img_path : ($salePageProduct->images->first()->img_path ?? null);

        // 4. ✅ หัวใจสำคัญ: Map ข้อมูลให้หน้าเว็บใช้งานได้ (แก้ Error Missing ID)
        // สร้าง Object ใหม่เพื่อแปลงชื่อ Field จาก Database (pd_sp_...) ให้ตรงกับที่ View ต้องการ (pd_...)
        $product = (object) [
            // --- กลุ่ม ID (สำคัญมากสำหรับ Route ตะกร้า) ---
            'id' => $salePageProduct->pd_sp_id,   // แก้ Error Missing parameter: id
            'pd_id' => $salePageProduct->pd_sp_id,
            'pd_sp_id' => $salePageProduct->pd_sp_id,

            // --- กลุ่มชื่อและรายละเอียด ---
            'pd_name' => $salePageProduct->pd_sp_name, // หน้าเว็บใช้ pd_name
            'pd_sp_name' => $salePageProduct->pd_sp_name,
            'pd_details' => $salePageProduct->pd_sp_description,
            'pd_sp_details' => $salePageProduct->pd_sp_description,

            // --- กลุ่มราคา ---
            'pd_price' => $salePageProduct->pd_sp_price,
            'pd_sp_price' => $salePageProduct->pd_sp_price,
            'pd_sp_discount' => $salePageProduct->pd_sp_discount ?? 0,

            // --- กลุ่มรหัสสินค้า (SKU) ---
            'pd_code' => $salePageProduct->pd_sp_code, // หน้าเว็บใช้ pd_code
            'pd_sp_code' => $salePageProduct->pd_sp_code,

            // --- กลุ่มสต็อก ---
            'quantity' => $salePageProduct->pd_sp_stock ?? 0,

            // --- กลุ่มรูปภาพ ---
            'pd_img' => $imagePath,            // รูปปกเดี่ยวๆ
            'images' => $salePageProduct->images, // อัลบั้มรูปทั้งหมด

            // --- กลุ่มตัวเลือกและโปรโมชั่น ---
            'options' => $salePageProduct->options,
            'is_bogo_active' => $salePageProduct->is_bogo_active ?? 0,
            'bogoFreeOptions' => $salePageProduct->bogoFreeOptions ?? collect(),

            // --- อื่นๆ (ป้องกัน Error Undefined) ---
            'brand_name' => null,
        ];

        // ส่งตัวแปร $product ที่ปรุงสำเร็จแล้วไปที่หน้า view 'product'
        return view('product', compact('product'));
    }
}
