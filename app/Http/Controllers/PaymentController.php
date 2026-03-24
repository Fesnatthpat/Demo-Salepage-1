<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\ProductSalepage;
use App\Models\Province;
use App\Services\OrderService;
use App\Services\PromptPayService;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Darryldecode\Cart\ItemCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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

    public function checkout(Request $request)
    {
        $userId = auth()->id();
        $cartContent = Cart::session($userId)->getContent();

        $selectedItems = $request->input('selected_items', []);
        $selectedFreebies = $request->input('selected_freebies', []);

        if (empty($selectedItems) && ! $cartContent->isEmpty()) {
            $selectedItems = $cartContent->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        }

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'ขออภัย! กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
        }

        $cartItems = collect();

        foreach ($cartContent as $item) {
            if (in_array((string) $item->id, $selectedItems)) {
                $cartItems->push($item);
            }
        }

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
                $productId = $item->attributes['product_id'] ?? $item->id;
                $optionId = $item->attributes['option_id'] ?? null;

                if ($optionId) {
                    $option = \App\Models\ProductOption::find($optionId);
                    if (! $option || $option->option_stock < $item->quantity) {
                        return redirect()->route('cart.index')->with('error', "สินค้า '{$item->name}' หมดสต็อก");
                    }
                } else {
                    $product = ProductSalepage::find($productId);
                    if (! $product || $product->pd_sp_stock < $item->quantity) {
                        return redirect()->route('cart.index')->with('error', "สินค้า '{$item->name}' หมดสต็อก");
                    }
                }
            }

            $totalAmount += ($item->price * $item->quantity);
            $originalPrice = $item->attributes['original_price'] ?? $item->price;
            $totalOriginalAmount += ($originalPrice * $item->quantity);
            $totalDiscount += (($originalPrice - $item->price) * $item->quantity);
        }

        $cartService = $this->orderService->getCartService();

        $promoDiscount = $cartService->calculateTotalDiscount($totalAmount, $cartItems);

        $totalDiscount += $promoDiscount;
        $totalAmount -= $promoDiscount;

        $addresses = DeliveryAddress::where('user_id', auth()->id())->get();
        $provinces = Province::all();
        $productIds = $cartItems->pluck('id')->toArray();
        $products = ProductSalepage::whereIn('pd_sp_id', $productIds)->get()->keyBy('pd_sp_id');

        return view('payment', compact('cartItems', 'totalAmount', 'totalDiscount', 'totalOriginalAmount', 'addresses', 'selectedItems', 'selectedFreebies', 'provinces', 'products'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'string',
            'selected_freebies' => 'nullable|array',
            'discount_code' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        if ($request->filled('discount_code')) {
            try {
                $this->orderService->getCartService()->applyPromoCode($request->input('discount_code'));
            } catch (\Exception $e) {
                return redirect()->route('cart.index')->with('error', $e->getMessage());
            }
        }

        try {
            $order = $this->orderService->createOrder(
                [
                    'delivery_address_id' => $request->input('delivery_address_id'),
                    'payment_method' => 'promptpay',
                ],
                $user,
                $request->input('selected_items', []),
                $request->input('selected_freebies', [])
            );

            return redirect()->route('payment.qr', ['orderId' => $order->ord_code]);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (str_contains($message, 'ไม่เพียงพอ') || str_contains($message, 'หมดสต็อก') || str_contains($message, 'มีไม่เพียงพอ')) {
                return redirect()->route('cart.index')->with('error', 'ขออภัย: '.$message.' กรุณาตรวจสอบจำนวนสินค้าในตะกร้าอีกครั้ง');
            }

            return back()->with('error', 'เกิดข้อผิดพลาดในการสร้างคำสั่งซื้อ: '.$message);
        }
    }

    public function showQr($orderId, PromptPayService $promptPayService)
    {
        $order = Order::where('ord_code', $orderId)->where('user_id', Auth::id())->firstOrFail();

        $expireTime = $order->updated_at->addMinutes(1);
        $secondsRemaining = now()->diffInSeconds($expireTime, false);
        if ($secondsRemaining < 0) {
            $secondsRemaining = 0;
        }

        $promptpayTarget = env('PROMPTPAY_ACCOUNT', '0812345678');
        $payload = $promptPayService->generatePayload($promptpayTarget, $order->net_amount);
        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate($payload));

        return view('qr', compact('order', 'qrCodeBase64', 'secondsRemaining'));
    }

    public function refreshQr($orderCode)
    {
        $order = Order::where('ord_code', $orderCode)->where('user_id', Auth::id())->firstOrFail();

        if ($order->status_id == Order::STATUS_CANCELLED) {
            return back()->with('error', 'ออเดอร์นี้ถูกยกเลิกแล้ว ไม่สามารถสร้าง QR Code ใหม่ได้');
        }

        if (now()->greaterThan($order->created_at->addMinutes(1))) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $order->status_id = Order::STATUS_CANCELLED;
                $order->save();

                foreach ($order->details as $detail) {
                    $stockRecord = \App\Models\StockProduct::where('pd_sp_id', $detail->pd_id)
                        ->where('option_id', $detail->option_id)
                        ->lockForUpdate()
                        ->first();
                    if ($stockRecord) {
                        $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                        $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                    }
                }
            });

            return back()->with('error', 'หมดเวลาชำระเงินแล้ว ออเดอร์ถูกยกเลิก');
        }

        if ($order->status_id == Order::STATUS_PENDING) {
            $order->touch();

            return redirect()->route('payment.qr', ['orderId' => $orderCode])->with('success', 'รีเฟรช QR Code แล้ว');
        }

        return back()->with('error', 'ไม่สามารถรีเฟรชได้');
    }

    public function cancelOrder($orderCode)
    {
        $order = Order::where('ord_code', $orderCode)
            ->where('user_id', Auth::id())
            ->where('status_id', Order::STATUS_PENDING)
            ->firstOrFail();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $order->status_id = Order::STATUS_CANCELLED;
                $order->save();

                foreach ($order->details as $detail) {
                    $stockRecord = \App\Models\StockProduct::where('pd_sp_id', $detail->pd_id)
                        ->where('option_id', $detail->option_id)
                        ->lockForUpdate()
                        ->first();
                    if ($stockRecord) {
                        $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                        $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                    }
                }
            });

            return redirect()->route('orders.index')->with('success', 'ยกเลิกคำสั่งซื้อเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการยกเลิก: '.$e->getMessage());
        }
    }

    public function applyDiscount(Request $request)
    {
        try {
            $request->validate([
                'code' => 'nullable|string|max:255',
                'selected_items' => 'required|array',
                'selected_items.*' => 'string',
                'selected_freebies' => 'nullable|array',
                'selected_freebies.*' => 'numeric',
            ]);

            $now = now();
            $discountCode = $request->filled('code') ? trim($request->input('code')) : null;
            $selectedItems = $request->input('selected_items');
            $selectedFreebies = $request->input('selected_freebies', []);
            $userId = auth()->id();

            $success = false;
            $message = 'รหัสส่วนลดไม่ถูกต้องหรือไม่สามารถใช้ได้';

            if (empty($discountCode)) {
                $this->orderService->getCartService()->removePromoCode();
                $success = true;
                $message = 'ลบรหัสส่วนลดแล้ว';
            } else {
                try {
                    $this->orderService->getCartService()->applyPromoCode($discountCode);
                    $success = true;
                    $message = 'ใช้รหัสส่วนลด '.$discountCode.' สำเร็จ!';
                } catch (\Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                    session()->forget("cart_{$userId}_promo_code");
                }
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

            $promoDiscount = $this->orderService->getCartService()->calculateTotalDiscount($totalAmount, $checkoutCartItems);

            $grandTotal = max(0, $totalAmount - $promoDiscount);
            $totalDiscount = $totalDiscountFromProducts + $promoDiscount;
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

        if ($order->status_id == Order::STATUS_CANCELLED) {
            return back()->with('error', 'ออเดอร์นี้ถูกยกเลิกเนื่องจากชำระเงินเกินเวลาที่กำหนด');
        }

        if (now()->greaterThan($order->updated_at->addMinutes(15))) {
            return back()->with('error', 'หมดเวลาชำระเงิน กรุณากดปุ่มรีเฟรช');
        }

        try {
            if ($request->hasFile('slip_image')) {
                DB::beginTransaction();

                $extension = $request->file('slip_image')->getClientOriginalExtension();
                $filename = \Illuminate\Support\Str::uuid() . '.' . $extension;
                $path = $request->file('slip_image')->storeAs('slips', $filename, 'public');

                $alreadyPaid = ($order->status_id >= Order::STATUS_PAID);

                $order->slip_path = $path;
                $order->status_id = Order::STATUS_PAID;
                $order->save();

                if (! $alreadyPaid) {
                    $this->orderService->finalizeOrder($order);
                }

                DB::commit();

                $order->refresh();
                \App\Jobs\SendOrderToApiJob::dispatchSync($order);

                return redirect()->route('orders.show', ['orderCode' => $order->ord_code])
                    ->with('success', 'แนบสลิปเรียบร้อยแล้ว!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Slip Upload Failed (PaymentController): '.$e->getMessage());

            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดสลิป: '.$e->getMessage());
        }

        return back()->with('error', 'กรุณาเลือกไฟล์รูปภาพ');
    }
}
