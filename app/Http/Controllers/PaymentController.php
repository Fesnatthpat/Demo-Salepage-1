<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\ProductSalepage;
use App\Models\Province;
use App\Services\OrderService;
use App\Services\PromptPayService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Darryldecode\Cart\ItemCollection; // เรียกใช้ Class ให้ถูกต้อง
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    protected OrderService $orderService;

    protected PromptPayService $promptPayService;

    public function __construct(OrderService $orderService, PromptPayService $promptPayService)
    {
        $this->orderService = $orderService;
        $this->promptPayService = $promptPayService;
    }

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
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'selected_items' => 'required|array|min:1',
            // Add validation for payment_method if needed
        ]);

        $user = Auth::user();
        $guestSessionKey = '_guest_'.session()->getId();

        try {
            $order = $this->orderService->createOrder(
                [
                    'delivery_address_id' => $request->input('delivery_address_id'),
                    'payment_method' => 'promptpay', // Assuming promptpay for now, adjust as needed
                ],
                $user,
                $guestSessionKey
            );

            return redirect()->route('payment.qr', ['orderId' => $order->ord_code]);

        } catch (\Exception $e) {
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
