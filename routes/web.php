<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\AllProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ==========================================
// 1. หน้าทั่วไป (Public Routes)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/allproducts', [AllProductController::class, 'index'])->name('allproducts');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// ==========================================
// 2. ระบบตะกร้าสินค้า (Cart)
// ==========================================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// 🛠️ จุดที่แก้ไข: เปลี่ยนกลับเป็น Route::post เพื่อให้ตรงกับการส่งค่า (AJAX Fetch API) จากหน้าเว็บ
Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');

// ✅ Route สำหรับเพิ่มสินค้าแบบ Bundle (ซื้อคู่)
Route::post('/cart/bundle', [CartController::class, 'addBundleToCart'])->name('cart.addBundle');
// Route สำหรับอัปเดตและลบสินค้า
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
    Route::post('/payment/apply-discount', [PaymentController::class, 'applyDiscount'])->name('payment.applyDiscount');

    // หน้าแสดง QR Code และ Upload Slip
    Route::get('/payment/qr/{orderId}', [PaymentController::class, 'showQr'])->name('payment.qr');
    Route::post('/payment/refresh/{orderCode}', [PaymentController::class, 'refreshQr'])->name('payment.refresh');
    Route::post('/payment/cancel/{orderCode}', [PaymentController::class, 'cancelOrder'])->name('payment.cancel');
    Route::post('/payment/slip/upload/{orderCode}', [PaymentController::class, 'uploadSlip'])->name('payment.slip.upload');

    // -- กลุ่มที่ต้องกรอกข้อมูลส่วนตัวครบแล้ว --
    Route::middleware(['profile.completed'])->group(function () {
        // ประวัติการสั่งซื้อ (ฝั่งลูกค้า)
        Route::get('/orderhistory', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/order/{orderCode}', [OrderController::class, 'show'])->name('orders.show');

        // ✅ เพิ่ม Route สำหรับกดบันทึกคำสั่งซื้อ (เรียกไปที่ฟังก์ชัน store)
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
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
Route::get('/ordertracking', [TrackingController::class, 'index'])->name('order.tracking');
Route::get('/ordertracking/form', [TrackingController::class, 'index'])->name('order.tracking.form');

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
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Customer Management (จัดการลูกค้า)
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::resource('customers', CustomerController::class);

    // Product Management (จัดการสินค้า)
    Route::resource('products', AdminProductController::class)->parameters([
        'products' => 'product',
    ]);

    // Route สำหรับลบรูปสินค้า (Ajax)
    Route::delete('/products/image/{product_image}', [AdminProductController::class, 'destroyImage'])->name('products.image.destroy');

    // Favorite Image Deletion (Ajax)
    Route::delete('/favorite-images/{image}', [\App\Http\Controllers\Admin\FavoriteController::class, 'destroyImage'])->name('favorites.image.destroy');

    // Review Images
    Route::get('/products/{product}/review-images', [AdminProductController::class, 'showReviewImages'])->name('products.review-images.show');
    Route::post('/products/{product}/review-images', [AdminProductController::class, 'storeReviewImage'])->name('products.review-images.store');
    Route::delete('/products/review-images/{review_image}', [AdminProductController::class, 'destroyReviewImage'])->name('products.review-images.destroy');

    // Route สำหรับตั้งค่ารูปหลัก
    Route::post('/products/image/{image}/set-main', [AdminProductController::class, 'setMainImage'])->name('products.setMainImage');

    // Route สำหรับ Toggle สินค้าแนะนำ
    Route::post('/products/{product}/toggle-recommended', [AdminProductController::class, 'toggleRecommended'])->name('products.toggleRecommended');

    // ✅ เพิ่ม Route สำหรับกดเปลี่ยนสถานะเปิด/ปิดสินค้า
    Route::post('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggleStatus');

    // Promotion Management (จัดการโปรโมชั่น)
    Route::get('/promotions/logs', [App\Http\Controllers\Admin\PromotionLogController::class, 'index'])->name('promotions.logs');
    Route::post('/promotions/{promotion}/toggle-status', [PromotionController::class, 'toggleStatus'])->name('promotions.toggle-status');
    Route::resource('promotions', PromotionController::class);

    // FAQ Management (จัดการคำถามที่พบบ่อย)
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);

    // Homepage Content Management
    Route::get('/homepage-content/live-edit', [App\Http\Controllers\Admin\HomepageContentController::class, 'liveEdit'])->name('homepage-content.live-edit');
    Route::post('/homepage-content/{homepageContent}/update-value', [App\Http\Controllers\Admin\HomepageContentController::class, 'updateValue'])->name('homepage-content.updateValue');
    Route::resource('homepage-content', App\Http\Controllers\Admin\HomepageContentController::class);

    // Favorite Management (จัดการเกี่ยวกับติดใจ)
    Route::resource('favorites', \App\Http\Controllers\Admin\FavoriteController::class);

    // Contact Management (จัดการติดต่อเรา)
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class);

    // Admin Management (จัดการผู้ดูแลระบบ)
    Route::resource('admins', App\Http\Controllers\Admin\AdminManagementController::class)->middleware('is.superadmin');

    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');

    // Settings
    Route::get('/settings', [AdminController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminController::class, 'update'])->name('settings.update');

});
