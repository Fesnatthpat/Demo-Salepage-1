<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\CustomerController;
// --- Controllers ฝั่งหน้าบ้าน (Frontend) ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\AllProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController; // Controller ฝั่งหน้าบ้าน (Order History)
use App\Http\Controllers\OrderController;
// --- Controllers ฝั่งหลังบ้าน (Admin) ---
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController; // ตั้งชื่อใหม่กันชนกับ OrderController หน้าบ้าน
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;





// ==========================================
// 1. หน้าทั่วไป (Public Routes)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/allproducts', [AllProductController::class, 'index'])->name('allproducts');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// ==========================================
// 2. ระบบตะกร้าสินค้า (Cart)
// ==========================================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');

// ✅ Route สำหรับเพิ่มสินค้าแบบ Bundle (ซื้อคู่) - ต้องมีบรรทัดนี้
Route::post('/cart/bundle', [CartController::class, 'addBundleToCart'])->name('cart.addBundle');

// Route สำหรับอัปเดตและลบสินค้า (เหลือชุดเดียว ไม่ซ้ำซ้อนแล้ว)
Route::patch('/cart/update/{id}/{action}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'removeItem'])->name('cart.remove');

// ==========================================
// 3. ระบบสมาชิก (Login/Logout)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Line Login
Route::get('/login/line', [AuthController::class, 'redirectToLine'])->name('login.line');
Route::get('/callback/line', [AuthController::class, 'handleLineCallback'])->name('line.callback');

// ==========================================
// 4. ส่วนที่ต้องเข้าสู่ระบบ (Authenticated Users)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // -- กรอกข้อมูลส่วนตัวเพิ่มเติม --
    Route::get('/profile/complete', [ProfileController::class, 'create'])->name('profile.completion');
    Route::post('/profile/complete', [ProfileController::class, 'store'])->name('profile.store');

    // -- แก้ไขข้อมูลส่วนตัว --
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // -- ระบบชำระเงิน (Checkout & Payment) --
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

    // หน้าแสดง QR Code และ Upload Slip
    Route::get('/payment/qr/{orderId}', [PaymentController::class, 'showQr'])->name('payment.qr');
    Route::post('/payment/refresh/{orderCode}', [PaymentController::class, 'refreshQr'])->name('payment.refresh');
    Route::post('/payment/slip/upload/{orderCode}', [PaymentController::class, 'uploadSlip'])->name('payment.slip.upload');

    // -- กลุ่มที่ต้องกรอกข้อมูลส่วนตัวครบแล้ว --
    Route::middleware(['profile.completed'])->group(function () {
        // ประวัติการสั่งซื้อ (ฝั่งลูกค้า)
        Route::get('/orderhistory', [OrderController::class, 'index'])->name('order.history');
        Route::get('/order/{orderCode}', [OrderController::class, 'show'])->name('order.show');
    });

    // -- จัดการที่อยู่ (Address) --
    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::post('/address', [AddressController::class, 'saveAddress'])->name('address.save');
    Route::put('/address/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
});

// ==========================================
// 5. ติดตามพัสดุ (Tracking) - Guest เข้าได้
// ==========================================
Route::get('/ordertracking', [OrderController::class, 'showTrackingForm'])->name('order.tracking.form');
Route::post('/ordertracking', [OrderController::class, 'trackOrder'])->name('order.tracking');

// ==========================================
// 6. API สำหรับ Dropdown ที่อยู่ (Ajax)
// ==========================================
Route::get('/api/amphures/{province_id}', [AddressController::class, 'getAmphures']);
Route::get('/api/districts/{amphure_id}', [AddressController::class, 'getDistricts']);

// ==========================================
// 7. Admin Panel (ระบบหลังบ้าน)
// ==========================================
Route::get('admin/login', [App\Http\Controllers\Admin\AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [App\Http\Controllers\Admin\AdminController::class, 'login']);
Route::post('admin/logout', [App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');

    // Order Management (จัดการออเดอร์)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    // Route นี้จะส่ง {order} (ซึ่งคือ ID) ไปให้ method show($id) ใน Controller
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Customer Management (จัดการลูกค้า)
    Route::resource('customers', CustomerController::class);

    // Product Management (จัดการสินค้า)
    Route::resource('products', AdminProductController::class)->parameters([
        'products' => 'product',
    ]);
    // Route สำหรับลบรูปสินค้า (Ajax)
    Route::delete('/products/image/{product_image}', [AdminProductController::class, 'destroyImage'])->name('products.image.destroy');

    // Promotion Management (จัดการโปรโมชั่น)
    Route::resource('promotions', PromotionController::class);

    // Admin Management
    Route::resource('admins', App\Http\Controllers\Admin\AdminManagementController::class)->middleware('is.superadmin');

    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

});
