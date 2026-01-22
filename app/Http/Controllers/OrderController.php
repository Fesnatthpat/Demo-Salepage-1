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
                'ord_code' => 'ORD-' . strtoupper(uniqid()),
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'status' => 'pending',
            ]);

            // 2. บันทึกรายละเอียดสินค้า
            foreach ($request->cart_items as $item) {
                $item = (object) $item;

                OrderDetail::create([
                    'ord_id'              => $order->id,
                    'pd_id'               => $item->id,
                    
                    // ✅ ใช้ชื่อคอลัมน์ที่ถูกต้องจากรูปภาพ
                    'ordd_price'          => $item->price,
                    'ordd_original_price' => $item->original_price ?? $item->price,
                    'ordd_count'          => $item->quantity,
                    'ordd_discount'       => $item->discount ?? 0,
                    
                    'ordd_create_date'    => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('orders.show', $order->ord_code)
                             ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}