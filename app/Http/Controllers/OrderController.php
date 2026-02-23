<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * แสดงหน้ารายการคำสั่งซื้อทั้งหมด (สำหรับฝั่งลูกค้า)
     */
    public function index()
    {
        // ดึงข้อมูลรายการคำสั่งซื้อของ User ที่ Login อยู่ เรียงจากล่าสุดไปเก่าสุด
        $orders = Order::where('user_id', Auth::id() ?? 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // แบ่งหน้า หน้าละ 10 รายการ

        // ชี้ไปที่ไฟล์ resources/views/orderhistory.blade.php
        return view('orderhistory', compact('orders'));
    }

    /**
     * แสดงหน้ารายละเอียดของคำสั่งซื้อนั้นๆ
     */
    public function show($ord_code)
    {
        // ค้นหาออเดอร์จาก ord_code ถ้าไม่พบจะแสดงหน้า 404 Not Found
        $order = Order::where('ord_code', $ord_code)->firstOrFail();

        // ชี้ไปที่ไฟล์ resources/views/orderdetail.blade.php
        return view('orderdetail', compact('order'));
    }

    /**
     * บันทึกคำสั่งซื้อใหม่ และส่ง API เข้า CRM ทันที
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลเบื้องต้นให้ครอบคลุม เพื่อป้องกัน Error
        $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'cart_items' => 'required|array',
            'customer_name' => 'nullable|string|max:255',
            'total_price' => 'nullable|numeric|min:0',
            'province' => 'nullable|string',
            'amphure' => 'nullable|string',
            'district' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 2. บันทึก Order ลงฐานข้อมูลเราเองก่อน
            $order = Order::create([
                'user_id' => Auth::id() ?? 0,
                'ord_date' => now(),
                'ord_code' => 'ORD-'.strtoupper(uniqid()),
                'shipping_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'net_amount' => $request->total_price ?? 0,
                'status_id' => 1, // Default pending status
            ]);

            // บันทึกรายละเอียดสินค้า
            foreach ($request->cart_items as $item) {
                $item = (object) $item;
                $attributes = (object) ($item->attributes ?? []);

                $optionName = null;
                if (str_contains($item->name, '(') && str_contains($item->name, ')')) {
                    preg_match('/\((.*?)\)/', $item->name, $matches);
                    $optionName = $matches[1] ?? null;
                }

                $productId = $attributes->product_id ?? $item->id;

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'pd_id' => $productId,
                    'option_name' => $optionName,
                    'ordd_price' => $item->price,
                    'ordd_original_price' => $attributes->original_price ?? $item->price,
                    'ordd_count' => $item->quantity,
                    'ordd_discount' => $attributes->discount ?? 0,
                    'ordd_create_date' => now(),
                ]);
            }

            DB::commit();

            // 3. เตรียมข้อมูลที่อยู่สำหรับส่งไป CRM
            $addressData = [
                'province' => $request->province ?? '',
                'amphure' => $request->amphure ?? '',
                'district' => $request->district ?? '',
                'postal_code' => $request->postal_code ?? '',
                'customer_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                'payment_method' => $request->payment_method ?? 'Prepaid',
                'shipping_method' => $request->shipping_method ?? 'Standard Delivery',
            ];

            // 4. ⭐ ส่งข้อมูลเข้า CRM ทันที (ไม่ต้องรอคิว) โดยใช้ dispatchSync ⭐
            \App\Jobs\SendOrderToApiJob::dispatchSync($order, $addressData);

            // 5. เมื่อทำงานเสร็จ ให้ส่งกลับไปหน้าประวัติ/รายละเอียดออเดอร์
            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();

            // บันทึก Log เอาไว้เผื่อมีปัญหา จะได้ตามหาสาเหตุเจอ
            Log::error('Order Creation Failed: '.$e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }

    /**
     * Show the form for tracking an order.
     */
    public function showTrackingForm()
    {
        return view('ordertracking');
    }

    /**
     * Track an order based on the tracking code.
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:255',
        ]);

        $order = Order::where('ord_code', $request->tracking_code)->first();

        return view('ordertracking', [
            'order' => $order,
            'tracking_code' => $request->tracking_code,
        ]);
    }
}
