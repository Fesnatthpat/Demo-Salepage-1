<?php $__env->startSection('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด'); ?>

<?php $__env->startSection('content'); ?>

    
    <style>
        /* --- 1. ปรับแต่งจุด (Pagination) ของ Hero Slider เป็นสีขาว --- */
        .mySwiper .swiper-pagination-bullet {
            background-color: #ffffff !important;
            opacity: 0.5 !important;
            transition: all 0.3s ease;
        }

        .mySwiper .swiper-pagination-bullet-active,
        .mySwiper .swiper-pagination-bullet-active-main {
            background-color: #ffffff !important;
            opacity: 1 !important;
            transform: scale(1.2);
        }

        /* --- 2. ปรับแต่งลูกศร (Navigation) แยกขนาดมือถือและคอมพิวเตอร์ --- */

        /* 📱 1. ขนาดสำหรับมือถือ (Mobile First) */
        .swiper-button-next,
        .swiper-button-prev {
            width: 26px !important;
            height: 26px !important;
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-radius: 50% !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
            margin-top: -13px !important;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 11px !important;
            font-weight: 900 !important;
        }

        .swiper-button-prev {
            left: 8px !important;
        }

        .swiper-button-next {
            right: 8px !important;
        }

        /* 💻 2. ขนาดสำหรับคอมพิวเตอร์และแท็บเล็ต (Tablet/Desktop) */
        @media (min-width: 768px) {

            .swiper-button-next,
            .swiper-button-prev {
                width: 35px !important;
                height: 35px !important;
                margin-top: -17.5px !important;
            }

            .swiper-button-next::after,
            .swiper-button-prev::after {
                font-size: 14px !important;
            }

            .swiper-button-prev {
                left: 16px !important;
            }

            .swiper-button-next {
                right: 16px !important;
            }
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            transform: scale(1.1) !important;
        }
    </style>

    
    <div class="w-full bg-white py-6">
        <div class="container mx-auto px-4">
            <div
                class="relative w-full h-[180px] md:h-[300px] lg:h-[500px] bg-gray-100 group rounded-[5px] overflow-hidden shadow-lg">
                <div class="swiper mySwiper w-full h-full">
                    <div class="swiper-wrapper">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-1.png', 'th-2.png', 'th-3.png', 'th-4.png', 'th-5.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <div class="swiper-slide">
                                <a href="/allproducts" class="block max-w-full h-full">
                                    <img src="<?php echo e(asset('images/' . $img)); ?>" class="w-full h-full object-cover object-center"
                                        alt="Slide"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500?text=Banner';" />
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
    </div>

    
    <div class="w-full bg-red-50">
        <div class="container mx-auto">
            <img src="<?php echo e(asset('images/image_27e610.png')); ?>" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                class="w-full h-auto block shadow-sm hover:shadow-lg transition-shadow duration-300"
                onerror="this.onerror=null;this.style.display='none';" />
        </div>
    </div>

    
    <div class="w-full pb-12 pt-4 bg-gray-100">
        <div class="container mx-auto px-4 mb-10">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <div class="inline-block px-3 py-1 bg-red-100 text-red-600 rounded-lg text-sm font-bold mb-2">
                        Recommended</div>
                    <h2 class="text-3xl md:text-4xl font-black text-gray-800 tracking-tight">เมนูแนะนำ <span
                            class="text-red-600">ต้องลอง!</span></h2>
                </div>
                <a href="/allproducts"
                    class="group flex items-center gap-1 text-red-600 font-bold hover:text-red-700 hidden md:flex transition">
                    ดูทั้งหมด <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($recommendedProducts) && count($recommendedProducts) > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $recommendedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <?php
                            // 1. เช็คว่ามี Options หรือไม่
                            $hasOptions = isset($product->options) && $product->options->count() > 0;

                            // 2. ดึงราคาเริ่มต้นตามเงื่อนไข (ตัวเลือกที่ถูกที่สุด หรือ ราคาหลัก)
                            if ($hasOptions) {
                                $originalPrice = (float) $product->options->min('option_price');
                            } else {
                                $originalPrice = (float) ($product->pd_sp_price ?? 0);
                            }

                            $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                            $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                            $isOnSale = $discountAmount > 0;

                            // จัดการรูปภาพ
                            $displayImage = $product->cover_image_url;
                        ?>
                        <div
                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full rounded-2xl overflow-hidden">
                            <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>"
                                class="block overflow-hidden relative pt-[100%]">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock <= 0): ?>
                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                        <div
                                            class="w-28 h-28 rounded-full bg-black bg-opacity-60 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-md text-center">สินค้าหมด</span>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($product->pd_sp_name); ?>"
                                    class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 <?php echo e($product->pd_sp_stock <= 0 ? 'opacity-50' : ''); ?>"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400?text=No+Image';" />
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                    <div
                                        class="absolute top-3 right-3 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md animate-pulse">
                                        ลด <?php echo e(number_format($discountAmount)); ?>.-</div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </a>
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="mb-2">
                                    <h2 class="text-lg font-bold text-gray-800 leading-tight line-clamp-2 hover:text-red-600 transition cursor-pointer"
                                        onclick="window.location='<?php echo e(route('product.show', $product->pd_sp_id)); ?>'">
                                        <?php echo e($product->pd_sp_name); ?>

                                    </h2>
                                </div>
                                <div class="mt-auto">
                                    <p
                                        class="text-[11px] font-semibold mb-3 <?php echo e($product->pd_sp_stock > 0 ? 'text-green-600' : 'text-red-500'); ?>">
                                        <?php echo e($product->pd_sp_stock > 0 ? '● มีสินค้า' : '● สินค้าหมด'); ?>

                                    </p>

                                    
                                    <div class="flex items-center justify-between w-full mb-4">
                                        <div class="flex items-center gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOptions): ?>
                                                <span class="text-[10px] text-gray-500 mr-0.5">เริ่มต้น</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span
                                                class="text-xl font-black text-red-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                            <span
                                                class="text-md text-gray-400 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    
                                    <button type="button"
                                        <?php if($hasOptions): ?> onclick="window.location='<?php echo e(route('product.show', $product->pd_sp_id)); ?>'"
                                        <?php else: ?>
                                            onclick="addToCartQuick(this, '<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>')" <?php endif; ?>
                                        class="btn w-full rounded-xl border-none font-bold text-white shadow-md transition-transform active:scale-95 <?php echo e($product->pd_sp_stock > 0 ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-gray-300 cursor-not-allowed'); ?>"
                                        <?php echo e($product->pd_sp_stock <= 0 ? 'disabled' : ''); ?>>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock > 0): ?>
                                            <?php echo e($hasOptions ? 'เลือกตัวเลือก' : 'ใส่ตะกร้าเลย'); ?>

                                        <?php else: ?>
                                            สินค้าหมด
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php else: ?>
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                        <p class="text-gray-500 font-medium">ไม่พบสินค้าแนะนำในขณะนี้</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-10 text-center md:hidden">
                <a href="/allproducts"
                    class="btn btn-outline border-red-600 text-red-600 w-full rounded-xl font-bold">ดูสินค้าทั้งหมด</a>
            </div>
        </div>
    </div>

    
    <div class="w-full bg-gray-50/50 pt-8 pb-4">
        <div class="container mx-auto px-4">
            <div class="swiper mySwiper2 w-full max-w-[900px] mx-auto rounded-2xl shadow-md overflow-hidden relative">
                <div class="swiper-wrapper">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['th-a.png', 'th-b.png', 'th-c.png']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                        <div class="swiper-slide"><img src="<?php echo e(asset('images/' . $img)); ?>" class="w-full h-auto block"
                                onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400?text=Banner+2';" />
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>

    
    <div class="bg-white border-b border-gray-100 py-8 relative">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100">
                <?php
                    $serviceBarItems = [
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                            'text' => 'สูตรเด็ดต้นตำรับ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                            'text' => 'ส่งไว ทันใจ',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>',
                            'text' => 'ชำระเงินปลอดภัย',
                        ],
                        [
                            'icon' =>
                                '<svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>',
                            'text' => 'ทำด้วยใจทุกขั้นตอน',
                        ],
                    ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $serviceBarItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div class="flex flex-col items-center gap-3 group cursor-default">
                        <div class="p-3 bg-red-50 rounded-full group-hover:bg-red-100 transition duration-300">
                            <?php echo $item['icon']; ?></div>
                        <span
                            class="text-base font-bold text-gray-700 group-hover:text-red-600 transition"><?php echo e($item['text']); ?></span>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="w-full py-16 bg-red-700">
        <div class="container mx-auto px-4 relative z-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-center text-white mb-12 drop-shadow-md">6
                เหตุผลทำไมต้องเลือกเรา</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4 md:px-10 lg:px-20">
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">เรารู้จริง</h3>
                    <p class="text-white/90 text-sm leading-relaxed">เรารู้ว่าคุณต้องการอะไร กังวลสิ่งไหน
                        เราจึงตั้งใจส่งมอบสิ่งที่ดีที่สุดให้กับคุณและคนที่คุณรัก</p>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">พิถีพิถัน</h3>
                    <p class="text-white/90 text-sm leading-relaxed">เราใส่ใจทุกรายละเอียดอย่างแท้จริง
                        ตั้งแต่การคัดเลือกวัตถุดิบคุณภาพสูง ผ่านกระบวนการผลิตที่มีมาตรฐานระดับสากล</p>
                </div>
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">ทุกเพศ ทุกวัย ทุกสไตล์</h3>
                    <p class="text-white/90 text-sm leading-relaxed">อร่อยแบบไม่จำกัด
                        ด้วยผลิตภัณฑ์ที่มีหลากหลายชนิดหลายสูตร เพื่อตอบสนองต่อความต้องการที่หลากหลาย</p>
                </div>
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
                speed: 800,
                effect: 'slide',
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
            var swiper2 = new Swiper(".mySwiper2", {
                slidesPerView: 1,
                spaceBetween: 10,
                loop: true,
                speed: 800,
                autoHeight: true,
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false
                },
                pagination: {
                    el: ".swiper-pagination",
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
                            timer: 1500
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
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Connection failed',
                    confirmButtonColor: '#DC2626'
                }))
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