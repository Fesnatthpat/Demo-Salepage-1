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
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Models\Province;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderService
{
    public function __construct(protected CartService $cartService, protected PromotionService $promotionService) {}

    public function getCartService(): CartService
    {
        return $this->cartService;
    }

    /**
     * Helper to calculate shipping cost.
     */
    public function calculateShippingValue($addressId, $subtotal, $shippingMethodId = null, $itemCount = 1, ?Collection $cartItems = null)
    {
        // 🎫 Check for Free Shipping from Promotions
        if ($cartItems && $this->promotionService->isFreeShippingApplicable($cartItems)) {
            return 0;
        }

        if (!$addressId) {
            return (float) ShippingSetting::get('upc_flat_rate', 60);
        }

        $shippingMode = ShippingSetting::get('shipping_mode', 'global');

        if ($shippingMode === 'global') {
            $freeThreshold = (float) ShippingSetting::get('free_shipping_threshold', 999);
            if ($subtotal >= $freeThreshold) {
                return 0;
            }

            $address = DeliveryAddress::find($addressId);
            $bkkMetroNames = ['กรุงเทพมหานคร', 'นนทบุรี', 'ปทุมธานี', 'สมุทรปราการ'];
            $province = \App\Models\Province::find($address?->province_id);
            $isBkk = $province && in_array($province->name_th, $bkkMetroNames);

            return $isBkk 
                ? (float) ShippingSetting::get('bkk_flat_rate', 40)
                : (float) ShippingSetting::get('upc_flat_rate', 60);
        }

        // Methods mode (Automated: Use default or first active method)
        $method = ShippingMethod::where('is_active', true)->where('is_default', true)->first()
               ?? ShippingMethod::where('is_active', true)->orderBy('sort_order')->first();

        if (!$method) {
            return (float) ShippingSetting::get('upc_flat_rate', 60);
        }

        // Check Free Shipping Conditions for Method
        if ($method->free_threshold !== null && $subtotal >= $method->free_threshold) {
            return 0;
        }
        if ($method->min_items_for_free_shipping !== null && $itemCount >= $method->min_items_for_free_shipping) {
            return 0;
        }

        $address = DeliveryAddress::find($addressId);
        $isBkk = false;
        if ($address) {
            $bkkMetroNames = ['กรุงเทพมหานคร', 'นนทบุรี', 'ปทุมธานี', 'สมุทรปราการ'];
            $province = \App\Models\Province::find($address->province_id);
            $isBkk = $province && in_array($province->name_th, $bkkMetroNames);
        }

        $baseRate = $isBkk ? $method->bkk_rate : $method->upc_rate;
        $extraItems = max(0, $itemCount - 1);
        $extraCost = $extraItems * $method->per_item_rate;

        return (float) ($baseRate + $extraCost);
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
            $totalItemCount = 0;

            foreach ($cartItems as $item) {
                $itemsToProcess->push([
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                    'attributes' => $item->attributes,
                    'is_freebie' => false,
                ]);
                $totalItemCount += (int) $item->quantity;
            }

            if (! empty($selectedFreebies)) {
                // 🔒 Security Check: Verify if the user actually earns these freebies
                $applicablePromos = $this->promotionService->getApplicablePromotions($cartItems);
                $allowedFreebieLimit = $this->promotionService->calculateFreebieLimit($cartItems, $applicablePromos);
                
                if (count($selectedFreebies) > $allowedFreebieLimit) {
                    throw new \Exception('จำนวนของแถมเกินสิทธิ์ที่คุณได้รับ');
                }

                $allowedGiftIds = $applicablePromos->flatMap(function ($promo) {
                    return $promo->actions->flatMap(function ($action) {
                        $ids = collect();
                        if (isset($action->actions['product_id_to_get'])) {
                            $ids->push((int)$action->actions['product_id_to_get']);
                        }
                        if ($action->giftableProducts->isNotEmpty()) {
                            $ids = $ids->merge($action->giftableProducts->pluck('pd_sp_id'));
                        }
                        return $ids;
                    });
                })->unique()->toArray();

                foreach ($selectedFreebies as $sfId) {
                    if (!in_array((int)$sfId, $allowedGiftIds)) {
                        throw new \Exception('สินค้าของแถมบางรายการไม่ตรงตามเงื่อนไขโปรโมชั่น');
                    }
                }

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
                    $totalItemCount += 1;
                }
            }

            $shippingDetails = [
                'name' => 'ลูกค้าทั่วไป',
                'phone' => '',
                'address' => '',
                'province' => '',
                'amphure' => '',
                'district' => '',
                'zipcode' => '',
            ];

            $deliveryAddressId = $data['delivery_address_id'] ?? null;

            if ($deliveryAddressId) {
                $deliveryAddress = DeliveryAddress::with(['province', 'amphure', 'district'])
                    ->where('id', $deliveryAddressId)
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

            // Set Default Shipping Info
            $shippingMethodId = null;
            $shippingMethodName = 'Standard Delivery';

            $order = Order::create([
                'ord_code' => $ord_code,
                'user_id' => $userId,
                'ord_date' => now(),
                'status_id' => Order::STATUS_PENDING,
                'shipping_method_id' => $shippingMethodId,
                'shipping_method_name' => $shippingMethodName,
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
            $subTotal = 0;

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

                $hasReservedQty = Schema::hasTable('stock_product') && Schema::hasColumn('stock_product', 'reserved_qty');
                $availableStock = $stockRecord->quantity - ($hasReservedQty ? $stockRecord->reserved_qty : 0);

                if ($availableStock < $item->quantity) {
                    throw new \Exception('สินค้า '.$item->name.' มีไม่เพียงพอ (เหลือพร้อมขาย '.$availableStock.' ชิ้น)');
                }

                if ($hasReservedQty) {
                    $stockRecord->increment('reserved_qty', $item->quantity);
                }

                $originalPrice = $item->attributes['original_price'] ?? $item->price;
                $finalItemPrice = $item->price;

                $totalPrice += ($originalPrice * $item->quantity);
                $subTotal += ($finalItemPrice * $item->quantity);
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

            // Calculate Promotions
            $promos = $this->promotionService->getApplicablePromotions($cartItems);
            $additionalDiscount = 0;
            $appliedCode = $this->promotionService->getAppliedPromoCode();

            foreach ($promos as $promo) {
                if ($promo->usage_limit !== null && $promo->used_count >= $promo->usage_limit) {
                    continue;
                }

                $thisPromoDiscount = 0;
                $isAutoDiscount = !$promo->is_discount_code;
                $isMatchingCode = $promo->is_discount_code && !empty($appliedCode) && $promo->code === $appliedCode;

                if ($promo->discount_value > 0 && ($isAutoDiscount || $isMatchingCode)) {
                    if ($promo->discount_type === 'fixed') {
                        $thisPromoDiscount = (float) $promo->discount_value;
                    } elseif ($promo->discount_type === 'percentage') {
                        $thisPromoDiscount = ($subTotal * ((float) $promo->discount_value / 100));
                    }
                }

                if ($thisPromoDiscount > 0) {
                    $additionalDiscount += $thisPromoDiscount;
                    \App\Models\Promotion::where('id', $promo->id)->increment('used_count');
                    \App\Models\PromotionUsageLog::create([
                        'promotion_id' => $promo->id,
                        'order_id' => $order->id,
                        'user_id' => $userId,
                        'code_used' => $promo->is_discount_code ? $promo->code : null, 
                        'discount_amount' => $thisPromoDiscount, 
                    ]);
                }
            }

            $subTotalAfterPromo = max(0, $subTotal - $additionalDiscount);
            $totalDiscount += $additionalDiscount;

            // 🚚 Calculate Real Shipping Cost
            $shippingCost = $this->calculateShippingValue($deliveryAddressId, $subTotalAfterPromo, $shippingMethodId, $totalItemCount, $cartItems);
            $netAmount = $subTotalAfterPromo + $shippingCost;

            $order->total_price = $totalPrice;
            $order->total_discount = $totalDiscount;
            $order->shipping_cost = $shippingCost;
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
                'shipping_method' => $shippingMethodName,
            ];

            SendOrderToApiJob::dispatch($order, $addressData);

            return $order;
        });
    }

    public function deductStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $hasReservedQty = Schema::hasTable('stock_product') && Schema::hasColumn('stock_product', 'reserved_qty');
            foreach ($order->details as $detail) {
                $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                    ->where('option_id', $detail->option_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockRecord) {
                    $stockRecord->decrement('quantity', $detail->ordd_count);
                    if ($hasReservedQty) {
                        $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                        if ($reserveToSubtract > 0) {
                            $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                        }
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

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->status_id == Order::STATUS_CANCELLED) {
                return;
            }

            $oldStatus = $order->status_id;
            $order->status_id = Order::STATUS_CANCELLED;
            $order->save();

            $hasReservedQty = Schema::hasTable('stock_product') && Schema::hasColumn('stock_product', 'reserved_qty');

            foreach ($order->details as $detail) {
                $stockRecord = StockProduct::where('pd_sp_id', $detail->pd_id)
                    ->where('option_id', $detail->option_id)
                    ->lockForUpdate()
                    ->first();

                if ($stockRecord) {
                    if ($oldStatus == Order::STATUS_PENDING) {
                        if ($hasReservedQty) {
                            $reserveToSubtract = min($stockRecord->reserved_qty, $detail->ordd_count);
                            $stockRecord->decrement('reserved_qty', $reserveToSubtract);
                        }
                    } elseif ($oldStatus >= Order::STATUS_PAID) {
                        $stockRecord->increment('quantity', $detail->ordd_count);
                    }
                }
            }
        });
    }
}
