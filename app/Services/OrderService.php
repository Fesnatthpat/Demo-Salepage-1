<?php

namespace App\Services;

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

    public function createOrder(array $data, User $user): Order
    {
        return DB::transaction(function () use ($data, $user) {
            $cartItems = $this->cartService->getCartContents(); // Corrected method name
            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            // Ensure a delivery address is selected
            $deliveryAddress = DeliveryAddress::with(['province', 'amphure', 'district'])
                ->where('user_id', $user->id)
                ->where('id', $data['delivery_address_id'])
                ->firstOrFail();

            // --- Start Fix ---
                                    // 1. Generate Order Code
                                    $ord_code = 'ORD-' . now()->format('YmdHis') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            // 2. Create the order with fields matching the Order model's $fillable property
            $order = Order::create([
                'ord_code' => $ord_code,
                'user_id' => $user->id,
                'ord_date' => now(),
                'status_id' => 1, // Assuming 1 = 'pending' status
                'total_price' => 0, // Placeholder, will be updated after loop
                'shipping_cost' => 0, // Assuming 0 for now
                'total_discount' => 0, // Placeholder
                'net_amount' => 0, // Placeholder
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
            // --- End Fix ---

            $totalPrice = 0;
            $totalDiscount = 0;
            $netAmount = 0;

            foreach ($cartItems as $cartItem) {
                // Ensure cart item details are available
                $product = ProductSalepage::lockForUpdate()->find($cartItem->id);
                if (! $product) {
                    continue; // Skip if product not found
                }

                if ($product->pd_sp_stock < $cartItem->quantity) {
                    throw new \Exception('Not enough stock for '.$product->pd_sp_name);
                }

                // Use attributes from cart item
                $originalPrice = $cartItem->attributes->get('original_price', $cartItem->price);
                $finalItemPrice = $cartItem->price;

                $totalPrice += $originalPrice * $cartItem->quantity;
                $netAmount += $finalItemPrice * $cartItem->quantity;
                $totalDiscount += ($originalPrice * $cartItem->quantity) - ($finalItemPrice * $cartItem->quantity);

                OrderDetail::create([
                    'ord_id' => $order->id, // Use the new order's ID
                    'pd_id' => $cartItem->id,
                    'ordd_price' => $finalItemPrice,
                    'ordd_original_price' => $originalPrice,
                    'ordd_count' => $cartItem->quantity,
                    'ordd_discount' => ($originalPrice - $finalItemPrice),
                    'ordd_create_date' => now(),
                ]);

                // Decrement stock
                $product->decrement('pd_sp_stock', $cartItem->quantity);
            }

            // --- Start Fix: Update totals on the order ---
            $order->total_price = $totalPrice;
            $order->total_discount = $totalDiscount;
            $order->net_amount = $netAmount; // Assuming shipping_cost is 0
            $order->save();
            // --- End Fix ---

            // Clear the cart after successful order creation
            // This logic should be in CartService, but for now we'll leave it
            Cart::session($user->id)->clear();
            CartStorage::where('user_id', $user->id)->delete();
            if (empty($user->id)) {
                Cart::session($user->id)->clear();
            }

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
