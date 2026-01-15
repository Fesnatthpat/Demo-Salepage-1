<?php

namespace App\Http\Controllers;

use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Province;
use App\Services\PromptPayService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    // [Step 1] Checkout Page
    public function checkout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        }

        $cartContent = Cart::session(auth()->id())->getContent();
        $cartItems = [];
        $totalAmount = 0;
        $totalDiscount = 0; // Initialize total discount
        $totalOriginalAmount = 0; // Initialize total original amount

        foreach ($cartContent as $item) {
            if (in_array((string) $item->id, $selectedItems)) {
                $cartItems[] = $item;
                $totalAmount += ($item->price * $item->quantity);

                // Calculate original price for this item (similar to cart.blade.php)
                $originalPrice = $item->attributes->has('original_price')
                    ? $item->attributes->original_price
                    : $item->price;

                $totalOriginalAmount += ($originalPrice * $item->quantity);
                $totalDiscount += ($item->attributes->discount ?? 0); // Sum up fixed per-item discount
            }
        }

        $addresses = DeliveryAddress::where('user_id', auth()->id())->get();
        $provinces = Province::all();

        return view('payment', compact('cartItems', 'totalAmount', 'totalDiscount', 'totalOriginalAmount', 'addresses', 'selectedItems', 'provinces'));
    }

    // [Step 2] Process Order (Create)
    public function process(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:delivery_addresses,id',
            'selected_items' => 'required|array|min:1',
        ]);

        $userId = Auth::id();
        $selectedItems = $request->input('selected_items', []);
        $cartContent = Cart::session($userId)->getContent();

        DB::beginTransaction();

        try {
            // คำนวณยอดเงิน
            $totalPrice = 0;
            $totalDiscount = 0; // Initialize totalDiscount
            $itemsToBuy = [];
            foreach ($cartContent as $item) {
                if (in_array((string) $item->id, $selectedItems)) {
                    $itemsToBuy[] = $item;
                    $totalPrice += ($item->price * $item->quantity);
                    $totalDiscount += ($item->attributes['discount'] ?? 0); // Sum up fixed per-item discount (not multiplied by quantity)
                }
            }

            if (count($itemsToBuy) === 0) {
                throw new \Exception('ไม่พบสินค้า');
            }

            $shippingCost = 0;
            // $totalDiscount is now correctly calculated
            $netAmount = $totalPrice + $shippingCost;

            // ดึงที่อยู่
            $address = DeliveryAddress::with(['province', 'amphure', 'district'])->find($request->address_id);
            $fullAddress = $address->address_line1.' '.($address->address_line2 ?? '').' '.($address->district->name_th ?? '').' '.($address->amphure->name_th ?? '').' '.($address->province->name_th ?? '').' '.$address->zipcode;
            if (! empty($address->note)) {
                $fullAddress .= "\nหมายเหตุ: ".$address->note;
            }

            $orderCode = 'ORD-'.date('YmdHis').'-'.rand(100, 999);

            // สร้าง Order
            $order = Order::create([
                'ord_code' => $orderCode,
                'user_id' => $userId,
                'total_price' => $totalPrice,
                'shipping_cost' => $shippingCost,
                'total_discount' => $totalDiscount,
                'net_amount' => $netAmount,
                'ord_date' => now(),
                'status_id' => 1,
                'shipping_name' => $address->fullname,
                'shipping_phone' => $address->phone,
                'shipping_address' => $fullAddress,
            ]);

            // บันทึก Order Detail
            foreach ($itemsToBuy as $item) {
                OrderDetail::create([
                    // ★★★ แก้ไขจุดนี้: เปลี่ยนจาก ord_id เป็น id ★★★
                    'ord_id' => $order->id, // ใช้ ->id ซึ่งเป็น Primary Key ปกติของ Laravel หลัง create
                    'user_id' => $userId,
                    'pd_id' => $item->id,
                    'pd_price' => $item->price,
                    'pd_original_price' => $item->attributes['original_price'] ?? $item->price,
                    'ordd_count' => $item->quantity,
                    'pd_sp_discount' => $item->attributes['discount'] ?? 0,
                    'ordd_create_date' => now(),
                ]);
                Cart::session($userId)->remove($item->id);
            }

            // Update Storage
            $remaining = Cart::session($userId)->getContent();
            if ($remaining->isEmpty()) {
                CartStorage::where('user_id', $userId)->delete();
            } else {
                CartStorage::updateOrCreate(['user_id' => $userId], ['cart_data' => $remaining]);
            }

            DB::commit();

            return redirect()->route('payment.qr', ['orderId' => $orderCode]);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', ' '.$e->getMessage());
        }
    }

    // [Step 3] Show QR Code
    public function showQr($orderId, PromptPayService $promptPayService)
    {
        $order = Order::where('ord_code', $orderId)->where('user_id', Auth::id())->firstOrFail();

        // ★ ตั้งเวลาหมดอายุ 15 นาที จากเวลาอัปเดตล่าสุด
        $expireTime = $order->updated_at->addMinutes(15);
        $secondsRemaining = now()->diffInSeconds($expireTime, false);
        if ($secondsRemaining < 0) {
            $secondsRemaining = 0;
        }

        // สร้าง Payload พร้อมเพย์ผ่าน Service
        $promptpayTarget = env('PROMPTPAY_ACCOUNT', '0812345678');
        $payload = $promptPayService->generatePayload($promptpayTarget, $order->net_amount);

        // ★ สร้าง QR เป็น SVG เพื่อแก้ปัญหา Driver Error
        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($payload));

        return view('qr', compact('order', 'qrCodeBase64', 'secondsRemaining'));
    }

    // [New] Refresh QR Code Logic
    public function refreshQr($orderCode)
    {
        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status_id == 1) {
            // อัปเดตเวลา updated_at เป็นปัจจุบัน เพื่อเริ่มนับ 15 นาทีใหม่
            $order->touch();

            return redirect()->route('payment.qr', ['orderId' => $orderCode])->with('success', 'รีเฟรช QR Code แล้ว');
        }

        return back()->with('error', 'ไม่สามารถรีเฟรชได้');
    }

    // [Step 4] Upload Slip
    public function uploadSlip(Request $request, $orderCode)
    {
        $request->validate(['slip_image' => 'required|image|max:5120']); // Max 5MB

        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        // ตรวจสอบเวลาหมดอายุ (ต้องตรงกับใน showQr)
        if (now()->greaterThan($order->updated_at->addMinutes(15))) {
            return back()->with('error', 'หมดเวลาชำระเงิน กรุณากดปุ่มรีเฟรช');
        }

        if ($request->hasFile('slip_image')) {
            // ★ บันทึกไฟล์ลง Storage
            $path = $request->file('slip_image')->store('slips', 'public');

            // ★ บันทึก Path ลง Database
            $order->slip_path = $path;
            $order->status_id = 2; // แจ้งชำระเงินแล้ว
            $order->save();

            return redirect()->route('order.show', ['orderCode' => $order->ord_code])
                ->with('success', 'แนบสลิปเรียบร้อยแล้ว');
        }

        return back()->with('error', 'อัปโหลดไม่สำเร็จ');
    }
}
