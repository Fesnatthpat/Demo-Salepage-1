<?php

namespace App\Services;

use App\Jobs\SendOrderToApiJob;
use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSalepage;
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

    public function createOrder(array $data, User $user, array $selectedItems = [], array $selectedFreebies = []): Order
    {
        return DB::transaction(function () use ($data, $user, $selectedItems, $selectedFreebies) {
            $allCartItems = $this->cartService->getCartContents();
            
            // กรองเอาเฉพาะสินค้าที่เลือกมาจากหน้า Checkout
            $cartItems = $allCartItems->filter(function ($item) use ($selectedItems) {
                return empty($selectedItems) || in_array((string) $item->id, $selectedItems);
            });

            if ($cartItems->isEmpty() && empty($selectedFreebies)) {
                throw new \Exception('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
            }

            // จัดการข้อมูลของแถม (ถ้ามี)
            $freebieItems = collect();
            if (!empty($selectedFreebies)) {
                $freebieProducts = ProductSalepage::whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $fp) {
                    $freebieItems->push((object)[
                        'id' => $fp->pd_sp_id,
                        'name' => $fp->pd_sp_name . ' (ของแถม)',
                        'price' => 0,
                        'quantity' => 1,
                        'attributes' => collect([
                            'product_id' => $fp->pd_sp_id,
                            'original_price' => (float)$fp->pd_sp_price,
                            'is_freebie' => true
                        ])
                    ]);
                }
            }

            // รวมสินค้าปกติและของแถมเข้าด้วยกันเพื่อวนลูปสร้าง Order Detail
            $itemsToProcess = $cartItems->concat($freebieItems);

            // Ensure a delivery address is selected
            $deliveryAddress = DeliveryAddress::with(['province', 'amphure', 'district'])
                ->where('user_id', $user->id)
                ->where('id', $data['delivery_address_id'])
                ->firstOrFail();

            // 1. Generate Order Code
            $ord_code = 'ORD-'.now()->format('YmdHis').'-'.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            
            // 2. Create the order
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

            foreach ($itemsToProcess as $cartItem) {
                $productId = isset($cartItem->attributes['product_id']) ? $cartItem->attributes['product_id'] : $cartItem->id;
                
                $product = ProductSalepage::lockForUpdate()->find($productId);
                if (!$product) continue;

                // ตรวจสอบสต็อก (ยกเว้นของแถมบางประเภทที่อาจจะไม่ตัดสต็อก หรือตรวจสอบตามปกติ)
                if ($product->pd_sp_stock < $cartItem->quantity) {
                    throw new \Exception('สินค้า '.$product->pd_sp_name.' มีไม่เพียงพอ');
                }

                $originalPrice = isset($cartItem->attributes['original_price']) ? $cartItem->attributes['original_price'] : $cartItem->price;
                $finalItemPrice = $cartItem->price;

                $totalPrice += $originalPrice * $cartItem->quantity;
                $netAmount += $finalItemPrice * $cartItem->quantity;
                $totalDiscount += ($originalPrice * $cartItem->quantity) - ($finalItemPrice * $cartItem->quantity);

                $optionName = null;
                if (isset($cartItem->name) && str_contains($cartItem->name, '(') && str_contains($cartItem->name, ')')) {
                    preg_match('/\((.*?)\)/', $cartItem->name, $matches);
                    $optionName = $matches[1] ?? null;
                }

                OrderDetail::create([
                    'ord_id' => $order->id,
                    'pd_id' => $productId,
                    'option_name' => $optionName,
                    'ordd_price' => $finalItemPrice,
                    'ordd_original_price' => $originalPrice,
                    'ordd_count' => $cartItem->quantity,
                    'ordd_discount' => ($originalPrice - $finalItemPrice),
                    'ordd_create_date' => now(),
                ]);

                // ตัดสต็อก
                $product->decrement('pd_sp_stock', $cartItem->quantity);
            }

            // --- Apply Session Discount ---
            $additionalDiscountFromCode = 0;
            if (session()->has('applied_discount_code')) {
                $discountData = session('applied_discount_code');
                if (isset($discountData['fixed']) && $discountData['fixed'] > 0) {
                    $additionalDiscountFromCode = $discountData['fixed'];
                } elseif (isset($discountData['percentage']) && $discountData['percentage'] > 0) {
                    $additionalDiscountFromCode = $netAmount * $discountData['percentage'];
                }
                $additionalDiscountFromCode = round($additionalDiscountFromCode, 2);
                session()->forget('applied_discount_code');
            }

            $netAmount = max(0, $netAmount - $additionalDiscountFromCode);
            $totalDiscount += $additionalDiscountFromCode;

            $order->total_price = $totalPrice;
            $order->total_discount = $totalDiscount;
            $order->net_amount = $netAmount;
            $order->save();

            // ลบเฉพาะสินค้าที่สั่งซื้อออกจากตะกร้า (ไม่ลบทั้งหมด)
            foreach ($cartItems as $item) {
                Cart::session($user->id)->remove($item->id);
            }
            
            // อัปเดต Database Storage สำหรับตะกร้าที่เหลือ
            CartStorage::updateOrCreate(
                ['user_id' => $user->id],
                ['cart_data' => Cart::session($user->id)->getContent()->toJson()]
            );

            // 3. เตรียมข้อมูลที่อยู่สำหรับส่งไป CRM
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
        // This is simplified. In a real application, you'd integrate with a payment gateway.
        // For PromptPay, you might generate a QR code string or image.
        // For demonstration, let's return a simple string.
        return 'PromptPay QR Code Data for Order #'.$order->id.' Amount: '.$order->total_amount;
    }

    // You might add methods for payment processing, slip uploads, etc.
}
