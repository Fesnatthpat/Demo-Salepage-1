<?php

namespace App\Services;

use App\Jobs\SendOrderToApiJob;
use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSalepage;
use App\Models\StockProduct;
use App\Models\User;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getCartService(): CartService
    {
        return $this->cartService;
    }

    public function createOrder(array $data, User $user, array $selectedItems = [], array $selectedFreebies = []): Order
    {
        return DB::transaction(function () use ($data, $user, $selectedItems, $selectedFreebies) {
            $allCartItems = $this->cartService->getCartContents();

            $cartItems = $allCartItems->filter(function ($item) use ($selectedItems) {
                return in_array((string) $item->id, array_map('strval', $selectedItems));
            });

            if ($cartItems->isEmpty() && empty($selectedFreebies)) {
                throw new \Exception('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
            }

            $itemsToProcess = collect();

            foreach ($cartItems as $item) {
                $itemsToProcess->push([
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                    'attributes' => $item->attributes,
                    'is_freebie' => false,
                ]);
            }

            if (! empty($selectedFreebies)) {
                $existingFreebieIdsInCart = $itemsToProcess->filter(fn ($i) => $i['is_freebie'])
                    ->map(fn ($i) => (int) ($i['attributes']['product_id'] ?? $i['id']))
                    ->toArray();

                $freebieProducts = ProductSalepage::whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $fp) {
                    if (in_array((int) $fp->pd_sp_id, $existingFreebieIdsInCart)) {
                        continue;
                    }

                    $itemsToProcess->push([
                        'id' => $fp->pd_sp_id,
                        'name' => $fp->pd_sp_name.' (ของแถม)',
                        'price' => 0.0,
                        'quantity' => 1,
                        'attributes' => [
                            'product_id' => $fp->pd_sp_id,
                            'original_price' => (float) $fp->pd_sp_price,
                            'is_freebie' => true,
                        ],
                        'is_freebie' => true,
                    ]);
                }
            }

            $deliveryAddress = DeliveryAddress::with(['province', 'amphure', 'district'])
                ->where('user_id', $user->id)
                ->where('id', $data['delivery_address_id'])
                ->firstOrFail();

            $ord_code = 'ORD-'.now()->format('YmdHis').'-'.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

            $order = Order::create([
                'ord_code' => $ord_code,
                'user_id' => $user->id,
                'ord_date' => now(),
                'status_id' => 1,
                'total_price' => 0,
                'shipping_cost' => 0,
                'total_discount' => 0,
                'net_amount' => 0,
                'shipping_name' => $deliveryAddress->fullname,
                'shipping_phone' => $deliveryAddress->phone,
                'shipping_address' => sprintf(
                    '%s, %s, %s, %s, %s',
                    $deliveryAddress->address_line1,
                    optional($deliveryAddress->district)->name_th,
                    optional($deliveryAddress->amphure)->name_th,
                    optional($deliveryAddress->province)->name_th,
                    $deliveryAddress->zipcode
                ),
            ]);

            $totalPrice = 0;
            $totalDiscount = 0;
            $netAmount = 0;

            foreach ($itemsToProcess as $item) {
                $item = (object) $item;
                $productId = $item->attributes['product_id'] ?? $item->id;
                $optionId = $item->attributes['option_id'] ?? null;

                $stockRecord = StockProduct::where('pd_sp_id', $productId)
                    ->where('option_id', $optionId)
                    ->lockForUpdate()
                    ->first();

                if (is_null($optionId) && (! $stockRecord || ($stockRecord->quantity - $stockRecord->reserved_qty) < $item->quantity)) {
                    $fallbackStock = StockProduct::where('pd_sp_id', $productId)
                        ->where('quantity', '>', 0)
                        ->whereColumn('quantity', '>', 'reserved_qty')
                        ->orderBy('quantity', 'desc')
                        ->lockForUpdate()
                        ->first();

                    if ($fallbackStock) {
                        $stockRecord = $fallbackStock;
                        $optionId = $stockRecord->option_id;
                    }
                }

                if (! $stockRecord) {
                    $productExists = \App\Models\ProductSalepage::find($productId);
                    if ($productExists) {
                        $stockRecord = StockProduct::create([
                            'pd_sp_id' => $productId,
                            'option_id' => $optionId,
                            'quantity' => 0,
                            'reserved_qty' => 0,
                        ]);
                    } else {
                        throw new \Exception('ไม่พบข้อมูลสินค้าและสต็อกสำหรับ: '.$item->name);
                    }
                }

                $availableStock = $stockRecord->quantity - $stockRecord->reserved_qty;

                if ($availableStock < $item->quantity) {
                    throw new \Exception('สินค้า '.$item->name.' มีไม่เพียงพอ (เหลือพร้อมขาย '.$availableStock.' ชิ้น)');
                }

                $stockRecord->increment('reserved_qty', $item->quantity);

                $originalPrice = $item->attributes['original_price'] ?? $item->price;
                $finalItemPrice = $item->price;

                $totalPrice += ($originalPrice * $item->quantity);
                $netAmount += ($finalItemPrice * $item->quantity);
                $totalDiscount += (($originalPrice - $finalItemPrice) * $item->quantity);

                $optionName = null;
                if ($optionId) {
                    $option = \App\Models\ProductOption::find($optionId);
                    $optionName = $option ? $option->option_name : null;
                }

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'pd_id' => $productId,
                    'option_id' => $optionId,
                    'option_name' => $optionName,
                    'ordd_price' => $finalItemPrice,
                    'ordd_original_price' => $originalPrice,
                    'ordd_count' => $item->quantity,
                    'ordd_discount' => ($originalPrice - $finalItemPrice),
                    'ordd_create_date' => now(),
                    'user_id' => $user->id,
                ]);
            }

            // ✅ 🌟 แก้ไข: บันทึกส่วนลดลง Database ตามการรองรับทั้ง Auto-Discount และ Coupon Code
            $promos = $this->cartService->getApplicablePromotions($cartItems);
            $additionalDiscount = 0;
            $appliedCode = $this->cartService->getAppliedPromoCode();

            foreach ($promos as $promo) {
                if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
                    continue;
                }

                $thisPromoDiscount = 0;

                // เช็คประเภทของส่วนลด
                $isAutoDiscount = !$promo->is_discount_code;
                $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

                if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                    if ($promo->discount_type === 'fixed') {
                        $thisPromoDiscount = (float) $promo->discount_value;
                    } elseif ($promo->discount_type === 'percentage') {
                        $thisPromoDiscount = ($netAmount * ((float) $promo->discount_value / 100));
                    }
                }

                if ($thisPromoDiscount > 0) {
                    $additionalDiscount += $thisPromoDiscount;

                    // บันทึกประวัติการใช้โปรโมชั่นลงฐานข้อมูล
                    \App\Models\Promotion::where('id', $promo->id)->increment('used_count');

                    \App\Models\PromotionUsageLog::create([
                        'promotion_id' => $promo->id,
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        // ถ้าเป็นโปรอัตโนมัติ จะบันทึกโค้ดเป็น null, ถ้าใช้โค้ดก็บันทึกรหัสลงไป
                        'code_used' => $promo->is_discount_code ? $promo->code : null, 
                        'discount_amount' => $thisPromoDiscount, 
                    ]);
                }
            }

            $netAmount = max(0, $netAmount - $additionalDiscount);
            $totalDiscount += $additionalDiscount;

            $order->total_price = $totalPrice;
            $order->total_discount = $totalDiscount;
            $order->net_amount = $netAmount;
            $order->save();

            $this->cartService->removePromoCode();

            foreach ($cartItems as $item) {
                Cart::session($user->id)->remove($item->id);
            }

            CartStorage::updateOrCreate(
                ['user_id' => $user->id],
                ['cart_data' => Cart::session($user->id)->getContent()->toJson()]
            );

            $addressData = [
                'province' => $deliveryAddress->province->name_th ?? '',
                'amphure' => $deliveryAddress->amphure->name_th ?? '',
                'district' => $deliveryAddress->district->name_th ?? '',
                'postal_code' => $deliveryAddress->zipcode ?? '',
                'customer_name' => $deliveryAddress->fullname ?? $user->name ?? 'ลูกค้าทั่วไป',
                'payment_method' => $data['payment_method'] ?? 'PromptPay',
                'shipping_method' => 'Standard Delivery',
            ];

            SendOrderToApiJob::dispatch($order, $addressData);

            return $order;
        });
    }

    public function getPaymentQrCodeData(Order $order): string
    {
        return 'PromptPay QR Code Data for Order #'.$order->id.' Amount: '.$order->total_amount;
    }
}