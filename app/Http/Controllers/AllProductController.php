<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllProductController extends Controller
{
    // ในไฟล์ app/Http/Controllers/AllProductController.php

    public function index(Request $request)
    {
        $query = DB::table('product_salepage as ps')
            ->select(
                'ps.pd_sp_id as pd_id',
                'ps.pd_sp_id',
                'ps.pd_sp_code as pd_code',   // แก้เป็น pd_sp_code
                'ps.pd_sp_name as pd_name',
                'ps.pd_sp_price as pd_price',
                'ps.pd_sp_discount',
                'img.img_path as pd_img'      // แก้เป็น img_path
            )
            // แก้ไขส่วน Join ตารางรูปภาพ
            ->leftJoin('product_images as img', function ($join) {
                $join->on('ps.pd_sp_id', '=', 'img.pd_sp_id') // แก้เป็น pd_sp_id
                    ->where('img.img_sort', '=', 1);         // แก้เป็น img_sort
            })
            ->where('ps.pd_sp_active', 1);
        // ->where('ps.pd_sp_display_location', 'general'); // ปิดไว้ก่อนถ้ายังไม่มีคอลัมน์นี้

        // --- Search Logic ---
        if ($request->has('search') && $request->search != '') {
            $query->where('ps.pd_sp_name', 'like', '%'.$request->search.'%');
        }

        $products = $query->orderBy('ps.pd_sp_id', 'desc')
            ->paginate(12);

        $categories = ['Electronics', 'Books', 'Clothing', 'Home & Kitchen'];

        return view('allproducts', compact('products', 'categories'));
    }
}
