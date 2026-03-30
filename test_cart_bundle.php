<?php

use App\Services\CartService;
use App\Models\ProductSalepage;
use App\Models\StockProduct;
use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\Facades\CartFacade as Cart;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pids = App\Models\ProductSalepage::limit(2)->pluck('pd_sp_id')->toArray();
if (count($pids) < 2) {
    echo "Not enough products in database.\n";
    exit;
}
$pid1 = $pids[0];
$pid2 = $pids[1];

// Ensure stock
StockProduct::updateOrCreate(['pd_sp_id' => $pid1, 'option_id' => null], ['quantity' => 100]);
StockProduct::updateOrCreate(['pd_sp_id' => $pid2, 'option_id' => null], ['quantity' => 100]);

$cartService = app(CartService::class);
$userId = $cartService->getUserId();
Cart::session($userId)->clear();

echo "Adding Bundle 1 (Product $pid1 + Product $pid2 as gift)...\n";
$cartService->addBundle($pid1, 0, [$pid2]);

echo "Cart contents after Bundle 1:\n";
$items = Cart::session($userId)->getContent();
foreach ($items as $item) {
    echo "ID: {$item->id}, Name: {$item->name}, Qty: {$item->quantity}, Group: " . ($item->attributes['promo_group_id'] ?? 'none') . "\n";
}

echo "\nAdding Bundle 2 (Product $pid1 + Product $pid2 as gift)...\n";
$cartService->addBundle($pid1, 0, [$pid2]);

echo "Cart contents after Bundle 2:\n";
$items = Cart::session($userId)->getContent();
foreach ($items as $item) {
    echo "ID: {$item->id}, Name: {$item->name}, Qty: {$item->quantity}, Group: " . ($item->attributes['promo_group_id'] ?? 'none') . "\n";
}

echo "\nAdding Product $pid1 as standalone item...\n";
$cartService->addOrUpdate($pid1, 1);

echo "\nUpdating quantity for standalone Product $pid1 (increase)...\n";
$cartService->updateQuantity($pid1, 'increase');

echo "Cart contents after update:\n";
$items = Cart::session($userId)->getContent();
foreach ($items as $item) {
    echo "ID: {$item->id}, Name: {$item->name}, Qty: {$item->quantity}, Group: " . ($item->attributes['promo_group_id'] ?? 'none') . "\n";
}

$bundleKey = null;
foreach ($items as $item) {
    if (str_contains($item->id, 'bundle')) {
        $bundleKey = $item->id;
        break;
    }
}

if ($bundleKey) {
    echo "\nUpdating quantity for bundle item $bundleKey (increase)...\n";
    $cartService->updateQuantity($bundleKey, 'increase');

    echo "Cart contents after bundle item update:\n";
    $items = Cart::session($userId)->getContent();
    foreach ($items as $item) {
        echo "ID: {$item->id}, Name: {$item->name}, Qty: {$item->quantity}, Group: " . ($item->attributes['promo_group_id'] ?? 'none') . "\n";
    }
}
