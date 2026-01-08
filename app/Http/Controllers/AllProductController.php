<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. เริ่มต้น Query จากตาราง product_salepage
        $query = DB::table('product_salepage')
            ->select(
                // ดึง pd_code จากตาราง salepage เป็นหลัก
                'product_salepage.pd_code', 
                'product_salepage.pd_sp_price as pd_price', // ราคาขายจาก salepage (แก้ไข)
                'product_salepage.pd_sp_discount',        // ส่วนลดจาก salepage

                // ดึงรายละเอียดอื่นๆ จากตาราง product
                'product.pd_id',
                'product.pd_name',
                'product.pd_img',
                'product.pd_full_price'                   // ราคาเต็ม (อาจจะยังใช้แสดง)
            )
            // 2. เชื่อมตารางด้วย pd_code
            ->leftJoin('product', 'product_salepage.pd_code', '=', 'product.pd_code')
            
            // 3. กรองเฉพาะรายการที่ Active ใน salepage
            ->where('product_salepage.pd_sp_active', 1);

        // --- Search Logic (ค้นหาจากชื่อสินค้า) ---
        if ($request->has('search') && $request->search != '') {
            $query->where('product.pd_name', 'like', '%' . $request->search . '%');
        }

        // --- Category Logic (ถ้ามี) ---
        if ($request->has('category') && $request->category != '') {
             // $query->where(...) 
        }

        // 4. Group By เพื่อป้องกันข้อมูลซ้ำ
        $products = $query->groupBy(
            'product_salepage.pd_code',
            'product_salepage.pd_sp_price',
            'product_salepage.pd_sp_discount',
            'product.pd_id',
            'product.pd_name',
            'product.pd_img',
            'product.pd_full_price'
        )
        // เรียงลำดับ
        ->orderBy('product.pd_id', 'desc') 
        ->paginate(12);

        // ดึง Category (ตัวอย่าง)
        $categories = ["Electronics", "Books", "Clothing", "Home & Kitchen"]; 

        return view('allproducts', compact('products', 'categories'));
    }
}