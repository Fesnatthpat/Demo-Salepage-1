<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = DB::table('product_salepage as ps')
            ->select(
                'ps.pd_sp_id as pd_id',
                'ps.pd_code',
                'ps.pd_sp_name as pd_name',
                'ps.pd_sp_price as pd_price',
                'ps.pd_sp_discount',
                'img.image_path as pd_img'
            )
            ->leftJoin('image_product as img', function ($join) {
                $join->on('ps.pd_sp_id', '=', 'img.product_id')
                     ->where('img.is_primary', '=', 1);
            })
            ->where('ps.pd_sp_active', 1)
            ->where('ps.pd_sp_display_location', 'homepage')
            ->orderBy('ps.pd_sp_id', 'desc')
            ->limit(4)
            ->get();

        return view('index', compact('recommendedProducts'));
    }
}