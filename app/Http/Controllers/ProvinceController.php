<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{
    public function index()
    {
        // provinces table มาตรฐาน: id, name_th
        $provinces = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th')
            ->get();

        return view('province.index', compact('provinces'));
    }

    public function apiProvinces()
    {
        return DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th')
            ->get();
    }
}
