<?php

use App\Services\CartService;
use App\Services\OrderService;
use App\Models\ProductSalepage;
use App\Models\StockProduct;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pids = App\Models\ProductSalepage::limit(2)->pluck('pd_sp_id')->toArray();
$pid1 = $pids[0];
$pid2 = $pids[1];

// Ensure stock
StockProduct::updateOrCreate(['pd_sp_id' => $pid1, 'option_id' => null], ['quantity' => 100]);
StockProduct::updateOrCreate(['pd_sp_id' => $pid2, 'option_id' => null], ['quantity' => 100]);

$cartService = app(CartService::class);
$orderService = app(OrderService::class);
$user = User::first();
Auth::login($user);

$userId = $user->id;
Cart::session($userId)->clear();

echo "Adding Bundle (Product $pid1 + Product $pid2 as gift)...\n";
$cartService->addBundle($pid1, 0, [$pid2]);

$items = Cart::session($userId)->getContent();
$mainItemId = null;
$freebieItemId = null;

foreach ($items as $item) {
    if ($item->attributes['is_freebie'] ?? false) {
        $freebieItemId = $item->id;
    } else {
        $mainItemId = $item->id;
    }
}

echo "Main Item ID: $mainItemId\n";
echo "Freebie Item ID: $freebieItemId\n";

echo "\nAttempting to create order with ONLY the freebie...\n";
$addressId = App\Models\DeliveryAddress::first()?->id ?? 1;
try {
    $order = $orderService->createOrder(
        ['delivery_address_id' => $addressId],
        $user,
        [$freebieItemId], // ONLY FREEBIE
        []
    );
    echo "SUCCESS (BUG!): Order created with only freebie. Order ID: " . $order->ord_code . "\n";
    echo "Net Amount: " . $order->net_amount . "\n";
} catch (\Exception $e) {
    echo "FAILED (CORRECT): " . $e->getMessage() . "\n";
}
