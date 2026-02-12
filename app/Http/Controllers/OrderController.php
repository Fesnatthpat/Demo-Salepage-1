<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('details.productSalepage')->orderBy('created_at', 'desc')->paginate(10);

        return view('orderhistory', compact('orders'));
    }

    public function show($orderCode)
    {
        $order = Order::with('details.productSalepage') // Load order details and their related products
            ->where('ord_code', $orderCode)
            ->firstOrFail(); // Throw 404 if not found

        // Optional: Add authorization check to ensure only the owner can view their order
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('orderdetail', compact('order')); // Assuming 'orderdetail.blade.php'
    }

    public function showTrackingForm(Request $request)
    {
        return view('order.tracking_form');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_code' => 'required|string',
        ]);

        $order = Order::where('ord_code', $request->order_code)->first();

        if ($order) {
            return view('order.tracking_form', compact('order'));
        }

        return back()->with('error', 'ไม่พบข้อมูลการสั่งซื้อสำหรับรหัสที่ระบุ');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'cart_items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. สร้าง Order หลัก
            $order = Order::create([
                'user_id' => Auth::id() ?? 0,
                'ord_date' => now(),
                'ord_code' => 'ORD-'.strtoupper(uniqid()),
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'status' => 'pending',
            ]);

            // 2. บันทึกรายละเอียดสินค้า
            foreach ($request->cart_items as $item) {
                $item = (object) $item;
                $attributes = (object) ($item->attributes ?? []);

                // Extract option name from cart item name if it exists
                $optionName = null;
                if (str_contains($item->name, '(') && str_contains($item->name, ')')) {
                    preg_match('/\((.*?)\)/', $item->name, $matches);
                    $optionName = $matches[1] ?? null;
                }

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'pd_id' => $attributes->product_id ?? $item->id,
                    'option_name' => $optionName,
                    'ordd_price' => $item->price,
                    'ordd_original_price' => $attributes->original_price ?? $item->price,
                    'ordd_count' => $item->quantity,
                    'ordd_discount' => $attributes->discount ?? 0,
                    'ordd_create_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }
}
