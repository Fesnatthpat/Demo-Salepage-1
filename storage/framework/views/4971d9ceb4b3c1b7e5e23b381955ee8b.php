<?php $__env->startSection('title', 'ติดต่อติดใจ'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="container mx-auto px-4 py-12 bg-white min-h-screen">
        <div class="max-w-4xl mx-auto">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-bold text-[#1e3a5f] mb-4"><?php echo e($contact->title ?? 'ติดต่อติดใจ'); ?></h1>
                    <p class="text-gray-500 text-base">
                        เรารูปยินดีให้บริการและพร้อมตอบทุกคำถามของคุณ กรุณาติดต่อเราผ่านช่องทางด้านล่าง
                    </p>
                </div>

                
                <div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] overflow-hidden border border-gray-100">
                    <div class="p-8 md:p-12">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            
                            <div>
                                <h2 class="text-xl font-bold text-[#1e3a5f] mb-6">ข้อมูลการติดต่อ</h2>
                                <div class="space-y-5">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->address): ?>
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-6 mt-1">
                                                <i class="fas fa-map-marker-alt text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600 leading-relaxed">
                                                    <strong>บริษัท ติดใจ จำกัด</strong><br>
                                                    <?php echo nl2br(e($contact->address)); ?>

                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->phone): ?>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-6">
                                                <i class="fas fa-phone-alt text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600">โทรศัพท์: <?php echo e($contact->phone); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->email): ?>
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-6">
                                                <i class="fas fa-envelope text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600">อีเมล: <?php echo e($contact->email); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            
                            <div>
                                <h2 class="text-xl font-bold text-[#1e3a5f] mb-6">เวลาทำการ</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันจันทร์ - วันศุกร์:</span>
                                        <span>9:00 น. - 18:00 น.</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันเสาร์ - วันอาทิตย์:</span>
                                        <span class="text-red-500">ปิดทำการ</span>
                                    </div>
                                    <div class="mt-6">
                                        <p class="text-sm text-gray-400 leading-relaxed italic">
                                            หากคุณมีข้อสงสัยหรือต้องการความช่วยเหลือเร่งด่วน
                                            กรุณาโทรศัพท์หาเราในช่วงเวลาทำการ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-[#1e3a5f] text-center mb-6">แผนที่</h2>
                            <div class="rounded-xl overflow-hidden border border-gray-200 h-[400px] w-full relative">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->map_url): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains($contact->map_url, '<iframe')): ?>
                                        <?php echo str_replace('<iframe', '<iframe class="absolute inset-0 w-full h-full border-0"', $contact->map_url); ?>

                                    <?php else: ?>
                                        <iframe src="<?php echo e($contact->map_url); ?>"
                                            class="absolute inset-0 w-full h-full border-0" allowfullscreen=""
                                            loading="lazy"></iframe>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center h-full bg-gray-50 text-gray-400">
                                        <i class="fas fa-map-marked-alt text-5xl mb-3"></i>
                                        <p>ยังไม่ได้ระบุตำแหน่งบนแผนที่</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-center py-20">
                    <i class="far fa-address-book text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-400">ไม่พบข้อมูลการติดต่อ</p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/contact.blade.php ENDPATH**/ ?>