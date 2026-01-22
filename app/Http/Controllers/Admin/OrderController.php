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
        // เริ่มต้น Query พร้อมโหลดข้อมูล User เพื่อลดจำนวน Query (N+1 Problem)
        $query = Order::with('user');

        // 1. ระบบค้นหา (Search)
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ord_code', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%");
            });
        }

        // 2. ระบบกรองสถานะ (Filter Status)
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_id', $request->status);
        }

        // 3. เรียงลำดับ (ล่าสุดขึ้นก่อน) และแบ่งหน้า
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // ส่งตัวแปร $orders ไปยัง View
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * แสดงรายละเอียดออเดอร์ (สำหรับหน้า show)
     * ✅ แก้ไข: ส่งตัวแปร $order ไปยัง View เพื่อแก้ Error "Undefined variable"
     */
    public function show($id)
    {
        // 1. ดึงข้อมูลออเดอร์ตาม ID
        // สำคัญ: ใช้ with(...) เพื่อดึงข้อมูลสินค้าในออเดอร์และรูปภาพสินค้ามาด้วย
        $order = Order::with([
            'user',                             // ข้อมูลลูกค้า
            'details.productSalepage.images',    // ข้อมูลสินค้าและรูปภาพ (สำหรับแสดงในตารางสินค้า)
        ])->findOrFail($id);

        // 2. รายการสถานะ สำหรับ Dropdown เปลี่ยนสถานะ
        $statuses = [
            1 => 'รอชำระเงิน',
            2 => 'แจ้งชำระเงินแล้ว',
            3 => 'กำลังเตรียมจัดส่ง',
            4 => 'จัดส่งแล้ว',
            5 => 'ยกเลิก',
        ];

        // 3. ส่งข้อมูล $order และ $statuses ไปยังหน้า View
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * อัปเดตสถานะออเดอร์
     */
    public function updateStatus(Request $request, $id)
    {
        // ตรวจสอบค่าที่ส่งมา
        $request->validate([
            'status_id' => 'required|integer|in:1,2,3,4,5',
        ]);

        // ค้นหาและบันทึก
        $order = Order::findOrFail($id);
        $order->status_id = $request->status_id;
        $order->save();

        // เด้งกลับหน้าเดิมพร้อมแจ้งเตือน
        return redirect()->back()->with('success', 'อัปเดตสถานะออเดอร์เรียบร้อยแล้ว');
    }
}
