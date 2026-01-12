<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->orderBy('ord_date', 'desc')
                        ->get();

        return view('orderhistory', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  string  $orderCode
     * @return \Illuminate\View\View
     */
    public function show(string $orderCode)
    {
        $order = Order::with('details', 'details.productSalepage.images')
                        ->where('ord_code', $orderCode)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        return view('orderdetail', compact('order'));
    }

    /**
     * Display the order tracking form.
     *
     * @return \Illuminate\View\View
     */
    public function showTrackingForm()
    {
        return view('ordertracking');
    }

    /**
     * Track an order by its code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string|min:10',
            'phone' => 'required|string',
        ], [
            'order_code.required' => 'กรุณากรอกรหัสคำสั่งซื้อ',
            'order_code.min' => 'รหัสคำสั่งซื้อไม่ถูกต้อง',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
        ]);

        $orderCode = $request->input('order_code');
        $phone = $request->input('phone');

        $order = Order::with('details', 'details.productSalepage.images')
                        ->where('ord_code', $orderCode)
                        ->where('shipping_phone', $phone) // Validate phone as a second factor
                        ->first();

        if (!$order) {
            // Generic error to prevent leaking which field was wrong
            return back()->withInput()->with('error', 'ไม่พบข้อมูลคำสั่งซื้อ กรุณาตรวจสอบรหัสคำสั่งซื้อและเบอร์โทรศัพท์อีกครั้ง');
        }

        return view('ordertracking', compact('order'));
    }
}
