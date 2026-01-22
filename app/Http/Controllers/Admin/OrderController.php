<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * แสดงรายการออเดอร์ทั้งหมด (สำหรับหน้า index)
     */
    public function index(Request $request)
    {
        // โหลด details.productSalepage เพื่อเอาราคาจริง (pd_sp_price) มาใช้คำนวณแก้ขัด
        $query = Order::with(['user', 'details.productSalepage']);

        // 1. ระบบค้นหา
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ord_code', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%");
            });
        }

        // 2. กรองสถานะ
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_id', $request->status);
        }

        // 3. เรียงลำดับ
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * แสดงรายละเอียดออเดอร์ (สำหรับหน้า show)
     */
    public function show($id)
    {
        // โหลดข้อมูลสินค้าและรูปภาพมาแสดงผล
        $order = Order::with([
            'user',
            'details.productSalepage.images',
        ])->findOrFail($id);

        $statuses = [
            1 => 'รอชำระเงิน',
            2 => 'แจ้งชำระเงินแล้ว',
            3 => 'กำลังเตรียมจัดส่ง',
            4 => 'จัดส่งแล้ว',
            5 => 'ยกเลิก',
        ];

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * อัปเดตสถานะออเดอร์
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|integer|in:1,2,3,4,5',
        ]);

        $order = Order::findOrFail($id);
        $order->status_id = $request->status_id;
        $order->save();

        return redirect()->back()->with('success', 'อัปเดตสถานะออเดอร์เรียบร้อยแล้ว');
    }
}
