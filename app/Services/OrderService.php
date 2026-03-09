<?php

namespace App\Services;

use App\Jobs\SendOrderToApiJob;
use App\Models\CartStorage;
use App\Models\DeliveryAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductSalepage;
use App\Models\PromotionUsageLog;
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
                    'price' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                    'attributes' => $item->attributes,
                    'is_freebie' => false,
                ]);
            }

            // ดึงข้อมูลของแถมจากฐานข้อมูลและเพิ่มเข้าคอลเลกชัน
            if (! empty($selectedFreebies)) {
                $freebieProducts = ProductSalepage::whereIn('pd_sp_id', $selectedFreebies)->get();
                foreach ($freebieProducts as $fp) {
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

            // 4. วนลูปสร้าง OrderDetail และจองสต็อก
            foreach ($itemsToProcess as $item) {
                $item = (object) $item;
                $productId = $item->attributes['product_id'] ?? $item->id;
                $optionId = $item->attributes['option_id'] ?? null;

                // ดึงข้อมูลสต็อกและล็อคแถวไว้เพื่อป้องกัน Race Condition
                $stockRecord = StockProduct::where('pd_sp_id', $productId)
                    ->where('option_id', $optionId)
                    ->lockForUpdate()
                    ->first();

                // 🌟 [แก้ไข] หากไม่พบสต็อกหลัก (NULL) แต่เป็นสินค้าที่มีตัวเลือก (Options) 
                // ให้พยายามดึงสต็อกของตัวเลือกแรกมาใช้ (กรณีของแถมที่ไม่ได้ระบุตัวเลือก)
                if (! $stockRecord && is_null($optionId)) {
                    $stockRecord = StockProduct::where('pd_sp_id', $productId)
                        ->whereNotNull('option_id')
                        ->orderBy('stock_id', 'asc')
                        ->lockForUpdate()
                        ->first();
                    
                    // หากใช้สต็อกของ Option มาแทน ให้เก็บ option_id นั้นไว้เพื่อบันทึกลง OrderDetail ด้วย
                    if ($stockRecord) {
                        $optionId = $stockRecord->option_id;
                    }
                }

                if (! $stockRecord) {
                    // หากยังไม่พบอีก ให้สร้างสต็อกหลอกขึ้นมา (เพื่อไม่ให้ระบบพัง) หรือโยน Exception
                    // ในที่นี้เลือกที่จะสร้าง record ใหม่ด้วยจำนวน 0 หากเป็นสินค้าที่มีอยู่ในระบบจริง
                    $productExists = \App\Models\ProductSalepage::find($productId);
                    if ($productExists) {
                        $stockRecord = StockProduct::create([
                            'pd_sp_id' => $productId,
                            'option_id' => $optionId,
                            'quantity' => 0,
                            'reserved_qty' => 0
                        ]);
                    } else {
                        throw new \Exception('ไม่พบข้อมูลสินค้าและสต็อกสำหรับ: '.$item->name);
                    }
                }

                // [แก้ไข] สต๊อกที่พร้อมขาย (Available Stock)
                $availableStock = $stockRecord->quantity - $stockRecord->reserved_qty;

                // ตรวจสอบสต๊อกที่ว่าง
                if ($availableStock < $item->quantity) {
                    throw new \Exception('สินค้า '.$item->name.' มีไม่เพียงพอ (เหลือพร้อมขาย '.$availableStock.' ชิ้น)');
                }

                // 🌟 [แก้ไขใหม่] ทำการ "จองสต๊อก" ทันที เพื่อไม่ให้คนอื่นแย่ง
                // เพิ่มยอดจอง (reserved_qty) เท่านั้น สต็อกหลัก (quantity) ยังไม่ลดจนกว่าจะจ่ายเงิน
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

            // 5. คำนวณส่วนลดเพิ่มเติมจากโปรโมชั่น (รหัสส่วนลด และส่วนลดอัตโนมัติ)
            $promos = $this->cartService->getApplicablePromotions($allCartItems);
            $additionalDiscount = 0;

            foreach ($promos as $promo) {
                // ตรวจสอบ usage_limit ก่อนใช้งาน (Double check ใน Transaction)
                if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
                    continue; // ข้ามโปรโมชั่นที่สิทธิ์เต็มแล้ว
                }

                // คำนวณส่วนลด
                $thisPromoDiscount = 0;
                if ($promo->discount_value > 0) {
                    if ($promo->discount_type === 'fixed') {
                        $thisPromoDiscount = (float) $promo->discount_value;
                    } elseif ($promo->discount_type === 'percentage') {
                        $thisPromoDiscount = ($netAmount * ((float) $promo->discount_value / 100));
                    }
                }

                $additionalDiscount += $thisPromoDiscount;

                // [NEW] เพิ่มจำนวนการใช้งานโปรโมชั่น
                \App\Models\Promotion::where('id', $promo->id)->increment('used_count');

                // [NEW] บันทึก Log การใช้โปรโมชั่น
                \App\Models\PromotionUsageLog::create([
                    'promotion_id' => $promo->id,
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'code_used' => $promo->code, // อาจเป็นรหัสส่วนลด หรือ NULL ถ้าเป็น Auto Promotion
                    'discount_amount' => $thisPromoDiscount,
                ]);
            }
            
            $netAmount = max(0, $netAmount - $additionalDiscount);
            $totalDiscount += $additionalDiscount;

            $order->total_price = $totalPrice;
            $order->total_discount = $totalDiscount;
            $order->net_amount = $netAmount;
            $order->save();

            // 6. ล้างรหัสส่วนลด (ถ้ามี)
            $this->cartService->removePromoCode();

            // 7. ลบสินค้าออกจากตะกร้า (เฉพาะที่สั่งซื้อจริง)
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
}
