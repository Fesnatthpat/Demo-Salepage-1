<?php $__env->startSection('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด'); ?>

<?php $__env->startSection('content'); ?>

    <style>
        /* --- Swiper Styles --- */
        .mySwiper .swiper-pagination-bullet,
        .productSwiper .swiper-pagination-bullet {
            background-color: #dc2626 !important;
            opacity: 0.3 !important;
            transition: all 0.3s ease;
        }

        .mySwiper .swiper-pagination-bullet-active,
        .productSwiper .swiper-pagination-bullet-active {
            background-color: #dc2626 !important;
            opacity: 1 !important;
            transform: scale(1.2);
        }

        .swiper-button-next,
        .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(2px);
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
            margin-top: -16px !important;
            z-index: 50;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 14px !important;
            font-weight: 900 !important;
        }

        .swiper-button-prev {
            left: 0px !important;
        }

        .swiper-button-next {
            right: 0px !important;
        }

        .mySwiper .swiper-button-prev {
            left: 12px !important;
        }

        .mySwiper .swiper-button-next {
            right: 12px !important;
        }

        @media (min-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 45px !important;
                height: 45px !important;
                margin-top: -22.5px !important;
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 18px !important;
            }

            .mySwiper .swiper-button-prev {
                left: 24px !important;
            }

            .mySwiper .swiper-button-next {
                right: 24px !important;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }

        .product-title-fixed {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5rem;
            line-height: 1.25;
        }

        @media (min-width: 768px) {
            .product-title-fixed {
                min-height: 3.5rem;
            }
        }
    </style>

    
    
    <div class="w-full pb-6 pt-2 md:pt-4">
        <div class="container mx-auto px-4">
            <div
                class="relative w-full aspect-[16/10] md:aspect-[2.5/1] lg:aspect-[3/1] bg-gray-100 group rounded-2xl overflow-hidden shadow-xl">
                <div class="swiper mySwiper w-full h-full absolute inset-0">
                    <div class="swiper-wrapper">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($heroSlides) && $heroSlides->count() > 0): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $heroSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="swiper-slide">
                                    <a href="<?php echo e($slide->link_url ?? '/allproducts'); ?>" class="block w-full h-full">
                                        <img src="<?php echo e(Storage::url($slide->image_path)); ?>"
                                            class="w-full h-full object-center object-center"
                                            alt="<?php echo e($slide->title ?? 'Slide'); ?>"
                                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1600x600?text=Banner+Image';" />
                                    </a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php else: ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="swiper-slide">
                                    <a href="/allproducts" class="block w-full h-full">
                                        <img src="<?php echo e(asset('images/' . $img)); ?>"
                                            class="w-full h-full object-cover object-center" alt="Slide"
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

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($infoBanner) && $infoBanner->is_active): ?>
        <div class="w-full py-6">
            <div class="container mx-auto px-4">
                <img src="<?php echo e(Storage::url($infoBanner->image_path)); ?>"
                    alt="<?php echo e($infoBanner->title ?? 'ข้อมูลสำหรับผู้แพ้อาหาร'); ?>"
                    class="w-full md:max-w-3xl mx-auto h-auto block rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-4 border-white"
                    onerror="this.onerror=null;this.style.display='none';" />
            </div>
        </div>
    <?php elseif(!isset($infoBanner)): ?>
        <div class="w-full  ">
            <div class="container mx-auto px-4">
                <img src="<?php echo e(asset('images/image_27e610.png')); ?>" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                    class="w-full md:max-w-3xl mx-auto h-auto block rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border-4 border-white"
                    onerror="this.onerror=null;this.style.display='none';" />
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="w-full pb-16 pt-10">
        <div class="container mx-auto px-4 mb-10">

            
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-6 md:p-8 shadow-xl">

                
                <div class="flex justify-between items-end mb-8 md:mb-10">
                    <div>
                        <div
                            class="inline-block px-3 py-1 bg-red-100 text-red-600 rounded-lg text-xs md:text-sm font-bold mb-2 shadow-sm">
                            Recommended
                        </div>
                        <h2 class="text-3xl md:text-4xl font-black text-gray-800 tracking-tight">
                            เมนูแนะนำ <span
                                class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-500">ต้องลอง!</span>
                        </h2>
                    </div>
                    <a href="/allproducts"
                        class="group flex items-center gap-2 text-red-600 font-bold hover:text-red-700 hidden md:flex transition-all hover:bg-red-50 px-4 py-2 rounded-full">
                        ดูทั้งหมด
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($recommendedProducts) && count($recommendedProducts) > 0): ?>
                    <div class="relative group">
                        <div class="swiper productSwiper !pb-12 !px-2">
                            <div class="swiper-wrapper">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recommendedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <?php
                                        $hasOptions = isset($product->options) && $product->options->count() > 0;
                                        $originalPrice = $hasOptions
                                            ? (float) $product->options->min('option_price')
                                            : (float) ($product->pd_sp_price ?? 0);
                                        $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                                        $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                                        $isOnSale = $discountAmount > 0;
                                        $displayImage = $product->cover_image_url;
                                        $productPromo = isset($promotions)
                                            ? $promotions->first(function ($promo) use ($product) {
                                                return $promo->rules->contains(function ($rule) use ($product) {
                                                    $pids = (array) ($rule->rules['product_id'] ?? []);
                                                    return in_array($product->pd_sp_id, array_map('intval', $pids));
                                                });
                                            })
                                            : null;
                                    ?>

                                    <div class="swiper-slide h-auto">
                                        <div
                                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full rounded-2xl overflow-hidden relative">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($productPromo): ?>
                                                <div class="absolute top-0 right-0 z-20">
                                                    <div
                                                        class="bg-gradient-to-l from-pink-600 to-red-500 text-white px-3 py-1 rounded-bl-xl shadow-lg flex flex-col items-end">
                                                        <span class="text-[10px] font-black uppercase tracking-tighter">Free
                                                            Gift</span>
                                                        <span
                                                            class="text-[9px] font-bold opacity-90 leading-none"><?php echo e($productPromo->name); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>"
                                                class="relative aspect-square overflow-hidden bg-gray-100 block">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock <= 0): ?>
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center z-10 bg-black/60 backdrop-blur-[2px]">
                                                        <span
                                                            class="bg-white/10 text-white border border-white/50 text-sm px-4 py-1.5 rounded-full font-bold">สินค้าหมด</span>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($product->pd_sp_name); ?>"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-700 <?php echo e($product->pd_sp_stock <= 0 ? 'opacity-50 grayscale' : ''); ?>"
                                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400?text=No+Image';" />
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                    <div
                                                        class="absolute top-3 left-3 bg-red-600 text-white px-2.5 py-1 rounded-lg text-[10px] md:text-xs font-bold shadow-lg shadow-red-600/30">
                                                        ลด <?php echo e(number_format($discountAmount)); ?>.-</div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </a>
                                            <div class="p-4 flex-1 flex flex-col">
                                                <h2 class="text-sm md:text-base font-bold text-gray-800 leading-tight product-title-fixed hover:text-red-600 transition cursor-pointer mb-2"
                                                    onclick="window.location='<?php echo e(route('product.show', $product->pd_sp_id)); ?>'">
                                                    <?php echo e($product->pd_sp_name); ?></h2>
                                                <div class="flex items-center justify-between mb-3">
                                                    <p
                                                        class="text-[10px] md:text-xs font-semibold <?php echo e($product->pd_sp_stock > 0 ? 'text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full' : 'text-red-500 bg-red-50 px-2 py-0.5 rounded-full'); ?>">
                                                        <?php echo e($product->pd_sp_stock > 0 ? '● มีสินค้า' : '● หมด'); ?></p>
                                                    <p class="text-[10px] text-gray-400">ขายแล้ว
                                                        <?php echo e(number_format($product->pd_sp_sold ?? 0)); ?></p>
                                                </div>
                                                <div class="mt-auto pt-3 border-t border-gray-50">
                                                    <div class="flex justify-between items-end w-full mb-3">
                                                        <div class="flex flex-col">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                                <span
                                                                    class="text-xs text-gray-400 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <div class="flex items-center gap-1">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOptions): ?>
                                                                    <span
                                                                        class="text-[10px] text-gray-400 mb-0.5">เริ่ม</span>
                                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                <span
                                                                    class="text-lg md:text-xl font-black text-gray-800">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        <?php if($hasOptions): ?> onclick="window.location='<?php echo e(route('product.show', $product->pd_sp_id)); ?>'" <?php else: ?> onclick="addToCartQuick(this, '<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>')" <?php endif; ?>
                                                        class="btn btn-sm w-full rounded-xl border-none font-bold text-white shadow-md shadow-red-200 transition-all hover:shadow-lg hover:shadow-red-300 active:scale-95 <?php echo e($product->pd_sp_stock > 0 ? 'bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600' : 'bg-gray-300 cursor-not-allowed'); ?>"
                                                        <?php echo e($product->pd_sp_stock <= 0 ? 'disabled' : ''); ?>>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock > 0): ?>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            <?php echo e($hasOptions ? 'เลือกตัวเลือก' : 'ใส่ตะกร้า'); ?>

                                                        <?php else: ?>
                                                            สินค้าหมด
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                        <div class="swiper-button-next !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm"></div>
                        <div class="swiper-button-prev !w-8 !h-8 md:!w-10 md:!h-10 after:!text-xs md:after:!text-sm"></div>
                    </div>
                <?php else: ?>
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="bg-gray-50 p-4 rounded-full mb-3"><svg xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg></div>
                        <p class="text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-5 text-center md:hidden">
                    <a href="/allproducts"
                        class="btn btn-outline border-red-600 text-red-600 w-full rounded-xl font-bold hover:bg-red-50">ดูสินค้าทั้งหมด</a>
                </div>
            </div>
        </div>
    </div>

    
    <div class="w-full pt-10 pb-12">
        <div class="container mx-auto px-4">
            <div
                class="swiper mySwiper2 w-full max-w-[1000px] mx-auto rounded-2xl shadow-lg overflow-hidden relative border border-gray-100">
                <div class="swiper-wrapper">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($secSlides) && $secSlides->count() > 0): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $secSlides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="swiper-slide">
                                <a href="<?php echo e($slide->link_url ?? '#'); ?>" class="block w-full h-full">
                                    <img src="<?php echo e(Storage::url($slide->image_path)); ?>" class="w-full h-auto block"
                                        alt="<?php echo e($slide->title ?? 'Banner'); ?>"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Promotion';" />
                                </a>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-a.png', 'th-b.png', 'th-c.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="swiper-slide"><img src="<?php echo e(asset('images/' . $img)); ?>"
                                    class="w-full h-auto block"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Promotion+Banner';" />
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

    
    
    <div class="bg-white/95 backdrop-blur-sm border-t border-gray-100 py-12 relative">
        <div class="container mx-auto px-4">
            <div
                class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center divide-x-0 md:divide-x divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($services) && $services->count() > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex flex-col items-center gap-4 group cursor-default p-2">
                            <div
                                class="p-4 bg-white shadow-md rounded-2xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300 transform group-hover:-translate-y-2 ring-1 ring-gray-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains($service->icon, '<svg')): ?>
                                    <div class="w-8 h-8 group-hover:text-white text-red-500 transition-colors">
                                        <?php echo $service->icon; ?></div>
                                <?php else: ?>
                                    <i
                                        class="<?php echo e($service->icon); ?> text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <span
                                class="text-sm md:text-lg font-bold text-gray-700 group-hover:text-red-600 transition"><?php echo e($service->title); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <?php $serviceBarItems = [['icon' => 'fas fa-utensils', 'text' => 'สูตรเด็ดต้นตำรับ'], ['icon' => 'fas fa-shipping-fast', 'text' => 'ส่งไว ทันใจ'], ['icon' => 'fas fa-shield-alt', 'text' => 'ชำระเงินปลอดภัย'], ['icon' => 'fas fa-heart', 'text' => 'ทำด้วยใจทุกขั้นตอน']]; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $serviceBarItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex flex-col items-center gap-4 group cursor-default p-2">
                            <div
                                class="p-4 bg-white shadow-md rounded-2xl group-hover:bg-red-500 transition-all duration-300 transform group-hover:-translate-y-2">
                                <i
                                    class="<?php echo e($item['icon']); ?> text-3xl text-red-500 group-hover:text-white transition-colors"></i>
                            </div>
                            <span
                                class="text-sm md:text-lg font-bold text-gray-700 group-hover:text-red-600 transition"><?php echo e($item['text']); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="w-full py-20 bg-gradient-to-br from-red-800 to-red-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="container mx-auto px-4 relative z-10">
            <h2 class="text-3xl md:text-5xl font-extrabold text-center text-white mb-16 drop-shadow-lg">6
                เหตุผลทำไมต้องเลือกเรา</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12 px-4 md:px-10 lg:px-20">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($reasons) && $reasons->count() > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="flex flex-col items-center text-center group">
                            <div
                                class="mb-6 text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-white/20 bg-white/10 p-5 rounded-2xl backdrop-blur-sm shadow-inner">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains($reason->icon, '<svg')): ?>
                                    <div class="w-10 h-10"><?php echo $reason->icon; ?></div>
                                <?php else: ?>
                                    <i class="<?php echo e($reason->icon); ?> text-4xl"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 drop-shadow-md"><?php echo e($reason->title); ?></h3>
                            <p class="text-white/80 text-sm md:text-base leading-relaxed max-w-xs">
                                <?php echo e($reason->description); ?></p>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <div class="flex flex-col items-center text-center group">
                        <div class="mb-6 bg-white/10 p-5 rounded-2xl text-white"><i
                                class="fas fa-check-circle text-4xl"></i></div>
                        <h3 class="text-xl font-bold text-white mb-3">เรารู้จริง</h3>
                        <p class="text-white/80 text-sm leading-relaxed max-w-xs">คัดสรรแต่วัตถุดิบคุณภาพดีที่สุดเพื่อคุณ
                        </p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var swiper1 = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 0,
                loop: true,
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    dynamicBullets: true
                },
                navigation: {
                    nextEl: ".mySwiper .swiper-button-next",
                    prevEl: ".mySwiper .swiper-button-prev"
                },
            });

            var swiperProducts = new Swiper(".productSwiper", {
                slidesPerView: 2,
                spaceBetween: 15,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: ".productSwiper .swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".group .swiper-button-next",
                    prevEl: ".group .swiper-button-prev",
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 25,
                    },
                },
            });

            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                speed: 800,
                autoHeight: true,
                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".mySwiper2 .swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".mySwiper2 .swiper-button-next",
                    prevEl: ".mySwiper2 .swiper-button-prev"
                },
            });
        });

        function addToCartQuick(btnElement, url) {
            if (btnElement.disabled) return;
            const originalHTML = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<span class="loading loading-spinner loading-xs"></span> กำลังปรุง...';
            const formData = new FormData();
            formData.append('quantity', 1);
            fetch(url, {
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
                        if (typeof window.flyToCart === 'function') window.flyToCart(btnElement);
                        Swal.fire({
                            icon: 'success',
                            title: 'เพิ่มเรียบร้อย!',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            position: 'top-end'
                        });
                        setTimeout(() => {
                            Livewire.dispatch('cartUpdated');
                        }, 50);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'แจ้งเตือน',
                            text: data.message || 'เพิ่มสินค้าไม่ได้',
                            confirmButtonColor: '#DC2626'
                        });
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    setTimeout(() => {
                        btnElement.disabled = false;
                        btnElement.innerHTML = originalHTML;
                    }, 500);
                });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/index.blade.php ENDPATH**/ ?>