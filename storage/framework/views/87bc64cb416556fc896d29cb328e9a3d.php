<?php $__env->startSection('title', 'สินค้าทั้งหมด | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>

    
    <style>
        /* --- 1. Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet {
            background-color: #ffffff !important;
            opacity: 0.5 !important;
            width: 6px !important;
            height: 6px !important;
            transition: all 0.3s ease;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-pagination-bullet {
                width: 8px !important;
                height: 8px !important;
            }
        }

        .mySwiper .swiper-pagination-bullet-active,
        .mySwiper .swiper-pagination-bullet-active-main {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.3);
        }

        .mySwiper .swiper-button-next,
        .mySwiper .swiper-button-prev {
            width: 28px !important;
            height: 28px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -14px !important;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-button-next,
            .mySwiper .swiper-button-prev {
                width: 32px !important;
                height: 32px !important;
                margin-top: -16px !important;
            }
        }

        .mySwiper .swiper-button-next:hover,
        .mySwiper .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        .mySwiper .swiper-button-next::after,
        .mySwiper .swiper-button-prev::after {
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        @media (min-width: 640px) {
            .mySwiper .swiper-button-next::after,
            .mySwiper .swiper-button-prev::after {
                font-size: 14px !important;
            }
        }

        /* Category Slider Arrows - ซ่อนบนมือถือเพื่อลดความเกะกะ */
        @media (max-width: 767px) {
            .categorySwiper .swiper-button-next,
            .categorySwiper .swiper-button-prev {
                display: none !important;
            }
        }

        .categorySwiper .swiper-button-next,
        .categorySwiper .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: #ffffff !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            top: 50% !important;
            margin-top: -16px !important;
            z-index: 10 !important;
        }

        .categorySwiper .swiper-button-next::after,
        .categorySwiper .swiper-button-prev::after {
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        .categorySwiper .swiper-button-prev {
            left: 4px !important;
        }

        .categorySwiper .swiper-button-next {
            right: 4px !important;
        }

        .categorySwiper .swiper-button-disabled {
            opacity: 0.3 !important;
            cursor: not-allowed !important;
        }

        /* --- 2. Custom Product Card Styles (Mobile Optimization) --- */
        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem; /* ความสูงเผื่อ 2 บรรทัดสำหรับฟอนต์เล็ก */
            line-height: 1.25;
        }

        @media (min-width: 640px) {
            .product-title-fixed {
                min-height: 2.8rem;
            }
        }
    </style>

    
    <div class="min-h-screen py-4 md:py-8 bg-cover bg-center bg-no-repeat bg-fixed bg-gray-50/50"
        style="background-image: url('<?php echo e(asset('')); ?>');">

        <div class="container mx-auto px-3 sm:px-4 md:px-6 max-w-7xl">

            <div class="flex flex-col gap-5 md:gap-8">

                <main class="w-full">

                    
                    <div class="w-full pb-4 sm:pb-6 pt-2 sm:pt-4 bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100">
                        <div class="px-2 sm:px-4">
                            <div class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-50 group rounded-xl sm:rounded-2xl overflow-hidden shadow-inner border border-gray-100">
                                <div class="swiper mySwiper w-full h-full absolute inset-0">
                                    <div class="swiper-wrapper">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="swiper-slide">
                                                    <a href="<?php echo e($slide->link_url ?? '/allproducts'); ?>" class="block w-full h-full bg-gray-50">
                                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>"
                                                            class="w-full h-full object-contain object-center"
                                                            alt="<?php echo e($slide->title ?? 'Slide'); ?>"
                                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Banner+Image';" />
                                                    </a>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php else: ?>
                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="swiper-slide">
                                                    <a href="/allproducts" class="block w-full h-full bg-gray-50">
                                                        <img src="<?php echo e(asset('images/' . $img)); ?>"
                                                            class="w-full h-full object-contain object-center"
                                                            alt="Slide"
                                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Welcome+Banner';" />
                                                    </a>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="w-full py-3 sm:py-4 rounded-xl mt-4 mb-5 shadow-sm bg-red-600 overflow-hidden relative group select-none">
                        <div class="container mx-auto px-1 sm:px-2 relative">
                            <div class="swiper categorySwiper w-full pb-1 sm:pb-2">
                                <div class="swiper-wrapper items-start">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($dbCategories) && $dbCategories->count() > 0): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dbCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                            <div class="swiper-slide !h-auto">
                                                <a href="/allproducts?category=<?php echo e($menu->name); ?>"
                                                    class="flex flex-col items-center group w-full transition-transform duration-300 active:scale-95 px-1 sm:px-2 md:px-4">
                                                    <div class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 bg-gray-50 rounded-full flex items-center justify-center p-1.5 sm:p-2 mb-1.5 sm:mb-2 shadow-sm transition-colors overflow-hidden border border-red-500/30">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($menu->image_path): ?>
                                                            <img src="<?php echo e(Storage::url($menu->image_path)); ?>"
                                                                alt="<?php echo e($menu->name); ?>"
                                                                class="w-full h-full object-contain"
                                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/150x150/fca5a5/ffffff?text=IMG';" />
                                                        <?php else: ?>
                                                            <i class="<?php echo e($menu->icon ?? 'fas fa-th-large'); ?> text-red-600 text-lg sm:text-xl md:text-2xl"></i>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                    <span class="text-[9px] sm:text-[10px] md:text-xs font-bold text-white text-center leading-tight select-none">
                                                        <?php echo nl2br(e($menu->name)); ?>

                                                    </span>
                                                </a>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php else: ?>
                                        <div class="text-white text-xs w-full text-center py-4">ยังไม่มีหมวดหมู่</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 mb-5 gap-3 sm:gap-4">
                        <div>
                            <h2 class="text-gray-800 font-extrabold text-base sm:text-lg md:text-xl flex items-center gap-2">
                                สินค้าทั้งหมด
                                <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md text-[10px] sm:text-xs font-bold border border-gray-200">
                                    <?php echo e($products->total()); ?> รายการ
                                </span>
                            </h2>
                        </div>

                        <form id="sortForm" action="<?php echo e(route('allproducts')); ?>" method="GET" class="w-full sm:w-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search')): ?>
                                <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('category')): ?>
                                <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="flex items-center gap-2 w-full">
                                <label class="text-xs sm:text-sm font-bold text-gray-500 whitespace-nowrap shrink-0"><i class="fas fa-sort-amount-down-alt mr-1"></i> เรียงตาม:</label>
                                <select name="sort" onchange="document.getElementById('sortForm').submit();"
                                    class="w-full sm:w-48 bg-gray-50 border border-gray-200 text-gray-700 text-xs sm:text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none">
                                    <option value="newest" <?php echo e(request('sort') == 'newest' ? 'selected' : ''); ?>>ล่าสุด</option>
                                    <option value="popular" <?php echo e(request('sort') == 'popular' ? 'selected' : ''); ?>>ยอดนิยม</option>
                                    <option value="bestseller" <?php echo e(request('sort') == 'bestseller' ? 'selected' : ''); ?>>ขายดี</option>
                                    <option value="price_asc" <?php echo e(request('sort') == 'price_asc' ? 'selected' : ''); ?>>ราคา: ต่ำ - สูง</option>
                                    <option value="price_desc" <?php echo e(request('sort') == 'price_desc' ? 'selected' : ''); ?>>ราคา: สูง - ต่ำ</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->count() > 0): ?>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 md:gap-6">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product): ?>
                                    <?php
                                        // 1. Logic ราคาและ Options
                                        $hasOptions = isset($product->options) && $product->options->count() > 0;
                                        if ($hasOptions) {
                                            $originalPrice = (float) $product->options->min('option_price');
                                        } else {
                                            $originalPrice = (float) ($product->pd_sp_price ?? 0);
                                        }
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;

                                        // 2. รูปภาพ
                                        $primaryImage = $product->images->sortBy('img_sort')->first();
                                        $imagePath = $primaryImage
                                            ? $primaryImage->img_path
                                            : 'https://via.placeholder.com/400x500.png?text=No+Image';
                                    ?>

                                    <div class="relative bg-white border border-gray-100 shadow-sm hover:shadow-lg hover:border-red-100 transition-all duration-300 rounded-xl sm:rounded-2xl overflow-hidden flex flex-col h-full group">
                                        
                                        <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>" class="block relative aspect-square overflow-hidden bg-gray-50/50">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($product->pd_sp_stock ?? 0) <= 0): ?>
                                                <div class="absolute inset-0 flex items-center justify-center z-10 bg-white/60 backdrop-blur-[2px]">
                                                    <span class="bg-gray-800 text-white text-[10px] sm:text-xs px-3 sm:px-4 py-1 sm:py-1.5 rounded-full font-bold uppercase tracking-wider shadow-md">สินค้าหมด</span>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <img src="<?php echo e(Str::startsWith($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath)); ?>"
                                                alt="<?php echo e($product->pd_sp_name); ?>"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-in-out <?php echo e(($product->pd_sp_stock ?? 0) <= 0 ? 'grayscale opacity-60' : ''); ?>"
                                                onerror="this.onerror=null;this.src='https://via.placeholder.com/400x500.png?text=No+Image';" />

                                            
                                            <div class="absolute top-2 left-2 flex flex-col gap-1.5 items-start z-10">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                    <div class="bg-red-600 text-white px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[9px] sm:text-[10px] font-black shadow-sm uppercase tracking-wide border border-red-500">
                                                        ลด ฿<?php echo e(number_format($discountAmount)); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->gifts_per_item): ?>
                                                    <div class="bg-pink-500 text-white px-1.5 py-0.5 sm:px-2 sm:py-1 rounded text-[9px] sm:text-[10px] font-black shadow-sm flex items-center gap-1 uppercase tracking-wide border border-pink-400">
                                                        <i class="fas fa-gift text-[8px] sm:text-[9px]"></i> แถม <?php echo e($product->gifts_per_item); ?>

                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </a>

                                        <div class="p-3 sm:p-4 flex flex-col flex-1">
                                            <h2 class="text-xs sm:text-sm font-bold text-gray-800 leading-tight product-title-fixed mb-1 sm:mb-2 group-hover:text-red-600 transition-colors">
                                                <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>">
                                                    <?php echo e($product->pd_sp_name ?? 'Product Name'); ?>

                                                </a>
                                            </h2>

                                            
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="text-[9px] sm:text-[10px] font-bold <?php echo e(($product->pd_sp_stock ?? 0) > 0 ? 'text-emerald-500' : 'text-red-500'); ?> flex items-center gap-1">
                                                    <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
                                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 <?php echo e(($product->pd_sp_stock ?? 0) > 0 ? 'bg-emerald-400' : 'hidden'); ?>"></span>
                                                      <span class="relative inline-flex rounded-full h-1.5 w-1.5 sm:h-2 sm:w-2 <?php echo e(($product->pd_sp_stock ?? 0) > 0 ? 'bg-emerald-500' : 'bg-red-500'); ?>"></span>
                                                    </span>
                                                    <?php echo e(($product->pd_sp_stock ?? 0) > 0 ? 'มีสินค้า' : 'หมด'); ?>

                                                </p>
                                                <p class="text-[9px] sm:text-[10px] font-medium text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded">
                                                    ขายแล้ว <?php echo e(number_format($product->pd_sp_sold ?? 0)); ?>

                                                </p>
                                            </div>

                                            <div class="mt-auto pt-2 sm:pt-3 border-t border-gray-100/80">
                                                
                                                <div class="flex flex-wrap items-baseline justify-between w-full mb-2.5 sm:mb-3 gap-x-1 gap-y-0.5">
                                                    <div class="flex items-baseline gap-1">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOptions): ?>
                                                            <span class="text-[9px] sm:text-[10px] text-gray-400 font-bold">เริ่ม</span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <span class="text-base sm:text-lg font-black text-red-600 tracking-tight leading-none">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                    </div>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                        <span class="text-[9px] sm:text-[11px] font-bold text-gray-400 line-through decoration-gray-300">฿<?php echo e(number_format($originalPrice)); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($product->pd_sp_stock ?? 0) <= 0): ?>
                                                    
                                                    <button disabled
                                                        class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                                                        <i class="fas fa-ban opacity-70"></i> สินค้าหมด
                                                    </button>

                                                <?php elseif($hasOptions): ?>
                                                    
                                                    <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>" 
                                                        class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 hover:border-red-600 shadow-sm hover:shadow-red-500/30">
                                                        <i class="fas fa-list-ul"></i> เลือกตัวเลือก
                                                    </a>

                                                <?php else: ?>
                                                    
                                                    <form class="add-to-cart-form-listing w-full" data-action="<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit"
                                                            class="w-full rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs transition-all flex items-center justify-center gap-1.5 h-9 sm:h-10 min-h-[36px] sm:min-h-[40px] bg-red-50 text-red-600 hover:bg-red-600 hover:text-white border border-red-100 hover:border-red-600 shadow-sm hover:shadow-red-500/30">
                                                            <i class="fas fa-cart-plus text-sm"></i> เพิ่มลงตะกร้า
                                                        </button>
                                                    </form>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>

                        <div class="mt-8 sm:mt-12 flex justify-center">
                            <?php echo e($products->appends(request()->query())->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-16 sm:py-24 bg-white rounded-2xl border-2 border-dashed border-gray-200 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-box-open text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-700 mb-2">ไม่พบสินค้าที่คุณค้นหา</h3>
                            <p class="text-xs sm:text-sm text-gray-400 mb-6">ลองเปลี่ยนคำค้นหา หรือเลือกดูหมวดหมู่สินค้าอื่นๆ</p>
                            <a href="<?php echo e(route('allproducts')); ?>" class="px-6 py-2.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white font-bold rounded-xl transition-colors border border-red-100 hover:border-red-600 shadow-sm">
                                ล้างคำค้นหาและดูสินค้าทั้งหมด
                            </a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Banner
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".mySwiper .swiper-button-next",
                    prevEl: ".mySwiper .swiper-button-prev"
                },
            });

            // ★★★ Category Responsive Breakpoints ★★★
            new Swiper(".categorySwiper", {
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                navigation: {
                    nextEl: ".categorySwiper .swiper-button-next",
                    prevEl: ".categorySwiper .swiper-button-prev"
                },
                breakpoints: {
                    0: { slidesPerView: 3.5, spaceBetween: 8 },
                    480: { slidesPerView: 4.5, spaceBetween: 10 },
                    640: { slidesPerView: 6, spaceBetween: 12 },
                    1024: { slidesPerView: 8, spaceBetween: 16 },
                },
            });

            // Cart Logic 
            const forms = document.querySelectorAll('.add-to-cart-form-listing');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // 🛠️ จุดที่แก้ไข: เอาเงื่อนไขที่คอยจับคำว่า "เลือกตัวเลือก" ออกไปเลย 
                    // เพราะเราแยกปุ่มนั้นไปเป็น <a> แท็กแล้ว
                    e.preventDefault();
                    
                    const currentForm = this;
                    const submitBtn = currentForm.querySelector('button[type="submit"]');
                    const actionUrl = currentForm.getAttribute('data-action');
                    const quantity = currentForm.querySelector('[name="quantity"]').value;
                    const originalBtnContent = submitBtn.innerHTML;

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังเพิ่ม...';

                    const formData = new FormData();
                    formData.append('quantity', quantity);

                    fetch(actionUrl, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (typeof window.flyToCart === 'function') window.flyToCart(submitBtn);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มลงตะกร้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                setTimeout(() => {
                                    Livewire.dispatch('cartUpdated');
                                }, 50);
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: error.message || 'ไม่สามารถเพิ่มสินค้าได้',
                                confirmButtonColor: '#dc2626'
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