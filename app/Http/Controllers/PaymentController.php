<?php

namespace App\Http\Controllers;

use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSalepage;
use App\Models\Province;
use App\Services\PromptPayService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Darryldecode\Cart\ItemCollection; // เรียกใช้ Class ให้ถูกต้อง
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    // [Step 1] Checkout Page
    public function checkout(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);
        $selectedFreebies = $request->input('selected_freebies', []);

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        }

        $cartContent = Cart::session(auth()->id())->getContent();
        $cartItems = collect();

        foreach ($cartContent as $item) {
            if (in_array((string) $item->id, $selectedItems)) {
                $cartItems->push($item);
            }
        }

        // --- Freebie Logic (Preview) ---
        if (! empty($selectedFreebies)) {
            // ถ้า selected_freebies มาเป็น string (เช่นจาก query param) ให้แปลงเป็น array
            if (is_string($selectedFreebies)) {
                $selectedFreebies = explode(',', $selectedFreebies);
            }

            $freebieProducts = ProductSalepage::with('images')->whereIn('pd_sp_id', $selectedFreebies)->get();

            foreach ($freebieProducts as $freebie) {
                // จัดการ Path รูปภาพ
                $img = $freebie->images->first();
                $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
                if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                    $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
                }

                // สร้าง Dummy Item เพื่อแสดงผลหน้า Checkout
                $freebieCartItem = new ItemCollection([
                    'id' => $freebie->pd_sp_id,
                    'name' => $freebie->pd_sp_name.' (ของแถม)',
                    'price' => 0,
                    'quantity' => 1,
                    'attributes' => new Collection([
                        'original_price' => (float) $freebie->pd_sp_price,
                        'cover_image_url' => $imgPath,
                        'is_freebie' => true,
                    ]),
                    'associatedModel' => $freebie,
                ]);

                $cartItems->push($freebieCartItem);
            }
        }

        // คำนวณยอดรวม (ของแถมราคา 0 ไม่กระทบยอดเงิน แต่กระทบส่วนลดถ้าคิดตาม Logic เดิม)
        $totalAmount = 0;
        $totalDiscount = 0;
        $totalOriginalAmount = 0;

        foreach ($cartItems as $item) {
            // เช็คสต็อก (เฉพาะสินค้าหลัก)
            if (! $item->attributes->get('is_freebie')) {
                $product = ProductSalepage::find($item->id);
                if (! $product || $product->pd_sp_stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', "สินค้า '{$item->name}' หมดสต็อก");
                }
            }

            $totalAmount += ($item->price * $item->quantity);

            $originalPrice = $item->attributes['original_price'] ?? $item->price;
            $totalOriginalAmount += ($originalPrice * $item->quantity);

            // ส่วนลด = ราคาเต็ม - ราคาขายจริง
            $totalDiscount += (($originalPrice - $item->price) * $item->quantity);
        }

        $addresses = DeliveryAddress::where('user_id', auth()->id())->get();
        $provinces = Province::all();
        $productIds = $cartItems->pluck('id')->toArray();
        $products = ProductSalepage::whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');

        return view('payment', compact('cartItems', 'totalAmount', 'totalDiscount', 'totalOriginalAmount', 'addresses', 'selectedItems', 'selectedFreebies', 'provinces', 'products'));
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
        $selectedFreebies = $request->input('selected_freebies', []); // รับค่าของแถมมาด้วย

        $cartContent = Cart::session($userId)->getContent();

        DB::beginTransaction();

        try {
            $subTotalPrice = 0;
            $finalTotalPrice = 0;
            $itemsToBuy = [];

            // 1. จัดการสินค้าหลักในตะกร้า
            foreach ($cartContent as $item) {
                if (in_array((string) $item->id, $selectedItems)) {
                    $itemsToBuy[] = $item;
                    $finalTotalPrice += ($item->price * $item->quantity);

                    $originalPrice = $item->attributes['original_price'] ?? $item->price;
                    $subTotalPrice += ($originalPrice * $item->quantity);
                }
            }

            // 2. ★★★ [แก้ไขใหม่] จัดการของแถม ★★★
            // ดึงข้อมูลของแถมจาก Database ตาม ID ที่ส่งมา
            $freebieItemsToSave = [];
            if (! empty($selectedFreebies)) {
                if (is_string($selectedFreebies)) {
                    $selectedFreebies = explode(',', $selectedFreebies);
                }

                $freebieProducts = ProductSalepage::whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $freebie) {
                    $freebieItemsToSave[] = $freebie;
                    // ของแถม: เพิ่มราคาต้นลงไปใน subTotal แต่ไม่เพิ่มใน finalTotal (เพราะฟรี)
                    $subTotalPrice += $freebie->pd_sp_price;
                }
            }

            $totalDiscount = $subTotalPrice - $finalTotalPrice;

            if (count($itemsToBuy) === 0) {
                throw new \Exception('ไม่พบรายการสินค้าที่จะสั่งซื้อ');
            }

            $shippingCost = 0;
            $netAmount = $finalTotalPrice;

            // ดึงที่อยู่
            $address = DeliveryAddress::with(['province', 'amphure', 'district'])->find($request->address_id);
            $fullAddress = $address->address_line1.' '.($address->address_line2 ?? '').' '.($address->district->name_th ?? '').' '.($address->amphure->name_th ?? '').' '.($address->province->name_th ?? '').' '.$address->zipcode;
            if (! empty($address->note)) {
                $fullAddress .= "\nหมายเหตุ: ".$address->note;
            }

            $orderCode = 'ORD-'.date('YmdHis').'-'.rand(100, 999);

            // สร้าง Order หลัก
            $order = Order::create([
                'ord_code' => $orderCode,
                'user_id' => $userId,
                'total_price' => $subTotalPrice,
                'shipping_cost' => $shippingCost,
                'total_discount' => $totalDiscount,
                'net_amount' => $netAmount,
                'ord_date' => now(),
                'status_id' => 1,
                'shipping_name' => $address->fullname,
                'shipping_phone' => $address->phone,
                'shipping_address' => $fullAddress,
            ]);

            // 3.1 บันทึก Order Detail (สินค้าหลัก) และตัดสต็อก
            foreach ($itemsToBuy as $item) {
                $product = ProductSalepage::where('pd_sp_id', $item->id)->lockForUpdate()->first();

                if (! $product) {
                    throw new \Exception("ไม่พบสินค้า ID {$item->id}");
                }
                if ($product->pd_sp_stock < $item->quantity) {
                    throw new \Exception("สินค้า '{$item->name}' หมดสต็อก");
                }

                $product->decrement('pd_sp_stock', $item->quantity);

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'user_id' => $userId,
                    'pd_id' => $item->id,
                    'ordd_price' => $item->price, // ราคาขายจริง
                    'ordd_original_price' => $item->attributes['original_price'] ?? $item->price,
                    'ordd_count' => $item->quantity,
                    'ordd_discount' => ($item->attributes['original_price'] ?? $item->price) - $item->price,
                    'ordd_create_date' => now(),
                ]);
                Cart::session($userId)->remove($item->id);
            }

            // 3.2 ★★★ [แก้ไขใหม่] บันทึก Order Detail (ของแถม) และตัดสต็อก ★★★
            foreach ($freebieItemsToSave as $freebie) {
                // Lock เพื่อตัดสต็อกของแถมด้วย
                $product = ProductSalepage::where('pd_sp_id', $freebie->pd_sp_id)->lockForUpdate()->first();

                // เช็คสต็อกของแถม (สมมติแจก 1 ชิ้น)
                if (! $product || $product->pd_sp_stock < 1) {
                    throw new \Exception("ขออภัย ของแถม '{$freebie->pd_sp_name}' หมดสต็อก");
                }
                $product->decrement('pd_sp_stock', 1);

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'user_id' => $userId,
                    'pd_id' => $freebie->pd_sp_id,
                    'ordd_price' => 0, // ของแถมราคาขาย 0
                    'ordd_original_price' => $freebie->pd_sp_price,
                    'ordd_count' => 1,
                    'ordd_discount' => $freebie->pd_sp_price, // ส่วนลดเท่าราคาเต็ม
                    'ordd_create_date' => now(),
                ]);
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

            return back()->with('error', 'เกิดข้อผิดพลาด: '.$e->getMessage());
        }
    }

    // [Step 3] Show QR Code
    public function showQr($orderId, PromptPayService $promptPayService)
    {
        $order = Order::where('ord_code', $orderId)->where('user_id', Auth::id())->firstOrFail();

        $expireTime = $order->updated_at->addMinutes(15);
        $secondsRemaining = now()->diffInSeconds($expireTime, false);
        if ($secondsRemaining < 0) {
            $secondsRemaining = 0;
        }

        $promptpayTarget = env('PROMPTPAY_ACCOUNT', '0812345678');
        $payload = $promptPayService->generatePayload($promptpayTarget, $order->net_amount);
        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($payload));

        return view('qr', compact('order', 'qrCodeBase64', 'secondsRemaining'));
    }

    // [Step 3.1] Refresh QR Code
    public function refreshQr($orderCode)
    {
        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status_id == 1) {
            $order->touch();

            return redirect()->route('payment.qr', ['orderId' => $orderCode])->with('success', 'รีเฟรช QR Code แล้ว');
        }

        return back()->with('error', 'ไม่สามารถรีเฟรชได้');
    }

    // [Step 4] Upload Slip
    public function uploadSlip(Request $request, $orderCode)
    {
        $request->validate(['slip_image' => 'required|image|max:5120']);

        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        if (now()->greaterThan($order->updated_at->addMinutes(15))) {
            return back()->with('error', 'หมดเวลาชำระเงิน กรุณากดปุ่มรีเฟรช');
        }

        if ($request->hasFile('slip_image')) {
            $path = $request->file('slip_image')->store('slips', 'public');
            $order->slip_path = $path;
            $order->status_id = 2;
            $order->save();

            return redirect()->route('order.show', ['orderCode' => $order->ord_code])
                ->with('success', 'แนบสลิปเรียบร้อยแล้ว');
        }

        return back()->with('error', 'อัปโหลดไม่สำเร็จ');
    }
}
