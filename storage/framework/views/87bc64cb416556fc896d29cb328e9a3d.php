<?php $__env->startSection('title', 'สินค้าทั้งหมด | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>

    <div class="bg-gray-50 min-h-screen py-8">
        <div class="container mx-auto px-4">

            
            <div class="flex flex-col lg:flex-row gap-8">

                
                <aside class="w-full lg:w-1/4">
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 sticky top-24">
                        <h3 class="font-bold text-lg mb-4 text-gray-800">ตัวกรองค้นหา</h3>
                        <form action="<?php echo e(route('allproducts')); ?>" method="GET">
                            <div class="form-control mb-4">
                                <label class="label"><span class="label-text text-gray-600">ค้นหาชื่อสินค้า</span></label>
                                <div class="relative">
                                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                        placeholder="พิมพ์คำค้นหา..."
                                        class="input input-bordered w-full pr-10 bg-gray-50 focus:border-red-500 focus:ring-red-500" />
                                    <button type="submit"
                                        class="absolute right-2 top-2.5 text-gray-400 hover:text-red-600 transition-colors">
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
                                <label class="label"><span
                                        class="label-text font-bold text-gray-700">หมวดหมู่</span></label>
                                <ul class="menu bg-base-100 w-full p-0 text-gray-600 rounded-box">
                                    <li>
                                        <a href="<?php echo e(route('allproducts')); ?>"
                                            class="<?php echo e(!request('category') ? 'active bg-red-100 text-red-700' : 'hover:bg-red-50 hover:text-red-600'); ?>">
                                            ทั้งหมด
                                        </a>
                                    </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($categories)): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <li>
                                                <a href="<?php echo e(route('allproducts', ['category' => $cat])); ?>"
                                                    class="<?php echo e(request('category') == $cat ? 'active bg-red-100 text-red-700' : 'hover:bg-red-50 hover:text-red-600'); ?>">
                                                    <?php echo e($cat); ?>

                                                </a>
                                            </li>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </div>
                            <button type="submit"
                                class="btn btn-block bg-red-600 hover:bg-red-700 border-none text-white mt-4 shadow-md">
                                ค้นหา
                            </button>
                        </form>
                    </div>
                </aside>

                
                <main class="w-full lg:w-3/4">

                    
                    <div
                        class="w-full h-[150px] md:h-[250px] lg:h-[300px] rounded-2xl overflow-hidden mb-6 shadow-sm group relative">
                        <div class="swiper mySwiper w-full h-full">
                            <div class="swiper-wrapper">
                                
                                <div class="swiper-slide">
                                    <a href="#" class="block w-full">
                                        <img src="<?php echo e(asset('images/th-1.png')); ?>" class="w-full object-cover object-center"
                                            alt="โปรโมชั่น Sale"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/800x300/783630/ffffff?text=Banner+1';" />
                                    </a>
                                </div>
                                
                                <div class="swiper-slide">
                                    <a href="#" class="block w-full">
                                        <img src="<?php echo e(asset('images/th-2.png')); ?>" class="w-full object-cover object-center"
                                            alt="จัดส่งวันไหน"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/800x300/ef4444/ffffff?text=Banner+2';" />
                                    </a>
                                </div>
                                
                                <div class="swiper-slide">
                                    <a href="#" class="block w-full">
                                        <img src="<?php echo e(asset('images/th-3.png')); ?>" class="w-full object-cover object-center"
                                            alt="ขอขอบคุณลูกค้า"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/800x300/ef4444/ffffff?text=Banner+3';" />
                                    </a>
                                </div>
                            </div>
                            
                            <div class="swiper-button-next !w-8 !h-8 !after:text-xs md:!w-10 md:!h-10"></div>
                            <div class="swiper-button-prev !w-8 !h-8 !after:text-xs md:!w-10 md:!h-10"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>

                    
                    <div class="w-full  py-4 rounded-2xl mt-10 mb-10 shadow-lg ">
                        <div class="container mx-auto px-4">
                            <div class="grid grid-cols-5 lg:grid-cols-10 gap-2 justify-items-center items-start">
                                <?php
                                    $menuItems = [
                                        ['name' => 'กิมจิ', 'image' => 'menu-kimchi.png'],
                                        ['name' => 'ซอส<br>เกาหลี', 'image' => 'menu-korean-sauce.png'],
                                        ['name' => 'combo<br>set', 'image' => 'menu-combo.png'],
                                        ['name' => 'น้ำดอง<br>ผักดอง', 'image' => 'menu-pickle.png'],
                                        ['name' => 'เครื่องปรุง<br>เกาหลี', 'image' => 'menu-korean-seasoning.png'],
                                        ['name' => 'แป้ง/ข้าว/<br>เส้น', 'image' => 'menu-flour.png'],
                                        ['name' => 'สาหร่าย', 'image' => 'menu-seaweed.png'],
                                        ['name' => 'เครื่อง<br>ครัว', 'image' => 'menu-kitchenware.png'],
                                        ['name' => 'ซอส<br>ญี่ปุ่น', 'image' => 'menu-japan-sauce.png'],
                                        ['name' => 'เครื่องปรุง<br>ญี่ปุ่น', 'image' => 'menu-japan-seasoning.png'],
                                    ];
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <a href="/allproducts?category=<?php echo e(strip_tags($menu['name'])); ?>"
                                        class="flex flex-col items-center group w-full transition-transform duration-300 hover:scale-105">
                                        <div
                                            class="w-12 h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 bg-white hover:bg-red-600 rounded-full flex items-center justify-center p-1.5 mb-1 shadow-sm">
                                            <img src="<?php echo e(asset('images/' . $menu['image'])); ?>"
                                                alt="<?php echo e(strip_tags($menu['name'])); ?>" class="w-full h-full object-contain"
                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/150x150/fca5a5/ffffff?text=IMG';" />
                                        </div>
                                        <span class="text-[10px] md:text-xs font-bold text-red-600 text-center leading-tight">
                                            <?php echo $menu['name']; ?>

                                        </span>
                                    </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->count() > 0): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product): ?>
                                    <?php
                                        // --- Logic for Eloquent Model ---
                                        $originalPrice = (float) ($product->pd_sp_price ?? 0);
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;

                                        $primaryImage = $product->images->sortBy('img_sort')->first();
                                        $imagePath = $primaryImage
                                            ? $primaryImage->img_path
                                            : 'https://via.placeholder.com/400x500.png?text=No+Image';
                                    ?>

                                    <div
                                        class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all rounded-xl overflow-hidden duration-300 group flex flex-col h-full">
                                        <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>">
                                            <figure class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                                <img src="<?php echo e(Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath)); ?>"
                                                    alt="<?php echo e($product->pd_sp_name); ?>"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x500.png?text=No+Image';" />

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                    <div
                                                        class="absolute top-2 left-2 bg-red-600 px-2 py-1 rounded-lg text-white text-xs font-bold shadow-sm">
                                                        ลด ฿<?php echo e(number_format($discountAmount)); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->gifts_per_item): ?>
                                                    <div
                                                        class="absolute top-2 right-2 bg-red-500 px-2 py-1 rounded-lg text-white text-xs font-bold shadow-sm flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z"
                                                                clip-rule="evenodd" />
                                                            <path
                                                                d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z" />
                                                        </svg>
                                                        แถม <?php echo e($product->gifts_per_item); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </figure>
                                        </a>

                                        <div class="card-body p-4 flex flex-col flex-1">
                                            <h2
                                                class="card-title text-sm font-bold text-gray-800 leading-tight min-h-[2.5em] line-clamp-2">
                                                <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>"
                                                    class="hover:text-red-600 transition"><?php echo e($product->pd_sp_name ?? 'Missing Product Name'); ?></a>
                                            </h2>
                                            <p class="text-xs text-gray-500 mt-1">Code: <?php echo e($product->pd_sp_code); ?></p>

                                            <p
                                                class="text-xs font-medium mt-1 <?php echo e($product->pd_sp_stock > 0 ? 'text-green-600' : 'text-red-500'); ?>">
                                                <?php echo e($product->pd_sp_stock > 0 ? '● มีสินค้า' : '● สินค้าหมด'); ?>

                                            </p>

                                            <div class="mt-auto pt-3 border-t border-gray-50">
                                                <div class="flex flex-col mb-3">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                        <div class="flex items-center gap-2">
                                                            <span
                                                                class="text-lg font-bold text-red-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                            <span
                                                                class="text-xs text-gray-400 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                                        </div>
                                                    <?php else: ?>
                                                        <span
                                                            class="text-lg font-bold text-red-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                <form class="add-to-cart-form-listing w-full"
                                                    data-action="<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit"
                                                        class="btn btn-sm w-full <?php echo e($product->pd_sp_stock > 0 ? 'bg-red-600 hover:bg-red-700 text-white' : 'btn-disabled bg-gray-200 text-gray-400'); ?> border-none shadow-sm flex items-center justify-center gap-2"
                                                        <?php echo e($product->pd_sp_stock <= 0 ? 'disabled' : ''); ?>>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock > 0): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            เพิ่มลงตะกร้า
                                                        <?php else: ?>
                                                            สินค้าหมด
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div
                                        class="card bg-gray-50 border border-gray-200 shadow-sm rounded-xl overflow-hidden flex flex-col h-full p-4 items-center justify-center text-center">
                                        <p class="text-sm text-gray-400">ข้อมูลสินค้าไม่สมบูรณ์</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="mt-10 flex justify-center">
                            <?php echo e($products->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div
                            class="flex flex-col items-center justify-center py-20 bg-white rounded-xl border-2 border-dashed border-gray-200 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="text-lg font-bold text-gray-600">ไม่พบสินค้าที่คุณค้นหา</h3>
                            <p class="text-gray-500">ลองเปลี่ยนคำค้นหา หรือเลือกหมวดหมู่อื่น</p>
                            <a href="<?php echo e(route('allproducts')); ?>"
                                class="btn btn-sm btn-outline border-red-500 text-red-500 hover:bg-red-500 hover:text-white mt-4">ล้างคำค้นหา</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialize Swiper
            var swiper1 = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                speed: 800,
                effect: 'slide',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });

            // 2. จัดการ Cart
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

                    fetch(actionUrl, {
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
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    background: '#FEF2F2', // พื้นหลังสีแดงอ่อน
                                    iconColor: '#DC2626' // ไอคอนสีแดง
                                });

                                // 3. Dispatch Livewire event
                                setTimeout(() => {
                                    Livewire.dispatch('cartUpdated');
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
                                timer: 1500,
                                confirmButtonColor: '#DC2626' // ปุ่มยืนยันสีแดง
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