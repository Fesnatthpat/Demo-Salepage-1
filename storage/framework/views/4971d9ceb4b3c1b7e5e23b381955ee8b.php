<?php $__env->startSection('title', 'ติดต่อเรา'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mx-auto px-4 py-16 bg-gray-50 min-h-screen">
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">ติดต่อเรา</h1>
        <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full mb-6"></div>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            ยินดีให้คำปรึกษาและบริการ หากมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อเราได้ตามรายละเอียดด้านล่าง
        </p>
    </div>

    <div class="max-w-6xl mx-auto space-y-12">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col lg:flex-row transition-transform duration-300 hover:-translate-y-1 hover:shadow-2xl">
                
                <div class="p-8 lg:p-12 lg:w-1/2 flex flex-col justify-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4"><?php echo e($contact->title); ?></h2>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->content): ?>
                        <p class="text-gray-600 mb-8 leading-relaxed"><?php echo nl2br(e($contact->content)); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="space-y-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->address): ?>
                            <div class="flex items-start group">
                                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-2xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                    <i class="fas fa-map-marker-alt text-xl"></i>
                                </div>
                                <div class="ml-5 mt-1">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">ที่อยู่</h3>
                                    <span class="text-gray-600 leading-relaxed"><?php echo nl2br(e($contact->address)); ?></span>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->phone): ?>
                            <div class="flex items-start group">
                                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-2xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                    <i class="fas fa-phone-alt text-xl"></i>
                                </div>
                                <div class="ml-5 mt-1">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">เบอร์โทรศัพท์</h3>
                                    <a href="tel:<?php echo e(preg_replace('/[^0-9+]/', '', $contact->phone)); ?>" class="text-gray-600 hover:text-green-600 transition-colors block"><?php echo e($contact->phone); ?></a>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->email): ?>
                            <div class="flex items-start group">
                                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-2xl bg-red-50 text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                    <i class="fas fa-envelope text-xl"></i>
                                </div>
                                <div class="ml-5 mt-1">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">อีเมล</h3>
                                    <a href="mailto:<?php echo e($contact->email); ?>" class="text-gray-600 hover:text-red-600 transition-colors block"><?php echo e($contact->email); ?></a>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="lg:w-1/2 min-h-[350px] lg:min-h-full bg-gray-100 relative flex items-center justify-center overflow-hidden [&>iframe]:absolute [&>iframe]:inset-0 [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($contact->map_url): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_contains($contact->map_url, '<iframe')): ?>
                            <?php echo $contact->map_url; ?>

                        <?php else: ?>
                            <iframe src="<?php echo e($contact->map_url); ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center text-gray-400 z-10">
                            <i class="fas fa-map-marked-alt text-6xl mb-4 opacity-50"></i>
                            <p class="font-medium">ไม่ได้ระบุพิกัดแผนที่</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="flex flex-col items-center justify-center py-24 bg-white rounded-3xl shadow-sm border border-dashed border-gray-300">
                <i class="far fa-address-book text-6xl text-gray-300 mb-4"></i>
                <p class="text-xl text-gray-500 font-medium">ไม่มีข้อมูลการติดต่อในขณะนี้</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/contact.blade.php ENDPATH**/ ?>