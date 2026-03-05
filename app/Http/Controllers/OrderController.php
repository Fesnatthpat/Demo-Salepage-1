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
        $order = Order::where('ord_code', $ord_code)->firstOrFail();

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
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::id() ?? 0,
                'ord_date' => now(),
                'ord_code' => 'ORD-'.strtoupper(uniqid()),
                'shipping_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'net_amount' => $request->total_price ?? 0,
                'status_id' => 1, // 1 = รอชำระเงิน
            ]);

            foreach ($request->cart_items as $item) {
                $item = (object) $item;
                $attributes = (object) ($item->attributes ?? []);

                $optionName = null;
                if (str_contains($item->name, '(') && str_contains($item->name, ')')) {
                    preg_match('/\((.*?)\)/', $item->name, $matches);
                    $optionName = $matches[1] ?? null;
                }

                $productId = $attributes->product_id ?? $item->id;
                $optionId = $attributes->option_id ?? null;

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'pd_id' => $productId,
                    'option_id' => $optionId,
                    'option_name' => $optionName,
                    'ordd_price' => $item->price,
                    'ordd_original_price' => $attributes->original_price ?? $item->price,
                    'ordd_count' => $item->quantity,
                    'ordd_discount' => $attributes->discount ?? 0,
                    'ordd_create_date' => now(),
                ]);
            }

            DB::commit();

            // ข้อมูลสำหรับส่ง CRM
            $addressData = [
                'province' => $request->province ?? '',
                'amphure' => $request->amphure ?? '',
                'district' => $request->district ?? '',
                'postal_code' => $request->postal_code ?? '',
                'customer_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                'payment_method' => $request->payment_method ?? 'Prepaid',
                'shipping_method' => $request->shipping_method ?? 'Shopee SPX Express',
            ];

            if (class_exists(\App\Jobs\SendOrderToApiJob::class)) {
                \App\Jobs\SendOrderToApiJob::dispatchSync($order, $addressData);
            }

            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สร้างคำสั่งซื้อเรียบร้อยแล้ว กรุณาแนบสลิปเพื่อยืนยัน');

        } catch (\Exception $e) {
            DB::rollBack();
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
            if ($request->hasFile('slip_image')) {

                $alreadyPaid = ($order->status_id >= 2);

                // 2. บันทึกไฟล์
                $path = $request->file('slip_image')->store('slips', 'public');

                // 3. อัปเดตออเดอร์
                $order->slip_path = $path;
                $order->status_id = 2; // 2 = ชำระเงินแล้ว/รอตรวจสอบ
                $order->save();

                // ★★★ 4. ถ้าเป็นการจ่ายครั้งแรก ให้ตัดสต็อกและเพิ่มยอดขาย ★★★
                if (! $alreadyPaid) {

                    // A. ตัดสต็อก
                    if (method_exists($this->orderService, 'deductStock')) {
                        $this->orderService->deductStock($order);
                    }

                    // B. เพิ่มยอดขาย (Sold Count)
                    $orderDetails = OrderDetail::where('ord_id', $order->id)->get();

                    foreach ($orderDetails as $detail) {
                        $product = ProductSalepage::find($detail->pd_id);

                        if ($product) {
                            $product->increment('pd_sp_sold', $detail->ordd_count);
                        }
                    }
                }

                // 5. ส่งข้อมูลเข้า CRM
                if (class_exists(\App\Jobs\SendOrderToApiJob::class)) {
                    \App\Jobs\SendOrderToApiJob::dispatchSync($order);
                }

                return back()->with('success', 'อัปโหลดสลิปและบันทึกยอดขายเรียบร้อยแล้ว!');
            }
        } catch (\Exception $e) {
            Log::error('Slip Upload Failed: '.$e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }

        return back()->with('error', 'กรุณาเลือกไฟล์รูปภาพ');
    }
}
