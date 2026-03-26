<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSalepage; // ★ ต้องมีบรรทัดนี้เพื่อเรียกใช้ Model สินค้า
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected \App\Services\OrderService $orderService;

    public function __construct(\App\Services\OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * แสดงหน้ารายการคำสั่งซื้อทั้งหมด (สำหรับฝั่งลูกค้า)
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id() ?? 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orderhistory', compact('orders'));
    }

    /**
     * แสดงหน้ารายละเอียดของคำสั่งซื้อนั้นๆ
     */
    public function show($ord_code)
    {
        $order = Order::where('ord_code', $ord_code)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orderdetail', compact('order'));
    }

    /**
     * บันทึกคำสั่งซื้อใหม่ (ตอนลูกค้ากดสั่งซื้อครั้งแรก)
     */
    public function store(Request $request)
    {
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
            $selectedItems = array_map(function($item) {
                return $item['attributes']['product_id'] ?? $item['id'];
            }, $request->cart_items);

            $order = $this->orderService->createOrder(
                $request->all(),
                Auth::user(),
                $selectedItems
            );

            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สร้างคำสั่งซื้อเรียบร้อยแล้ว กรุณาแนบสลิปเพื่อยืนยัน');

        } catch (\Exception $e) {
            Log::error('Order Creation Failed: '.$e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }

    /**
     * อัปโหลดสลิป และ อัปเดตยอดขาย (Sold Count)
     */
    public function uploadSlip(Request $request, $ord_code)
    {
        // 1. Validate ไฟล์
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $order = Order::where('ord_code', $ord_code)->firstOrFail();

        try {
            DB::beginTransaction();
            // เช็คสถานะก่อนอัปเดต ว่าเคยจ่ายเงินแล้วหรือยัง (เพื่อป้องกันการตัดสต็อก/เพิ่มยอดซ้ำ)
            $alreadyPaid = ($order->status_id >= Order::STATUS_PAID); 

            if ($request->hasFile('slip_image')) {
                // 2. บันทึกไฟล์ (UUID เพื่อความปลอดภัย)
                $extension = $request->file('slip_image')->getClientOriginalExtension();
                $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
                $path = $request->file('slip_image')->storeAs('slips', $filename, 'public');

                // 3. อัปเดตออเดอร์
                $order->slip_path = $path;
                $order->status_id = Order::STATUS_PAID; 
                $order->save();

                // ★★★ 4. ถ้าเป็นการจ่ายครั้งแรก ให้ตัดสต็อกและเพิ่มยอดขาย ★★★
                if (! $alreadyPaid) {
                    $this->orderService->finalizeOrder($order);
                }

                DB::commit();

                // 5. ส่งข้อมูลเข้า CRM
                if (class_exists(\App\Jobs\SendOrderToApiJob::class)) {
                    \App\Jobs\SendOrderToApiJob::dispatchSync($order);
                }

                return back()->with('success', 'อัปโหลดสลิปและบันทึกยอดขายเรียบร้อยแล้ว!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Slip Upload Failed: '.$e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }

        return back()->with('error', 'กรุณาเลือกไฟล์รูปภาพ');
    }
}
