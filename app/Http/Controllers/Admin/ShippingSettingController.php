<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingSettingController extends Controller
{
    /**
     * Display the shipping settings mockup page.
     */
    public function index()
    {
        // Mock data for the view
        $shippingMethods = [
            [
                'id' => 1,
                'name' => 'Flash Express',
                'code' => 'flash',
                'is_active' => true,
                'base_rate' => 35,
                'free_shipping_threshold' => 1000,
                'type' => 'flat', // flat, weight, distance
            ],
            [
                'id' => 2,
                'name' => 'Kerry Express',
                'code' => 'kerry',
                'is_active' => true,
                'base_rate' => 45,
                'free_shipping_threshold' => 1500,
                'type' => 'flat',
            ],
            [
                'id' => 3,
                'name' => 'J&T Express',
                'code' => 'j&t',
                'is_active' => false,
                'base_rate' => 30,
                'free_shipping_threshold' => 800,
                'type' => 'flat',
            ],
            [
                'id' => 4,
                'name' => 'Thailand Post (EMS)',
                'code' => 'thailand_post_ems',
                'is_active' => true,
                'base_rate' => 50,
                'free_shipping_threshold' => null,
                'type' => 'weight',
            ]
        ];

        return view('admin.shipping.index', compact('shippingMethods'));
    }

    /**
     * Mock update method.
     */
    public function update(Request $request)
    {
        return back()->with('success', 'บันทึกการตั้งค่าค่าจัดส่งเรียบร้อยแล้ว (Mockup)');
    }
}
