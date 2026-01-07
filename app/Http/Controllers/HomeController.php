<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = DB::table('product_salepage')
            ->select(
                'product.pd_id',
                'product.pd_code',
                'product.pd_name',
                'product.pd_price',
                'product.pd_img',
                'product_salepage.pd_sp_discount',
                'product.pd_full_price' // <--- เลือกมาแล้ว
            )
            ->join('product', 'product_salepage.pd_id', '=', 'product.pd_id')
            ->leftJoin('brand', 'product.brand_id', '=', 'brand.brand_id')
            ->where('product_salepage.pd_sp_active', 1)
            ->where('product.pd_status', 1)

            ->groupBy(
                'product.pd_id',
                'product.pd_code',
                'product.pd_name',
                'product.pd_price',
                'product.pd_img',
                'product_salepage.pd_sp_discount',
                'product.pd_full_price' // <--- [จุดที่แก้] ต้องเพิ่มบรรทัดนี้ใน groupBy ด้วยครับ
            )
            ->orderBy('product.pd_id', 'desc')
            ->limit(4)
            ->get();

        return view('index', compact('recommendedProducts'));
    }
}