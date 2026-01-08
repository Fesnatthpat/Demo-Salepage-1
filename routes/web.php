<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AllProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// --- 1. หน้าทั่วไป ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/allproducts', [AllProductController::class, 'index'])->name('allproducts');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// --- 2. ตะกร้าสินค้า ---
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/update/{id}/{action}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'removeItem'])->name('cart.remove');

// --- 3. Login/Logout ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login/line', [AuthController::class, 'redirectToLine'])->name('login.line');
Route::get('/callback/line', [AuthController::class, 'handleLineCallback'])->name('line.callback');


// --- 4. ส่วนที่ต้อง Login ---
Route::middleware(['auth'])->group(function () {

    // --- Checkout & Payment ---
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    
    // หน้าแสดง QR Code
    Route::get('/payment/qr/{orderId}', [PaymentController::class, 'showQr'])->name('payment.qr');
    
    // ★★★ [เพิ่มใหม่] Route สำหรับปุ่ม Refresh QR Code ★★★
    Route::post('/payment/refresh/{orderCode}', [PaymentController::class, 'refreshQr'])->name('payment.refresh');

    // แนบสลิป
    Route::post('/payment/slip/upload/{orderCode}', [PaymentController::class, 'uploadSlip'])->name('payment.slip.upload');


    // --- ประวัติการสั่งซื้อ ---
    Route::get('/orderhistory', [OrderController::class, 'index'])->name('order.history');
    Route::get('/order/{orderCode}', [OrderController::class, 'show'])->name('order.show');

    // --- Address Management ---
    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::post('/address', [AddressController::class, 'saveAddress'])->name('address.save');
    Route::put('/address/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
});

// --- General Tracking (Guest & Auth) ---
Route::get('/ordertracking', [OrderController::class, 'showTrackingForm'])->name('order.tracking.form');
Route::post('/ordertracking', [OrderController::class, 'trackOrder'])->name('order.tracking');


// --- API (สำหรับ Dropdown ที่อยู่) ---
Route::get('/api/amphures/{province_id}', [AddressController::class, 'getAmphures']);
Route::get('/api/districts/{amphure_id}', [AddressController::class, 'getDistricts']);


// --- 5. Admin Panel ---


use App\Http\Controllers\Admin\DashboardController;


use App\Http\Controllers\Admin\OrderController as AdminOrderController;





Route::prefix('admin')->name('admin.')->group(function () {


    // TODO: Add admin authentication middleware


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');





    // Order Management


    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');


    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');


    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');


});

