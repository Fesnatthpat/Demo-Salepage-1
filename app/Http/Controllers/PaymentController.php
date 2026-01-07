<?php

namespace App\Http\Controllers;

use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Province;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon; // เรียกใช้ Carbon สำหรับจัดการเวลา

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

        foreach ($cartContent as $item) {
            if (in_array((string) $item->id, $selectedItems)) {
                $cartItems[] = $item;
                $totalAmount += ($item->price * $item->quantity);
            }
        }

        $addresses = DeliveryAddress::where('user_id', auth()->id())->get();
        $provinces = Province::all();

        return view('payment', compact('cartItems', 'totalAmount', 'addresses', 'selectedItems', 'provinces'));
    }

    // [Step 2] Process Order
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
            $totalPrice = 0;
            $itemsToBuy = [];

            foreach ($cartContent as $item) {
                if (in_array((string) $item->id, $selectedItems)) {
                    $itemsToBuy[] = $item;
                    $totalPrice += ($item->price * $item->quantity);
                }
            }

            if (count($itemsToBuy) === 0) {
                throw new \Exception('ไม่พบสินค้าที่เลือกในตะกร้า');
            }

            $shippingCost = 0;
            $totalDiscount = 0;
            $netAmount = ($totalPrice + $shippingCost) - $totalDiscount;

            $address = DeliveryAddress::with(['province', 'amphure', 'district'])->find($request->address_id);
            $fullAddress = $address->address_line1.' '.
                           ($address->address_line2 ? $address->address_line2.' ' : '').
                           ($address->district->name_th ?? '').' '.
                           ($address->amphure->name_th ?? '').' '.
                           ($address->province->name_th ?? '').' '.
                           $address->zipcode;

            $orderCode = 'ORD-'.date('YmdHis').'-'.rand(100, 999);

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

            $createdOrder = Order::where('ord_code', $orderCode)->first();

            if (!$createdOrder) {
                throw new \Exception('Failed to create order.');
            }

            foreach ($itemsToBuy as $item) {
                $itemDiscount = isset($item->attributes['discount']) ? $item->attributes['discount'] : 0;
                OrderDetail::create([
                    'ord_id' => $createdOrder->ord_id,
                    'user_id' => $userId,
                    'pd_id' => $item->id,
                    'pd_price' => $item->price,
                    'ordd_count' => $item->quantity,
                    'pd_sp_discount' => $itemDiscount,
                    'ordd_create_date' => now(),
                ]);
                Cart::session($userId)->remove($item->id);
            }

            $remainingCartData = Cart::session($userId)->getContent();
            if ($remainingCartData->isEmpty()) {
                CartStorage::where('user_id', $userId)->delete();
            } else {
                CartStorage::updateOrCreate(['user_id' => $userId], ['cart_data' => $remainingCartData]);
            }

            DB::commit();
            return redirect()->route('payment.qr', ['orderId' => $orderCode]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    // [Step 3] Show QR Code with Expiration Logic
    public function showQr($orderId)
    {
        $order = Order::where('ord_code', $orderId)->where('user_id', Auth::id())->firstOrFail();

        // 1. คำนวณเวลาที่เหลือ (15 นาที จาก updated_at)
        // เราใช้ updated_at เพราะเวลา Refresh เราจะอัปเดตฟิลด์นี้
        $expireTime = $order->updated_at->addMinutes(1);
        $secondsRemaining = now()->diffInSeconds($expireTime, false);

        // ถ้าหมดเวลาแล้ว (ค่าน้อยกว่า 0) ให้ส่งค่า 0 ไป
        if ($secondsRemaining < 0) {
            $secondsRemaining = 0;
        }

        // 2. สร้าง PromptPay Payload
        $promptpayTarget = '0980744060'; // ★ แก้เบอร์โทรตรงนี้
        $amount = $order->net_amount;
        $payload = $this->generatePromptPayPayload($promptpayTarget, $amount);

        // 3. สร้าง QR Code (SVG)
        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($payload));

        return view('qr', compact('order', 'qrCodeBase64', 'secondsRemaining'));
    }

    // [New Function] ปุ่ม Refresh QR Code
    public function refreshQr($orderCode)
    {
        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        // เช็คว่าสถานะยังรอจ่ายเงินอยู่ไหม (status_id = 1)
        if ($order->status_id == 1) {
            // "แตะ" (Touch) ออเดอร์ เพื่ออัปเดต updated_at เป็นเวลาปัจจุบัน
            // เวลา 15 นาทีจะเริ่มนับใหม่จากจุดนี้
            $order->touch(); 
            
            return redirect()->route('payment.qr', ['orderId' => $orderCode])
                             ->with('success', 'รีเฟรช QR Code เรียบร้อยแล้ว');
        }

        return back()->with('error', 'ไม่สามารถรีเฟรชรายการนี้ได้');
    }

    // [Step 4] Upload Slip
    public function uploadSlip(Request $request, $orderCode)
    {
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        // เช็คเวลาหมดอายุ (15 นาทีจาก updated_at)
        if (now()->greaterThan($order->updated_at->addMinutes(1))) {
            return back()->with('error', 'หมดเวลาชำระเงิน กรุณากดปุ่มรีเฟรชเพื่อขอ QR Code ใหม่');
        }

        if ($request->hasFile('slip_image')) {
            $path = $request->file('slip_image')->store('slips', 'public');
            $order->slip_path = $path;
            $order->status_id = 2; // แจ้งชำระเงินแล้ว
            $order->save();

            return redirect()->route('order.show', ['orderCode' => $order->ord_code])
                             ->with('success', 'แนบสลิปเรียบร้อยแล้ว');
        }

        return back()->with('error', 'เกิดข้อผิดพลาด');
    }

    // Private Helper Functions
    private function generatePromptPayPayload(string $mobile, float $amount): string
    {
        $formattedMobile = $mobile;
        if (strlen($mobile) === 10 && str_starts_with($mobile, "0")) {
            $formattedMobile = "0066" . substr($mobile, 1);
        }
        $amountStr = number_format($amount, 2, '.', '');
        $amountLength = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);
        $payload = "000201"."010212"."2937"."0016A000000677010111"."0113".$formattedMobile."5802TH"."5303764"."54".$amountLength.$amountStr;
        $checksumPayload = $payload . "6304";
        return $checksumPayload . $this->crc16_php($checksumPayload);
    }

    private function crc16_php(string $payload): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, "0", STR_PAD_LEFT));
    }
}