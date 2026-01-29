<?php $__env->startSection('title', 'สินค้าทั้งหมด | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>

    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">

            
            <div class="flex flex-col lg:flex-row gap-8">

                
                <aside class="w-full lg:w-1/4">
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 sticky top-4">
                        <h3 class="font-bold text-lg mb-4 text-gray-700">ตัวกรองค้นหา</h3>
                        <form action="<?php echo e(route('allproducts')); ?>" method="GET">
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text">ค้นหาชื่อสินค้า</span></label>
                                <div class="relative">
                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                        placeholder="พิมพ์คำค้นหา..."
                                        class="input input-bordered w-full pr-10 bg-gray-50" />
                                    <button type="submit"
                                        class="absolute right-2 top-2.5 text-gray-400 hover:text-emerald-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="mb-4">
                                <label class="label"><span class="label-text font-bold">หมวดหมู่</span></label>
                                <ul class="menu bg-base-100 w-full p-0 text-gray-600">
                                    <li><a href="<?php echo e(route('allproducts')); ?>"
                                            class="<?php echo e(!request('category') ? 'active' : ''); ?>">ทั้งหมด</a></li>
                                    
                                    <?php if(isset($categories)): ?>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><a href="#"><?php echo e($cat); ?></a></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-block text-white mt-4 shadow-md">ค้นหา</button>
                        </form>
                    </div>
                </aside>

                
                <main class="w-full lg:w-3/4">
                    <div
                        class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 mb-6 flex justify-between items-center">
                        <span class="text-gray-500 text-sm hidden sm:inline">พบสินค้า <?php echo e($products->total()); ?> รายการ</span>
                    </div>

                    <?php if($products->count() > 0): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($product): ?>
                                    <?php
                                        // --- Logic for Eloquent Model ---
                                        $originalPrice = (float) ($product->pd_sp_price ?? 0);
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;

                                        // ✅ แก้ไข: ดึงรูปปกโดยใช้ img_sort (เรียงมากไปน้อย เอา 1 ขึ้นก่อน)
                                        $primaryImage = $product->images->sortByDesc('img_sort')->first();

                                        // ✅ แก้ไข: ใช้ img_path ตามฐานข้อมูลใหม่
                                        $imagePath = $primaryImage
                                            ? $primaryImage->img_path
                                            : 'https://via.placeholder.com/400x500.png?text=No+Image';
                                    ?>

                                    <div
                                        class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all rounded-b-md overflow-hidden duration-300 group flex flex-col h-full">
                                        <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>">
                                            <figure class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                                
                                                <img src="<?php echo e(Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath)); ?>"
                                                    alt="<?php echo e($product->pd_sp_name); ?>"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500" />

                                                <?php if($isOnSale): ?>
                                                    <div
                                                        class="absolute top-2 left-2 bg-red-500 p-2 rounded-2xl text-white gap-1 text-xs font-bold shadow-sm">
                                                        ลด ฿<?php echo e(number_format($discountAmount)); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <?php if($product->gifts_per_item): ?>
                                                    <div class="absolute top-2 right-2 bg-emerald-500 p-2 rounded-2xl text-white gap-1 text-xs font-bold shadow-sm">
                                                        แถม <?php echo e($product->gifts_per_item); ?> ชิ้น
                                                    </div>
                                                <?php endif; ?>
                                            </figure>
                                        </a>

                                        <div class="card-body p-4 flex flex-col flex-1">
                                            <h2
                                                class="card-title text-sm font-bold text-gray-800 leading-tight min-h-[2.5em] line-clamp-2">
                                                <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>"
                                                    class="hover:text-emerald-600 transition"><?php echo e($product->pd_sp_name ?? 'Missing Product Name'); ?></a>
                                            </h2>
                                            <p class="text-xs text-gray-500">Code:
                                                <?php echo e($product->pd_sp_code); ?></p>
                                            <p
                                                class="text-xs <?php echo e($product->pd_sp_stock > 0 ? 'text-emerald-600' : 'text-red-500'); ?>">
                                                <?php echo e($product->pd_sp_stock > 0 ? 'มีสินค้า' : 'สินค้าหมด'); ?>

                                            </p>
                                            
                                            <div class="mt-auto pt-2">
                                                <div class="flex flex-col mb-3">
                                                    <?php if($isOnSale): ?>
                                                        <span
                                                            class="text-lg font-bold text-emerald-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                        <span
                                                            class="text-xs text-gray-400 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                                    <?php else: ?>
                                                        <span
                                                            class="text-lg font-bold text-emerald-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <form class="add-to-cart-form-listing w-full"
                                                    data-action="<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit"
                                                        class="btn btn-sm w-full <?php echo e($product->pd_sp_stock > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'btn-disabled bg-gray-200'); ?> border-none shadow-sm flex items-center justify-center gap-2"
                                                        <?php echo e($product->pd_sp_stock <= 0 ? 'disabled' : ''); ?>>
                                                        <?php if($product->pd_sp_stock > 0): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            เพิ่มลงตะกร้า
                                                        <?php else: ?>
                                                            สินค้าหมด
                                                        <?php endif; ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <div
                                        class="card bg-gray-100 border border-gray-200 shadow-sm rounded-b-md overflow-hidden flex flex-col h-full p-4 items-center justify-center text-center">
                                        <p class="text-sm text-gray-500">Error rendering product.</p>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <div class="mt-8 flex justify-center">
                            <?php echo e($products->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-20 bg-white rounded-lg border border-dashed">
                            <p class="text-gray-500 text-lg">ไม่พบสินค้าที่คุณค้นหา</p>
                        </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.add-to-cart-form-listing');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;

                    // เพิ่ม Effect กดปุ่มให้รู้ว่ากดแล้ว
                    const originalBtnContent = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="loading loading-spinner loading-xs"></span> กำลังเพิ่ม...';

                    const formData = new FormData();
                    formData.append('quantity', quantity);

                    fetch(actionUrl, { // Use actionUrl directly for POST
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 1. Animation Fly (ถ้ามี)
                                if (typeof window.flyToCart === 'function') {
                                    window.flyToCart(submitBtn);
                                }

                                // 2. Popup Success
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว',
                                    text: 'สินค้าถูกเพิ่มเรียบร้อย',
                                    position: 'center',
                                    showConfirmButton: false,
                                    timer: 1500
                                });

                                // 3. Update Badge
                                setTimeout(() => {
                                    if (window.updateCartBadge) {
                                        window.updateCartBadge(data.cartCount);
                                    }
                                }, 500);
                            } else {
                                throw new Error(data.message || 'Unknown error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเพิ่มสินค้าได้',
                                position: 'center',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        })
                        .finally(() => {
                            // คืนค่าปุ่มกลับสู่สภาพเดิม
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent;
                        });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/allproducts.blade.php ENDPATH**/ ?>