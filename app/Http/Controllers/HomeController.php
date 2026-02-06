<?php

namespace App\Http\Controllers;

use App\Models\ProductSalepage;

class HomeController extends Controller
{
    public function index()
    {
        $recommendedProducts = ProductSalepage::with('images')
            ->where('pd_sp_active', 1)
            ->where('is_recommended', 1) // Using the correct field for recommended products
            ->orderBy('pd_sp_id', 'desc')
            ->limit(8)
            ->get();

        return view('index', compact('recommendedProducts'));
    }

    public function about()
    {
        return view('about');
    }
}
