<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        // [แก้ไข 1] เริ่มต้น Query จากตาราง product_salepage (เหมือนหน้า Index)
        $query = DB::table('product_salepage')
            ->select(
                'product_salepage.pd_id as ps_pd_id', // Alias product_salepage's pd_id to avoid conflict
                'product_salepage.pd_code as ps_pd_code',
                'product_salepage.pd_sp_discount', // Discount from salepage
                'product.pd_id',
                'product.pd_code',
                'product.pd_name',
                'product.pd_img',
                'product.pd_price',      // Current selling price from product
                'product.pd_full_price'  // Full price from product
            )
            // [แก้ไข 2] Left Join ไปหาตาราง product (สินค้า)
            ->leftJoin('product', 'product_salepage.pd_id', '=', 'product.pd_id')
            
            // [แก้ไข 3] กรองเฉพาะรายการที่ Active ใน salepage และ product ต้องเปิดขาย
            ->where('product_salepage.pd_sp_active', 1); // Only filter salepage active, product status will be null if not found

        // --- Search Logic ---
        if ($request->has('search') && $request->search != '') {
            $query->where('product.pd_name', 'like', '%' . $request->search . '%');
        }

        // --- Category Logic (ถ้ามี) ---
        if ($request->has('category') && $request->category != '') {
             // $query->where(...) 
        }

        // [แก้ไข 4] Group By ตามจำนวน field ที่ Select มาให้ครบ
        $products = $query->groupBy(
            'product_salepage.pd_id', // Group by salepage pd_id
            'product_salepage.pd_code', // Group by salepage pd_code
            'product_salepage.pd_sp_discount',
            'product.pd_id',
            'product.pd_code',
            'product.pd_name',
            'product.pd_img',
            'product.pd_price',
            'product.pd_full_price'
        )
        ->orderBy('product.pd_id', 'desc')
        ->paginate(12);

        // ดึง Category (ตัวอย่าง)
        $categories = ["Electronics", "Books", "Clothing", "Home & Kitchen"]; 

        return view('allproducts', compact('products', 'categories'));
    }
}