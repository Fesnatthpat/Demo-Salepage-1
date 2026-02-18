<?php $__env->startSection('title', 'เกี่ยวกับเรา | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bg-gray-50 min-h-screen overflow-x-hidden font-sans pb-20">

        
        <div class="relative bg-gradient-to-br from-red-700 via-red-600 to-red-500 text-white pt-20 pb-32 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                <div
                    class="absolute -top-20 -left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse">
                </div>
            </div>
            <div class="container mx-auto px-4 relative z-10 text-center" data-aos="zoom-in" data-aos-duration="1000">
                <div class="mb-8 flex justify-center">
                    <div
                        class="p-6 bg-white/10 backdrop-blur-md rounded-full shadow-2xl ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500">
                        <img src="<?php echo e(isset($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : asset('images/logo/logo1.png')); ?>"
                            alt="Logo" class="h-24 w-auto drop-shadow-lg object-contain"
                            onerror="this.src='https://via.placeholder.com/150x150?text=LOGO';" />
                    </div>
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-wide text-shadow-lg">
                    <?php echo e($settings['about_title'] ?? 'เกี่ยวกับติดใจ'); ?>

                </h1>
                <p class="text-red-100 text-lg md:text-xl font-light max-w-2xl mx-auto">
                    <?php echo e($settings['about_subtitle'] ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ'); ?>

                </p>
            </div>
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg class="relative block w-full h-16 md:h-24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path
                        d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"
                        class="fill-gray-50"></path>
                </svg>
            </div>
        </div>

        
        <div class="container mx-auto max-w-5xl px-4 -mt-20 relative z-20">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $fav): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 relative overflow-hidden" data-aos="fade-up">

                    <?php
                        $hasMultipleImages = isset($fav->images) && $fav->images->count() > 1;
                        $hasSingleImage = (isset($fav->images) && $fav->images->count() == 1) || $fav->image_path;
                        $hasAnyImage = $hasMultipleImages || $hasSingleImage;
                    ?>

                    <div class="flex flex-col md:flex-row gap-12 <?php echo e(!$hasAnyImage ? 'justify-center' : ''); ?>">

                        
                        <div
                            class="w-full <?php echo e($hasAnyImage ? 'md:w-1/2' : 'md:w-3/4'); ?> <?php echo e($index % 2 == 0 && $hasAnyImage ? 'order-2 md:order-1' : 'order-2 md:order-2'); ?>">
                            <div class="flex items-start gap-4 mb-6">
                                <span class="w-1.5 h-10 bg-red-600 rounded-full mt-1 flex-shrink-0"></span>
                                <h2 class="text-3xl font-bold text-gray-800 leading-tight"><?php echo e($fav->title); ?></h2>
                            </div>
                            <div class="prose prose-lg text-gray-600 leading-loose font-light">
                                <?php echo $fav->content; ?>

                            </div>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAnyImage): ?>
                            <div
                                class="w-full md:w-1/2 <?php echo e($index % 2 == 0 ? 'order-1 md:order-2' : 'order-1 md:order-1'); ?> relative">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMultipleImages): ?>
                                    
                                    <div class="relative group h-full min-h-[300px]  rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-gray-100"
                                        id="carousel-<?php echo e($fav->id); ?>">

                                        
                                        <div class="carousel-track flex transition-transform duration-500 ease-in-out h-full w-full"
                                            data-index="0">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fav->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="min-w-full h-full relative cursor-pointer"
                                                    onclick="openLightbox('<?php echo e(asset('storage/' . $img->image_path)); ?>')">
                                                    <img src="<?php echo e(asset('storage/' . $img->image_path)); ?>"
                                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
                                                        alt="<?php echo e($fav->title); ?>">

                                                    
                                                    <div
                                                        class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                                        <i
                                                            class="fas fa-search-plus text-white text-3xl drop-shadow-md"></i>
                                                    </div>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>

                                        
                                        <button onclick="moveCarousel('<?php echo e($fav->id); ?>', -1)"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                                            <i class="fas fa-chevron-left text-lg"></i>
                                        </button>
                                        <button onclick="moveCarousel('<?php echo e($fav->id); ?>', 1)"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                                            <i class="fas fa-chevron-right text-lg"></i>
                                        </button>

                                        
                                        <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-10">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fav->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="carousel-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300 <?php echo e($key == 0 ? 'bg-white w-4' : ''); ?>"
                                                    data-target="<?php echo e($fav->id); ?>" data-slide="<?php echo e($key); ?>">
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <?php
                                        $singleImgPath =
                                            isset($fav->images) && $fav->images->count() > 0
                                                ? $fav->images->first()->image_path
                                                : $fav->image_path;
                                    ?>
                                    <div class="relative group-img h-full min-h-[300px] cursor-pointer"
                                        onclick="openLightbox('<?php echo e(asset('storage/' . $singleImgPath)); ?>')">
                                        <div
                                            class="absolute inset-0 bg-red-600 rounded-2xl transform rotate-3 transition-transform duration-300 opacity-10 group-hover:rotate-6">
                                        </div>
                                        <img src="<?php echo e(asset('storage/' . $singleImgPath)); ?>" alt="<?php echo e($fav->title); ?>"
                                            class="relative rounded-2xl shadow-lg w-full h-full object-cover border-4 border-white transform transition-transform duration-300 group-hover:-translate-y-2">

                                        
                                        <div
                                            class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                            <div class="bg-black/40 p-3 rounded-full backdrop-blur-sm">
                                                <i class="fas fa-search-plus text-white text-xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-20 bg-white rounded-3xl shadow-lg border-dashed border-2 border-gray-300 mb-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">เนื้อหากำลังปรับปรุง</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>

    
    <div id="lightboxModal"
        class="fixed inset-0 z-[999] bg-black/90 hidden items-center justify-center opacity-0 transition-opacity duration-300">
        
        <button onclick="closeLightbox()"
            class="absolute top-4 right-4 text-white hover:text-red-500 transition-colors z-50 p-4">
            <i class="fas fa-times text-4xl shadow-lg"></i>
        </button>

        
        <div class="relative w-full h-full flex items-center justify-center p-4 md:p-10" onclick="closeLightbox()">
            <img id="lightboxImage" src="" alt="Zoom"
                class="max-w-full max-h-full object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300"
                onclick="event.stopPropagation()"> 
        </div>
    </div>

    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: true
            });
        });

        // --- CAROUSEL LOGIC ---
        function moveCarousel(id, direction) {
            const container = document.getElementById(`carousel-${id}`);
            const track = container.querySelector('.carousel-track');
            const dots = container.querySelectorAll('.carousel-dot');
            const items = track.children;
            const totalItems = items.length;

            // อ่านค่า index ปัจจุบัน
            let currentIndex = parseInt(track.dataset.index || 0);

            // คำนวณ index ใหม่
            let newIndex = currentIndex + direction;

            // Loop logic (ถ้าเกินให้วนกลับ)
            if (newIndex < 0) newIndex = totalItems - 1;
            if (newIndex >= totalItems) newIndex = 0;

            // บันทึกค่าและเลื่อน
            track.dataset.index = newIndex;
            track.style.transform = `translateX(-${newIndex * 100}%)`;

            // Update Dots
            dots.forEach((dot, idx) => {
                if (idx === newIndex) {
                    dot.classList.add('bg-white', 'w-4');
                    dot.classList.remove('bg-white/50');
                } else {
                    dot.classList.remove('bg-white', 'w-4');
                    dot.classList.add('bg-white/50');
                }
            });
        }

        // --- LIGHTBOX LOGIC ---
        const lightbox = document.getElementById('lightboxModal');
        const lightboxImg = document.getElementById('lightboxImage');

        function openLightbox(src) {
            lightboxImg.src = src;
            lightbox.classList.remove('hidden');
            // Delay เล็กน้อยเพื่อให้ CSS Transition ทำงาน
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightboxImg.classList.remove('scale-95');
                lightboxImg.classList.add('scale-100');
            }, 10);
            document.body.style.overflow = 'hidden'; // ป้องกัน Scroll พื้นหลัง
        }

        function closeLightbox() {
            lightbox.classList.add('opacity-0');
            lightboxImg.classList.remove('scale-100');
            lightboxImg.classList.add('scale-95');

            setTimeout(() => {
                lightbox.classList.add('hidden');
                lightboxImg.src = '';
            }, 300); // รอให้ Transition จบก่อนซ่อน
            document.body.style.overflow = '';
        }

        // ปิดด้วยปุ่ม ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeLightbox();
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/about.blade.php ENDPATH**/ ?>