<?php $__env->startSection('title', 'หน้าหลัก | ติดใจ - ของกินเล่นสูตรเด็ด'); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="relative w-full h-[200px] md:h-[350px] lg:h-[700px] bg-gray-100 group">
        <div class="swiper mySwiper w-full h-full">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <a href="/allproducts" class="block max-w-full">
                        <img src="<?php echo e(asset('images/th-1.png')); ?>" class="w-full h-full object-cover object-center"
                            alt="Slide 1"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/783630/ffffff?text=Image+1';" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="/allproducts" class="block max-w-full">
                        <img src="<?php echo e(asset('images/th-2.png')); ?>" class="w-full h-full object-cover object-center"
                            alt="Slide 2"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+2';" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="/allproducts" class="block max-w-full">
                        <img src="<?php echo e(asset('images/th-3.png')); ?>" class="w-full h-full object-cover object-center"
                            alt="Slide 3"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+3';" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="/allproducts" class="block max-w-full">
                        <img src="<?php echo e(asset('images/th-4.png')); ?>" class="w-full h-full object-cover object-center"
                            alt="Slide 4"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+4';" />
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="/allproducts" class="block max-w-full">
                        <img src="<?php echo e(asset('images/th-5.png')); ?>" class="w-full h-full object-cover object-center"
                            alt="Slide 5"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x500/ef4444/ffffff?text=Image+5';" />
                    </a>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    
    <div class="w-full bg-red-50">
        <div class="container mx-auto">
            <img src="<?php echo e(asset('images/image_27e610.png')); ?>" alt="ข้อมูลสำหรับผู้แพ้อาหาร"
                class="w-full h-auto block shadow-sm hover:shadow-lg transition-shadow duration-300"
                onerror="this.onerror=null;this.style.display='none';" />
        </div>
    </div>


    
    
    <div class="w-full pb-12 pt-4 bg-cover bg-center bg-no-repeat"
        style="background-image: url('<?php echo e(asset('images/f1.png')); ?>');">

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
                            $originalPrice = (float) ($product->pd_sp_price ?? 0);
                            $discountAmount = (float) ($product->pd_sp_discount ?? 0);
                            $finalSellingPrice = max(0, $originalPrice - $discountAmount);
                            $isOnSale = $discountAmount > 0;
                            $displayImage = 'https://via.placeholder.com/400x400.png?text=Snack+Image';
                            if ($product->images && $product->images->isNotEmpty()) {
                                $primaryImage =
                                    $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                $rawPath = $primaryImage->image_path ?? $primaryImage->img_path;
                                if ($rawPath) {
                                    $displayImage = filter_var($rawPath, FILTER_VALIDATE_URL)
                                        ? $rawPath
                                        : asset('storage/' . ltrim(str_replace('storage/', '', $rawPath), '/'));
                                }
                            }
                        ?>
                        <div
                            class="card bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group flex flex-col h-full rounded-2xl overflow-hidden">
                            <a href="<?php echo e(route('product.show', $product->pd_sp_id)); ?>"
                                class="block overflow-hidden relative pt-[100%]">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock <= 0): ?>
                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                        <div class="w-28 h-28 rounded-full bg-black bg-opacity-60 flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-md text-center">สินค้าหมด</span>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($product->pd_sp_name); ?>"
                                    class="absolute top-0 left-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 <?php echo e($product->pd_sp_stock <= 0 ? 'opacity-50' : ''); ?>"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/400x400.png?text=No+Image';" />
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
                                        <?php echo e($product->pd_sp_name); ?></h2>
                                    
                                </div>
                                <div class="mt-auto">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex flex-col">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOnSale): ?>
                                                <span
                                                    class="text-xs text-gray-400 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                                <span
                                                    class="text-xl font-black text-red-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                            <?php else: ?>
                                                <span
                                                    class="text-xl font-black text-red-600">฿<?php echo e(number_format($finalSellingPrice)); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div
                                            class="text-xs font-semibold <?php echo e($product->pd_sp_stock > 0 ? 'text-green-600 bg-green-50' : 'text-red-500 bg-red-50'); ?> px-2 py-1 rounded-md">
                                            <?php echo e($product->pd_sp_stock > 0 ? 'มีสินค้า' : 'สินค้าหมด'); ?></div>
                                    </div>
                                    <button type="button"
                                        onclick="addToCartQuick(this, '<?php echo e(route('cart.add', ['id' => $product->pd_sp_id])); ?>')"
                                        class="btn w-full rounded-xl border-none font-bold text-white shadow-md transition-transform active:scale-95 <?php echo e($product->pd_sp_stock > 0 ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-gray-300 cursor-not-allowed'); ?>"
                                        <?php echo e($product->pd_sp_stock <= 0 ? 'disabled' : ''); ?>>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_sp_stock > 0): ?>
                                            ใส่ตะกร้าเลย
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
            <div class="swiper mySwiper2 w-full rounded-2xl shadow-md overflow-hidden">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="<?php echo e(asset('images/th-a.png')); ?>" class="w-full h-auto block"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+A';" />
                    </div>
                    <div class="swiper-slide"><img src="<?php echo e(asset('images/th-b.png')); ?>" class="w-full h-auto block"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+B';" />
                    </div>
                    <div class="swiper-slide"><img src="<?php echo e(asset('images/th-c.png')); ?>" class="w-full h-auto block"
                            onerror="this.onerror=null;this.src='https://via.placeholder.com/1200x400/ef4444/ffffff?text=Image+C';" />
                    </div>
                </div>
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


    
    
    <div class="w-full py-16 bg-cover bg-center bg-no-repeat"
        style="background-image: url('<?php echo e(asset('images/NATTHAPAT (1).png')); ?>');">

        
        

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

                
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">คุณค่าที่มากกว่า</h3>
                    <p class="text-white/90 text-sm leading-relaxed">มากกว่าเครื่องปรุงเกาหลีคือสุขภาพ
                        และอารมณ์ที่ดีของคุณและคนที่คุณรัก</p>
                </div>

                
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">ช่วงเวลาแห่งความสุขร่วมกัน</h3>
                    <p class="text-white/90 text-sm leading-relaxed">ให้สินค้าของเราเป็นสื่อกลาง
                        สานสัมพันธ์ระหว่างคุณและคนที่คุณรัก เพื่อสร้างเวลาแห่งความสุขร่วมกัน</p>
                </div>

                
                <div class="flex flex-col items-center text-center group">
                    <div class="mb-4 text-white transition-transform duration-300 group-hover:scale-110 drop-shadow-sm">
                        <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 drop-shadow-md">THE TRUE HAPPINESS</h3>
                    <p class="text-white/90 text-sm leading-relaxed">ที่สุด คือ ความสุขของคุณที่คุณรัก
                        เพราะเรารู้ว่านั่นคือ ของสุขของคุณเช่นกัน</p>
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
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
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
                            position: 'top-end',
                            toast: true,
                            background: '#FEF2F2',
                            iconColor: '#DC2626'
                        });
                        setTimeout(() => { // Add setTimeout
                            Livewire.dispatch('cartUpdated');
                            console.log('Dispatched cartUpdated from index.blade.php'); // Add log
                        }, 50); // Small delay
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