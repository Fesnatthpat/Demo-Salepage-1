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

    public function createOrder(array $data, User $user, array $selectedItems = [], array $selectedFreebies = []): Order
    {
        return DB::transaction(function () use ($data, $user, $selectedItems, $selectedFreebies) {
            $allCartItems = $this->cartService->getCartContents();
            
            // 1. กรองเอาเฉพาะสินค้าที่เลือก (ตรวจสอบทั้ง ID ที่เป็น String และ Integer)
            $cartItems = $allCartItems->filter(function ($item) use ($selectedItems) {
                return in_array((string) $item->id, array_map('strval', $selectedItems));
            });

            if ($cartItems->isEmpty() && empty($selectedFreebies)) {
                throw new \Exception('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
            }

            // 2. จัดการข้อมูลของแถม
            $itemsToProcess = collect();
            
            // เพิ่มสินค้าปกติเข้าคอลเลกชันที่จะประมวลผล
            foreach ($cartItems as $item) {
                $itemsToProcess->push([
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => (float)$item->price,
                    'quantity' => (int)$item->quantity,
                    'attributes' => $item->attributes,
                    'is_freebie' => false
                ]);
            }

            // ดึงข้อมูลของแถมจากฐานข้อมูลและเพิ่มเข้าคอลเลกชัน
            if (!empty($selectedFreebies)) {
                $freebieProducts = ProductSalepage::whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $fp) {
                    $itemsToProcess->push([
                        'id' => $fp->pd_sp_id,
                        'name' => $fp->pd_sp_name . ' (ของแถม)',
                        'price' => 0.0,
                        'quantity' => 1,
                        'attributes' => [
                            'product_id' => $fp->pd_sp_id,
                            'original_price' => (float)$fp->pd_sp_price,
                            'is_freebie' => true
                        ],
                        'is_freebie' => true
                    ]);
                }
            }

            // 3. เตรียมข้อมูลที่อยู่จัดส่ง
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

            // 4. วนลูปสร้าง OrderDetail และตัดสต็อก
            foreach ($itemsToProcess as $item) {
                $item = (object)$item;
                $productId = $item->attributes['product_id'] ?? $item->id;
                $optionId = $item->attributes['option_id'] ?? null;
                
                // ดึงข้อมูลสต็อกและล็อคแถวไว้เพื่อป้องกัน Race Condition
                $stockRecord = StockProduct::where('pd_sp_id', $productId)
                    ->where('option_id', $optionId)
                    ->lockForUpdate()
                    ->first();

                if (!$stockRecord) {
                    // ถ้าไม่พบเรคคอร์ดสต็อก ให้ลองสร้างใหม่ด้วย 0 หรือโยน Error ตามความเหมาะสม
                    // ในที่นี้เลือกโยน Error เพราะสินค้าควรมีเรคคอร์ดสต็อกเสมอ
                    throw new \Exception('ไม่พบข้อมูลสต็อกสำหรับสินค้า: ' . $item->name);
                }

                // ตรวจสอบสต็อก
                if ($stockRecord->quantity < $item->quantity) {
                    throw new \Exception('สินค้า '.$item->name.' มีไม่เพียงพอ (เหลือ '.$stockRecord->quantity.' ชิ้น)');
                }

                $originalPrice = $item->attributes['original_price'] ?? $item->price;
                $finalItemPrice = $item->price;

                $totalPrice += ($originalPrice * $item->quantity);
                $netAmount += ($finalItemPrice * $item->quantity);
                $totalDiscount += (($originalPrice - $finalItemPrice) * $item->quantity);

                // ✅ ดึงชื่อ Option จากฐานข้อมูลโดยตรงเพื่อความแม่นยำ
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

                // ตัดสต็อกถูกลบออกไปเพื่อไปตัดตอนแนบสลิปแทน
            }

            // 5. จัดการส่วนลดจากรหัสโปรโมชั่น
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

            // 6. ลบสินค้าออกจากตะกร้า (เฉพาะที่สั่งซื้อจริง)
            foreach ($cartItems as $item) {
                Cart::session($user->id)->remove($item->id);
            }
            
            // อัปเดต Database Storage
            CartStorage::updateOrCreate(
                ['user_id' => $user->id],
                ['cart_data' => Cart::session($user->id)->getContent()->toJson()]
            );

            // 7. ส่งข้อมูลเข้า CRM
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

    /**
     * ตัดสต็อกสำหรับออเดอร์ (เรียกใช้หลังแนบสลิป)
     */
    public function deductStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                    ->where('option_id', $detail->option_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stockRecord) {
                    throw new \Exception('ไม่พบข้อมูลสต็อกสำหรับสินค้า: ' . ($detail->productSalepage->pd_sp_name ?? 'ID ' . $detail->pd_id));
                }

                if ($stockRecord->quantity < $detail->ordd_count) {
                    throw new \Exception('สินค้า ' . ($detail->productSalepage->pd_sp_name ?? 'ID ' . $detail->pd_id) . ' มีไม่เพียงพอ (เหลือ ' . $stockRecord->quantity . ' ชิ้น)');
                }

                $stockRecord->decrement('quantity', $detail->ordd_count);
            }
        });
    }
}
