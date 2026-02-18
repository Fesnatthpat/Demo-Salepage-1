<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        // ✅ ชี้ไปที่ไฟล์ resources/views/orderhistory.blade.php
        return view('orderhistory', compact('orders'));
    }

    /**
     * แสดงหน้ารายละเอียดของคำสั่งซื้อนั้นๆ
     */
    public function show($ord_code)
    {
        // ค้นหาออเดอร์จาก ord_code ถ้าไม่พบจะแสดงหน้า 404 Not Found
        $order = Order::where('ord_code', $ord_code)->firstOrFail();

        // ✅ ชี้ไปที่ไฟล์ resources/views/orderdetail.blade.php
        return view('orderdetail', compact('order'));
    }

    /**
     * บันทึกคำสั่งซื้อใหม่ และส่ง API เข้า CRM
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลเบื้องต้น
        $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'cart_items' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 2. บันทึก Order ลงฐานข้อมูลเราเองก่อน
            $order = Order::create([
                'user_id' => Auth::id() ?? 0,
                'ord_date' => now(),
                'ord_code' => 'ORD-'.strtoupper(uniqid()),
                'shipping_phone' => $request->phone,
                'shipping_address' => $request->address,
                'total_price' => $request->total_price ?? 0,
                'status' => 'pending',
            ]);

            $apiItems = [];

            // 3. บันทึกรายละเอียดสินค้าและเตรียมข้อมูล SKU ให้ตรงกับ CRM
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

                // ค้นหา SKU ที่ถูกต้องจากตาราง product_salepage
                $product = DB::table('product_salepage')->where('pd_sp_id', $productId)->first();
                $productSku = 'UNKNOWN'; // ✅ Default SKU
                if ($product) {
                    $productSku = $product->pd_sp_SKU ?? $product->pd_sp_code ?? 'UNKNOWN';
                } else {
                    Log::warning("SKU not found for product_id: {$productId}");
                }

                if ($optionName) {
                    $productSku .= '['.$optionName.']';
                }

                $apiItems[] = [
                    'product_sku' => (string) $productSku,
                    'price_per_item' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                ];
            }

            DB::commit();

            // 4. เตรียมข้อมูล Payload สำหรับ CRM (✅ ครอบด้วย Array [] ชั้นนอกสุดให้เหมือน Postman)
            $payload = [
                [
                    'address' => $request->address,
                    'amphure' => $request->amphure ?? '',
                    'channel_name' => 'Sale Page',
                    'customer_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                    'district' => $request->district ?? '',
                    'net_amount' => (float) ($request->total_price ?? 0),
                    'order_date' => now()->format('Y-m-d H:i:s'),
                    'order_id' => $order->ord_code,
                    'tracking_number' => '',
                    'payment_date' => now()->format('Y-m-d H:i:s'),
                    'payment_method' => $request->payment_method ?? 'Prepaid',
                    'phone_number1' => $request->phone,
                    'phone_number2' => '',
                    'postal_code' => $request->postal_code ?? '',
                    'province' => $request->province ?? '',
                    'shipping_method' => $request->shipping_method ?? 'Standard Delivery',
                    'social_name' => '',
                    'store_name' => 'Sale Page',
                    'order_upload_status' => '',
                    'comp_id' => 1,
                    'items' => $apiItems,
                ],
            ];

            // 5. ยิง API และบันทึก Log อย่างละเอียด
            try {
                // Token ของคุณ
                $apiToken = 'cFVubW9zWUJyU3R4bDZhcXNiYjo1c21nNHJ1T1VDOVYzaHRabDNhdFNxVTcwN0RQVmpYUXUy';

                // ✅ 5.1: Log ข้อมูลทั้งหมดที่จะส่งไป
                Log::channel('daily')->info('CRM Payload for Order: '.$order->ord_code, $payload);

                $response = Http::withoutVerifying()    // ป้องกันปัญหา SSL error ใน Localhost
                    ->withToken($apiToken)  // ✅ แนบ Token เข้าไปใน Header แบบ Bearer
                    ->timeout(15)
                    ->asJson() // บังคับส่งแบบ Content-Type: application/json
                    ->post('https://demo.kawinbrothers.com/api/v1/create-order.php', $payload);

                // ✅ 5.2: Log คำตอบทั้งหมดที่ได้รับจาก CRM เสมอ
                Log::channel('daily')->info('CRM Response for Order: '.$order->ord_code, [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->json() ?? $response->body(),
                ]);

                if ($response->successful()) {
                    Log::channel('daily')->info('✅ CRM Task Completed Successfully for Order: '.$order->ord_code);
                } else {
                    Log::channel('daily')->error('❌ CRM Task Failed with non-2xx status for Order: '.$order->ord_code);
                }
            } catch (\Exception $apiError) {
                // ✅ 5.3: Log Error กรณีที่เชื่อมต่อไม่ได้
                Log::channel('daily')->error('💥 API Connection Failed for Order: '.$order->ord_code, [
                    'message' => $apiError->getMessage(),
                    'trace' => $apiError->getTraceAsString(),
                ]);
            }

            // 6. เมื่อทำงานเสร็จ ให้ส่งกลับไปหน้าประวัติ/รายละเอียดออเดอร์
            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();

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
