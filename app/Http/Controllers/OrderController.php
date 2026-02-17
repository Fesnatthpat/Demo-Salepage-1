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
     * แสดงหน้ารายการคำสั่งซื้อทั้งหมด (แก้ไข Error Method index does not exist)
     */
    public function index()
    {
        // ดึงข้อมูลรายการคำสั่งซื้อเรียงจากล่าสุดไปเก่าสุด (สมมติว่าดึงเฉพาะของ User ที่ Login)
        // หากต้องการดึงทั้งหมดให้ใช้ Order::orderBy('created_at', 'desc')->get();
        $orders = Order::where('user_id', Auth::id() ?? 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // แบ่งหน้า หน้าละ 10 รายการ

        // ส่งข้อมูลไปยังหน้า View ที่ชื่อว่า resources/views/orderhistory.blade.php
        return view('orderhistory', compact('orders'));
    }

    /**
     * แสดงหน้ารายละเอียดของคำสั่งซื้อนั้นๆ (จำเป็นต้องมีเพราะฟังก์ชัน store มีการ redirect มาหา)
     */
    public function show($ord_code)
    {
        // ค้นหาออเดอร์จาก ord_code ถ้าไม่พบจะแสดงหน้า 404 Not Found
        $order = Order::where('ord_code', $ord_code)->firstOrFail();

        // ส่งข้อมูลไปยังหน้า View ที่ชื่อว่า resources/views/orders/show.blade.php
        return view('orders.show', compact('order'));
    }

    /**
     * บันทึกคำสั่งซื้อใหม่ (โค้ดเดิมของคุณ)
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
                $productSku = $product->pd_sp_SKU ?? $product->pd_sp_code ?? 'UNKNOWN';

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

            // 4. ส่งข้อมูลตามโครงสร้างฟิลด์ที่ CRM ต้องการ
            $payload = [
                [
                    'address' => $request->address,
                    'amphure' => $request->amphure ?? 'ไม่ได้ระบุ',
                    'channel_name' => 'Sale Page',
                    'customer_name' => $request->customer_name ?? 'ลูกค้าทั่วไป',
                    'district' => $request->district ?? 'ไม่ได้ระบุ',
                    'net_amount' => (float) ($request->total_price ?? 0),
                    'order_date' => now()->format('Y-m-d H:i:s'),
                    'order_id' => $order->ord_code,
                    'tracking_number' => '',
                    'payment_date' => now()->format('Y-m-d H:i:s'),
                    'payment_method' => $request->payment_method ?? 'Prepaid',
                    'phone_number1' => $request->phone,
                    'phone_number2' => '',
                    'postal_code' => $request->postal_code ?? '00000',
                    'province' => $request->province ?? 'ไม่ได้ระบุ',
                    'shipping_method' => $request->shipping_method ?? 'Standard Delivery',
                    'social_name' => '',
                    'store_name' => 'Sale Page',
                    'order_upload_status' => '',
                    'comp_id' => 1,
                    'items' => $apiItems,
                ],
            ];

            // ยิง API แบบ Real-time (ไม่ผ่าน Job เพื่อเช็คผลทันที)
            try {
                $response = Http::timeout(15)->asJson()->post('https://demo.kawinbrothers.com/api/v1/create-order.php', $payload);

                if ($response->successful()) {
                    Log::info('✅ CRM Success: '.$order->ord_code);
                } else {
                    // หากไม่สำเร็จ ให้บันทึกเหตุผลที่ CRM ตอบกลับมาลงใน Log
                    Log::error('❌ CRM Error: '.$response->status().' - '.$response->body());
                }
            } catch (\Exception $apiError) {
                Log::error('💥 API Connection Failed: '.$apiError->getMessage());
            }

            return redirect()->route('orders.show', $order->ord_code)
                ->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }
}
