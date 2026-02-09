<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\ProductSalepage;
use App\Models\Promotion;
use App\Models\Province;
use App\Services\OrderService;
use App\Services\PromptPayService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Darryldecode\Cart\ItemCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
            if (is_string($selectedFreebies)) {
                $selectedFreebies = explode(',', $selectedFreebies);
            }

            $freebieProducts = ProductSalepage::with('images')->whereIn('pd_sp_id', $selectedFreebies)->get();

            foreach ($freebieProducts as $freebie) {
                $img = $freebie->images->first();
                $imgPath = $img ? ($img->img_path ?? $img->image_path) : null;
                if ($imgPath && ! filter_var($imgPath, FILTER_VALIDATE_URL)) {
                    $imgPath = asset('storage/'.ltrim(str_replace('storage/', '', $imgPath), '/'));
                }

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

        $totalAmount = 0;
        $totalDiscount = 0;
        $totalOriginalAmount = 0;

        foreach ($cartItems as $item) {
            if (! $item->attributes->get('is_freebie')) {
                $product = ProductSalepage::find($item->id);
                if (! $product || $product->pd_sp_stock < $item->quantity) {
                    return redirect()->route('cart.index')->with('error', "สินค้า '{$item->name}' หมดสต็อก");
                }
            }

            $totalAmount += ($item->price * $item->quantity);
            $originalPrice = $item->attributes['original_price'] ?? $item->price;
            $totalOriginalAmount += ($originalPrice * $item->quantity);
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
        ]);

        $user = Auth::user();
        $guestSessionKey = '_guest_'.session()->getId();

        try {
            $order = $this->orderService->createOrder(
                [
                    'delivery_address_id' => $request->input('delivery_address_id'),
                    'payment_method' => 'promptpay',
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

    // [Apply Discount Logic - Fixed with explicit USE]
    public function applyDiscount(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:255',
                'selected_items' => 'required|array',
                'selected_items.*' => 'numeric',
                'selected_freebies' => 'nullable|array',
                'selected_freebies.*' => 'numeric',
            ]);

            // ประกาศตัวแปร $now
            $now = now();

            $discountCode = trim($request->input('code'));
            $selectedItems = $request->input('selected_items');
            $selectedFreebies = $request->input('selected_freebies', []);
            $userId = auth()->id();

            $success = false;
            $message = 'รหัสส่วนลดไม่ถูกต้องหรือไม่สามารถใช้ได้';
            $fixedDiscountValue = 0;
            $percentageDiscountRate = 0;

            Log::info('applyDiscount: Processing code', ['code' => $discountCode, 'current_time' => $now]);

            // [FIX] ใช้ function($q) use ($now) แทน fn เพื่อป้องกันปัญหา Scope ของตัวแปร
            $promotion = Promotion::where('code', $discountCode)
                ->where('is_discount_code', true)
                ->where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $now);
                })
                ->first();

            if ($promotion) {
                Log::info('applyDiscount: Promotion found', [
                    'promotion_id' => $promotion->id,
                    'is_active' => $promotion->is_active,
                    'start_date' => $promotion->start_date,
                    'end_date' => $promotion->end_date,
                    'current_time' => $now,
                ]);

                $success = true;
                $message = 'ใช้รหัสส่วนลด '.$discountCode.' สำเร็จ!';
                if ($promotion->discount_type === 'fixed') {
                    $fixedDiscountValue = $promotion->discount_value;
                } elseif ($promotion->discount_type === 'percentage') {
                    $percentageDiscountRate = $promotion->discount_value / 100;
                }
            } else {
                Log::warning('applyDiscount: Code invalid or expired', [
                    'code_attempted' => $discountCode,
                    'server_time' => $now->toDateTimeString(),
                    'timezone' => config('app.timezone'),
                ]);
                session()->forget('applied_discount_code');
            }

            if ($success) {
                session(['applied_discount_code' => ['code' => $discountCode, 'fixed' => $fixedDiscountValue, 'percentage' => $percentageDiscountRate]]);
            }

            $cartContent = Cart::session($userId)->getContent();
            $checkoutCartItems = collect();

            foreach ($cartContent as $item) {
                if (in_array((string) $item->id, $selectedItems)) {
                    $checkoutCartItems->push($item);
                }
            }

            if (! empty($selectedFreebies)) {
                $freebieProducts = ProductSalepage::with('images')->whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $freebie) {
                    $checkoutCartItems->push(new ItemCollection([
                        'id' => $freebie->pd_sp_id,
                        'name' => $freebie->pd_sp_name.' (ของแถม)',
                        'price' => 0,
                        'quantity' => 1,
                        'attributes' => new Collection([
                            'original_price' => (float) $freebie->pd_sp_price,
                            'is_freebie' => true,
                        ]),
                        'associatedModel' => $freebie,
                    ]));
                }
            }

            $totalAmount = 0;
            $totalDiscountFromProducts = 0;
            $totalOriginalAmount = 0;

            foreach ($checkoutCartItems as $item) {
                $totalAmount += ($item->price * $item->quantity);
                $originalPrice = $item->attributes['original_price'] ?? $item->price;
                $totalOriginalAmount += ($originalPrice * $item->quantity);
                $totalDiscountFromProducts += (($originalPrice - $item->price) * $item->quantity);
            }

            $additionalDiscountFromCode = 0;
            if ($success) {
                if ($fixedDiscountValue > 0) {
                    $additionalDiscountFromCode = $fixedDiscountValue;
                } elseif ($percentageDiscountRate > 0) {
                    $additionalDiscountFromCode = $totalAmount * $percentageDiscountRate;
                }
            }

            $grandTotal = max(0, $totalAmount - $additionalDiscountFromCode);
            $totalDiscount = $totalDiscountFromProducts + $additionalDiscountFromCode;

            $shippingCost = 0;
            $finalTotal = $grandTotal + $shippingCost;

            return response()->json([
                'success' => $success,
                'message' => $message,
                'totalOriginalAmount' => (float) $totalOriginalAmount,
                'grandTotal' => (float) $grandTotal,
                'shippingCost' => (float) $shippingCost,
                'totalDiscount' => (float) $totalDiscount,
                'finalTotal' => (float) $finalTotal,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง: '.implode(', ', $e->errors()['code'] ?? []),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาดภายใน: '.$e->getMessage()], 500);
        }
    }

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
