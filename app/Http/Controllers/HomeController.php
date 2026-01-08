<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = DB::table('product_salepage')
            ->select(
                'product_salepage.pd_code', // เลือก code จากตาราง salepage เป็นหลัก
                'product_salepage.pd_sp_price as pd_price', // << แก้ไข
                'product_salepage.pd_sp_discount',
                'product.pd_id',
                'product.pd_name',
                'product.pd_img',
                'product.pd_full_price'
            )
            // ★★★ แก้ไขจุดที่ 1: เปลี่ยนมาเชื่อมด้วย pd_code ตามหน้า AllProducts ★★★
            ->leftJoin('product', 'product_salepage.pd_code', '=', 'product.pd_code')
            
            // เชื่อม Brand (ถ้าจำเป็น)
            ->leftJoin('brand', 'product.brand_id', '=', 'brand.brand_id')
            
            // กรองเฉพาะที่เปิดใช้งานใน Salepage
            ->where('product_salepage.pd_sp_active', 1)
            
            // (Optional) ถ้าต้องการเช็คว่าสินค้าหลักต้องเปิดขายด้วย ให้เปิดบรรทัดนี้
            // ->where('product.pd_status', 1) 

            ->groupBy(
                'product_salepage.pd_code',
                'product_salepage.pd_sp_price', // << แก้ไข
                'product_salepage.pd_sp_discount',
                'product.pd_id',
                'product.pd_name',
                'product.pd_img',
                'product.pd_full_price'
            )
            // เรียงลำดับจากใหม่ไปเก่า
            ->orderBy('product_salepage.pd_id', 'desc') // ใช้ ID ของ salepage ในการเรียง
            ->limit(4) // ดึงมา 4 รายการ
            ->get();

        return view('index', compact('recommendedProducts'));
    }
}