<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // ในไฟล์ app/Http/Controllers/HomeController.php

    public function index()
    {
        $recommendedProducts = DB::table('product_salepage as ps')
            ->select(
                'ps.pd_sp_id as pd_id',
                'ps.pd_sp_code as pd_code',   // แก้ pd_code เป็น pd_sp_code
                'ps.pd_sp_name as pd_name',
                'ps.pd_sp_price as pd_price',
                'ps.pd_sp_discount',
                'img.img_path as pd_img'      // แก้ image_path เป็น img_path
            )
            // แก้ไขส่วน Join ตารางรูปภาพ
            ->leftJoin('product_images as img', function ($join) {
                $join->on('ps.pd_sp_id', '=', 'img.pd_sp_id') // แก้ product_id เป็น pd_sp_id
                    ->where('img.img_sort', '=', 1);         // แก้ is_primary เป็น img_sort (ใช้เลข 1 แทนรูปปก)
            })
            ->where('ps.pd_sp_active', 1)
            // ->where('ps.pd_sp_display_location', 'homepage') // บรรทัดนี้ถ้ายังไม่ได้เพิ่มคอลัมน์ใน DB ให้ใส่ // ปิดไว้ก่อนครับ ไม่งั้นจะ Error
            ->orderBy('ps.pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        return view('index', compact('recommendedProducts'));
    }
}
