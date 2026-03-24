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
    public function __construct(protected CartService $cartService, protected PromotionService $promotionService) {}

    public function getCartService(): CartService
    {
        return $this->cartService;
    }

    public function createOrder(array $data, ?User $user = null, array $selectedItems = [], array $selectedFreebies = []): Order
    {
        return DB::transaction(function () use ($data, $user, $selectedItems, $selectedFreebies) {
            $userId = $user ? $user->id : 0;
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

            $shippingName = '';
            $shippingPhone = '';
            $shippingDetails = [
                'name' => 'ลูกค้าทั่วไป',
                'phone' => '',
                'address' => '',
                'province' => '',
                'amphure' => '',
                'district' => '',
                'zipcode' => '',
            ];

            if (isset($data['delivery_address_id'])) {
                $deliveryAddress = DeliveryAddress::with(['province', 'amphure', 'district'])
                    ->where('id', $data['delivery_address_id'])
                    ->firstOrFail();

                $shippingDetails['name'] = $deliveryAddress->fullname;
                $shippingDetails['phone'] = $deliveryAddress->phone;
                $shippingDetails['province'] = $deliveryAddress->province->name_th ?? '';
                $shippingDetails['amphure'] = $deliveryAddress->amphure->name_th ?? '';
                $shippingDetails['district'] = $deliveryAddress->district->name_th ?? '';
                $shippingDetails['zipcode'] = $deliveryAddress->zipcode;
                $shippingDetails['address'] = sprintf(
                    '%s, %s, %s, %s, %s',
                    $deliveryAddress->address_line1,
                    $shippingDetails['district'],
                    $shippingDetails['amphure'],
                    $shippingDetails['province'],
                    $shippingDetails['zipcode']
                );
            } else {
                $shippingDetails['name'] = $data['shipping_name'] ?? $data['customer_name'] ?? 'ลูกค้าทั่วไป';
                $shippingDetails['phone'] = $data['shipping_phone'] ?? $data['phone'] ?? '';
                $shippingDetails['address'] = $data['shipping_address'] ?? $data['address'] ?? '';
                $shippingDetails['province'] = $data['province'] ?? '';
                $shippingDetails['amphure'] = $data['amphure'] ?? '';
                $shippingDetails['district'] = $data['district'] ?? '';
                $shippingDetails['zipcode'] = $data['zipcode'] ?? $data['postal_code'] ?? '';
            }

            $ord_code = 'ORD-'.now()->format('YmdHis').'-'.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

            $order = Order::create([
                'ord_code' => $ord_code,
                'user_id' => $userId,
                'ord_date' => now(),
                'status_id' => Order::STATUS_PENDING,
                'total_price' => 0,
                'shipping_cost' => 0,
                'total_discount' => 0,
                'net_amount' => 0,
                'shipping_name' => $shippingDetails['name'],
                'shipping_phone' => $shippingDetails['phone'],
                'shipping_address' => $shippingDetails['address'],
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
                    'user_id' => $userId,
                ]);
            }

            // ✅ บันทึกส่วนลดลง Database โดยใช้ PromotionService
            $promos = $this->promotionService->getApplicablePromotions($cartItems);
            $additionalDiscount = 0;
            $appliedCode = $this->promotionService->getAppliedPromoCode();

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
                        'user_id' => $userId,
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
                Cart::session($userId)->remove($item->id);
            }

            CartStorage::updateOrCreate(
                ['user_id' => $userId],
                ['cart_data' => Cart::session($userId)->getContent()->toJson()]
            );

            $addressData = [
                'province' => $shippingDetails['province'],
                'amphure' => $shippingDetails['amphure'],
                'district' => $shippingDetails['district'],
                'postal_code' => $shippingDetails['zipcode'],
                'customer_name' => $shippingDetails['name'],
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

    public function deductStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                    ->where('option_id', $detail->option_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockRecord) {
                    // ลดจำนวนสต็อกจริง
                    $stockRecord->decrement('quantity', $detail->ordd_count);
                    
                    // ลดจำนวนที่จองไว้ (ถ้ามี)
                    $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                    if ($reserveToSubtract > 0) {
                        $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                    }
                }
            }
        });
    }

    public function incrementSoldCount(Order $order): void
    {
        foreach ($order->details as $detail) {
            $product = ProductSalepage::find($detail->pd_id);
            if ($product) {
                $product->increment('pd_sp_sold', $detail->ordd_count);
            }
        }
    }

    public function finalizeOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $this->deductStock($order);
            $this->incrementSoldCount($order);
        });
    }

    /**
     * ยกเลิกออเดอร์และคืนสต็อก
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // ป้องกันการยกเลิกซ้ำ
            if ($order->status_id == Order::STATUS_CANCELLED) {
                return;
            }

            $oldStatus = $order->status_id;
            $order->status_id = Order::STATUS_CANCELLED;
            $order->save();

            foreach ($order->details as $detail) {
                $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                    ->where('option_id', $detail->option_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockRecord) {
                    if ($oldStatus == Order::STATUS_PENDING) {
                        // ถ้ายังไม่จ่ายเงิน (แค่จองไว้) -> ลด reserved_qty
                        $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                        $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                    } elseif ($oldStatus >= Order::STATUS_PAID) {
                        // ถ้าจ่ายเงินแล้ว (ตัดสต็อกจริงไปแล้ว) -> เพิ่ม quantity กลับคืนมา
                        $stockRecord->increment('quantity', $detail->ordd_count);
                    }
                }
            }
        });
    }
}