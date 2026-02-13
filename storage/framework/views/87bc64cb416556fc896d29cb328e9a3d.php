<?php $__env->startSection('title', 'สินค้าทั้งหมด | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>

    
    <style>
        /* --- 1. ปรับแต่งจุด (Pagination) --- */
        .mySwiper .swiper-pagination-bullet {
            background-color: #ffffff !important;
            opacity: 0.5 !important;
            width: 8px !important;
            height: 8px !important;
            transition: all 0.3s ease;
        }
        .mySwiper .swiper-pagination-bullet-active,
        .mySwiper .swiper-pagination-bullet-active-main {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.3);
        }

        /* --- 2. ปรับแต่งลูกศร Hero Slider (Banner) ให้ดูพรีเมียม --- */
        .mySwiper .swiper-button-next,
        .mySwiper .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: rgba(255, 255, 255, 0.9) !important; /* พื้นหลังสีขาว */
            border-radius: 50% !important;
            color: #dc2626 !important; /* สีลูกศรแดง */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -16px !important; /* จัดกึ่งกลางเป๊ะๆ */
        }
        .mySwiper .swiper-button-next:hover,
        .mySwiper .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }
        .mySwiper .swiper-button-next::after,
        .mySwiper .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: 900 !important;
        }

        /* --- 3. ปรับแต่งลูกศร Category Slider (แถบแดง) ให้เล็กลงและมินิมอล --- */
        .categorySwiper .swiper-button-next,
        .categorySwiper .swiper-button-prev {
            width: 42px !important;  /* เล็กลงพอดี ไม่เบียดเมนู */
            height: 42px !important; 
            background-color: #ffffff !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            top: 45% !important; /* จัดให้อยู่กลางไอคอน (ชดเชยที่มี pb-4 ด้านล่าง) */
            margin-top: -14px !important; 
            z-index: 10 !important;
        }
        .categorySwiper .swiper-button-next:hover,
        .categorySwiper .swiper-button-prev:hover {
            transform: scale(1.15) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
        }
        .categorySwiper .swiper-button-next::after,
        .categorySwiper .swiper-button-prev::after {
            font-size: 12px !important; /* ขนาดไอคอนลูกศรด้านใน */
            font-weight: 900 !important;
        }
        /* ขยับให้อยู่ขอบซ้ายขวาพอดี */
        .categorySwiper .swiper-button-prev { left: 4px !important; }
        .categorySwiper .swiper-button-next { right: 4px !important; }

        /* สไตล์ปุ่มที่กดไม่ได้ (ถึงปลายทางแล้ว) */
        .categorySwiper .swiper-button-disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
            transform: scale(1) !important;
        }
    </style>

    
    <div class="min-h-screen py-8 bg-cover bg-center bg-no-repeat bg-fixed"
        style="background-image: url('<?php echo e(asset('')); ?>');">

        <div class="container mx-auto px-4">

            
            <div class="flex flex-col gap-8">

                
                <main class="w-full">

                    
                    <div class="w-full mb-6 shadow-sm group relative rounded-[5px] overflow-hidden">
                        <div class="aspect-[16/9] md:aspect-[3/1] lg:aspect-[ 5/1] w-full relative">
                            <div class="swiper mySwiper w-full h-full absolute inset-0">
                                <div class="swiper-wrapper">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-1.png', 'th-2.png', 'th-3.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                        <div class="swiper-slide">
                                            <a href="#" class="block w-full h-full">
                                                <img src="<?php echo e(asset('images/' . $image)); ?>"
                                                    class="w-full h-full object-cover object-center"
                                                    alt="Banner <?php echo e($index + 1); ?>"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Banner+<?php echo e($index + 1); ?>';" />
                                            </a>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="w-full py-4 rounded-2xl mt-6 mb-8 shadow-md shadow-gray-300 bg-red-600 overflow-hidden relative group select-none">
                        <div class="container mx-auto px-8 md:px-10 relative">
                            <div class="swiper categorySwiper w-full pb-4">
                                <div class="swiper-wrapper items-start">
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
                                        <div class="swiper-slide !h-auto">
                                            <a href="/allproducts?category=<?php echo e(strip_tags($menu['name'])); ?>"
                                                class="flex flex-col items-center group w-full transition-transform duration-300 hover:scale-105">
                                                <div
                                                    class="w-12 h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 bg-gray-50 rounded-full flex items-center justify-center p-2 mb-2 shadow-sm transition-colors">
                                                    <img src="<?php echo e(asset('images/' . $menu['image'])); ?>"
                                                        alt="<?php echo e(strip_tags($menu['name'])); ?>"
                                                        class="w-full h-full object-contain"
                                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/150x150/fca5a5/ffffff?text=IMG';" />
                                                </div>
                                                <span
                                                    class="text-[10px] md:text-xs font-bold text-white text-center leading-tight select-none">
                                                    <?php echo $menu['name']; ?>

                                                </span>
                                            </a>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                
                                
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
                        <div class="mb-3 sm:mb-0">
                            <h2 class="text-gray-800 font-bold text-lg flex items-center gap-2">
                                สินค้าทั้งหมด
                                <span class="badge badge-outline text-xs font-normal text-gray-500">
                                    <?php echo e($products->total()); ?> รายการ
                                </span>
                            </h2>
                        </div>

                        <form id="sortForm" action="<?php echo e(route('allproducts')); ?>" method="GET"
                            class="flex items-center gap-3 w-full sm:w-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search')): ?>
                                <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('category')): ?>
                                <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <label class="text-sm text-gray-600 whitespace-nowrap hidden sm:block">เรียงตาม:</label>
                            <select name="sort" onchange="document.getElementById('sortForm').submit();"
                                class="select select-bordered select-sm w-full sm:w-48 bg-gray-50 focus:border-red-500 focus:ring-red-500 text-gray-700">
                                <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>ล่าสุด
                                    (Newest)
                                </option>
                                <option value="popular" <?php echo e(request('sort') == 'popular' ? 'selected' : ''); ?>>ยอดนิยม
                                    (Popular)</option>
                                <option value="bestseller" <?php echo e(request('sort') == 'bestseller' ? 'selected' : ''); ?>>ขายดี
                                    (Best Seller)</option>
                                <option value="price_asc" <?php echo e(request('sort') == 'price_asc' ? 'selected' : ''); ?>>
                                    ราคา: ต่ำ - สูง</option>
                                <option value="price_desc" <?php echo e(request('sort') == 'price_desc' ? 'selected' : ''); ?>>
                                    ราคา: สูง - ต่ำ</option>
                            </select>
                        </form>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->count() > 0): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product): ?>
                                    <?php
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
                                        class="card relative bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all rounded-xl overflow-hidden duration-300 group flex flex-col h-full">
                                        <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>">
                                            <figure class="relative aspect-[4/5] overflow-hidden bg-gray-100">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock <= 0): ?>
                                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                                        <div class="w-28 h-28 rounded-full bg-black bg-opacity-60 flex items-center justify-center shadow-lg">
                                                            <span class="text-white font-bold text-md text-center">สินค้าหมด</span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <img src="<?php echo e(Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath)); ?>"
                                                    alt="<?php echo e($product->pd_sp_name); ?>"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500 <?php echo e($product->pd_sp_stock <= 0 ? 'opacity-50' : ''); ?>"
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
                                                    class="hover:text-red-600 transition">
                                                    <?php echo e($product->pd_sp_name ?? 'Missing Product Name'); ?>

                                                </a>
                                            </h2>

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
            // Banner Swiper
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
                    nextEl: ".mySwiper .swiper-button-next",
                    prevEl: ".mySwiper .swiper-button-prev",
                },
            });

            // ★★★ Category Menu Swiper ★★★
            var swiperCategory = new Swiper(".categorySwiper", {
                slidesPerView: 4.5,
                spaceBetween: 10,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".categorySwiper .swiper-button-next",
                    prevEl: ".categorySwiper .swiper-button-prev",
                },
                breakpoints: {
                    640: { // แท็บเล็ต
                        slidesPerView: 6,
                        spaceBetween: 10,
                    },
                    1024: { // Desktop
                        slidesPerView: 8,
                        spaceBetween: 15,
                    },
                },
            });

            // Cart Logic
            const forms = document.querySelectorAll('.add-to-cart-form-listing');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;
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
                                if (typeof window.flyToCart === 'function') {
                                    window.flyToCart(submitBtn);
                                }
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว',
                                    text: 'สินค้าถูกเพิ่มเรียบร้อย',
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    background: '#FEF2F2',
                                    iconColor: '#DC2626'
                                });
                                setTimeout(() => { // Add setTimeout
                                    console.log(
                                        'Dispatching cartUpdated from allproducts.blade.php'
                                    ); // Log
                                    Livewire.dispatch('cartUpdated');
                                }, 50); // Small delay
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
                                confirmButtonColor: '#DC2626'
                            });
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent;
                        });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/allproducts.blade.php ENDPATH**/ ?>