<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail; // ✅ เพิ่ม Import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ✅ เพิ่ม Import สำหรับ Transaction

class OrderController extends Controller
{
    /**
     * 1. ฟังก์ชันบันทึกข้อมูลการสั่งซื้อ (Checkout)
     * ฟังก์ชันนี้จะแก้ปัญหา Error: Unknown column 'user_id'
     */
    public function store(Request $request)
    {
        // 1. Validate ข้อมูลเบื้องต้น
        $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'cart_items' => 'required|array', // รับข้อมูลสินค้าเป็น Array
        ]);

        try {
            DB::beginTransaction(); // เริ่ม Transaction เพื่อความปลอดภัยของข้อมูล

            // 2. สร้าง Order หลัก (Header)
            $order = Order::create([
                'user_id' => Auth::id() ?? 0, // เก็บ user_id ที่นี่เท่านั้น
                'ord_date' => now(),
                'ord_code' => 'ORD-'.strtoupper(uniqid()), // ตัวอย่างการสร้างเลข Order
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'status' => 'pending',
                // ใส่ field อื่นๆ ตามตาราง orders ของคุณ
            ]);

            // 3. วนลูปบันทึกรายละเอียดสินค้า (Detail)
            foreach ($request->cart_items as $item) {
                // แปลงเป็น Object หากรับมาเป็น Array
                $item = (object) $item;

                OrderDetail::create([
                    'ord_id' => $order->id,                 // ✅ ผูกกับ Order หลัก
                    'pd_id' => $item->id,                  // รหัสสินค้า
                    'pd_price' => $item->price,               // ราคาขาย
                    'pd_original_price' => $item->original_price ?? $item->price,
                    'ordd_count' => $item->quantity,            // จำนวน
                    'pd_sp_discount' => $item->discount ?? 0,       // ส่วนลด
                    'ordd_create_date' => now(),

                    // ❌ ไม่ใส่ user_id ตรงนี้เด็ดขาด (แก้ปัญหา Error)
                ]);
            }

            DB::commit(); // บันทึกสำเร็จ

            // 4. ส่งกลับไปหน้าสำเร็จ หรือหน้า Tracking
            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack(); // ถ้ามี Error ให้ยกเลิกทั้งหมด

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }

    // --------------------------------------------------------------------------
    // ส่วนเดิมของคุณ (สำหรับการดูประวัติและ Tracking) ไม่ต้องแก้ไข ใช้งานได้เลย
    // --------------------------------------------------------------------------

    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::with('details.productSalepage.images')
            ->where('user_id', Auth::id())
            ->orderBy('ord_date', 'desc')
            ->get();

        return view('orderhistory', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(string $orderCode)
    {
        $order = Order::with('details', 'details.productSalepage.images')
            ->where('ord_code', $orderCode)
            // ถ้าเป็น Guest อาจต้องเอา where user_id ออก หรือเช็คเงื่อนไขเพิ่ม
            ->when(Auth::check(), function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->firstOrFail();

        return view('orderdetail', compact('order'));
    }

    /**
     * Display the order tracking form.
     */
    public function showTrackingForm()
    {
        return view('ordertracking');
    }

    /**
     * Track an order by its code.
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
            ->where('shipping_phone', $phone)
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', 'ไม่พบข้อมูลคำสั่งซื้อ กรุณาตรวจสอบรหัสคำสั่งซื้อและเบอร์โทรศัพท์อีกครั้ง');
        }

        return view('ordertracking', compact('order'));
    }
}
