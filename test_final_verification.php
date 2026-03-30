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
$pid1 = $pids[0];
$pid2 = $pids[1];

// Ensure stock
StockProduct::updateOrCreate(['pd_sp_id' => $pid1, 'option_id' => null], ['quantity' => 100]);
StockProduct::updateOrCreate(['pd_sp_id' => $pid2, 'option_id' => null], ['quantity' => 100]);

$cartService = app(CartService::class);
$userId = $cartService->getUserId();
Cart::session($userId)->clear();

echo "1. Adding Bundle 1 (Product $pid1 + Product $pid2 as gift)...\n";
$cartService->addBundle($pid1, 0, [$pid2]);

echo "2. Adding Bundle 2 (Product $pid1 + Product $pid2 as gift)...\n";
$cartService->addBundle($pid1, 0, [$pid2]);

echo "3. Adding Product $pid1 as standalone...\n";
$cartService->addOrUpdate($pid1, 1);

$items = Cart::session($userId)->getContent();
echo "Cart contents (should have 5 rows: 2 per bundle + 1 standalone):\n";
foreach ($items as $item) {
    echo "ID: {$item->id}, Name: {$item->name}, Qty: {$item->quantity}, Group: " . ($item->attributes['promo_group_id'] ?? 'none') . "\n";
}

$bundle1Keys = $items->filter(fn($i) => ($i->attributes['promo_group_id'] ?? '') === $items->first()->attributes['promo_group_id'])->keys()->toArray();
$standaloneKey = $pid1;

echo "\n4. Updating quantity of Bundle 1 item (first item in bundle)...\n";
$cartService->updateQuantity($bundle1Keys[0], 'increase');

$items = Cart::session($userId)->getContent();
echo "Check: Only ONE bundle item should have Qty 2:\n";
foreach ($items as $item) {
    echo "ID: {$item->id}, Qty: {$item->quantity}\n";
}

echo "\n5. Removing Bundle 1 by removing its main item...\n";
$cartService->removeItem($bundle1Keys[0]);

$items = Cart::session($userId)->getContent();
echo "Check: Bundle 1 should be completely gone (2 rows removed):\n";
foreach ($items as $item) {
    echo "ID: {$item->id}\n";
}

echo "\n6. Final Cart Count: " . Cart::session($userId)->getTotalQuantity() . "\n";
