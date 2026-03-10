

<?php $__env->startSection('title', 'คำถามที่พบบ่อย'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="min-h-screen bg-cover bg-center bg-fixed py-16 px-4 sm:px-6 lg:px-8 font-sans antialiased relative overflow-hidden"
        style="background-image: url('<?php echo e(asset('')); ?>');">

        
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-red-600/10 to-transparent -z-10"></div>
        <div
            class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -z-10">
        </div>
        <div
            class="absolute top-[20%] left-[-10%] w-72 h-72 bg-red-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -z-10">
        </div>

        
        <div
            class="max-w-4xl mx-auto relative z-10 bg-white rounded-3xl shadow-xl p-6 md:p-10 border border-white/50">

            
            <div class="text-center mb-12">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 text-red-600 text-sm font-semibold mb-6 shadow-sm border border-red-100/50 backdrop-blur-sm transition-transform hover:scale-105">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd"></path>
                    </svg>
                    ศูนย์ช่วยเหลือ
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 mb-4 tracking-tight leading-tight">
                    คุณมีคำถาม <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-400">เรามีคำตอบ</span>
                </h1>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    รวมคำถามที่พบบ่อยเกี่ยวกับการใช้งาน การสั่งซื้อ และการชำระเงิน
                </p>
            </div>

            
            <div class="flex flex-wrap justify-center gap-3 mb-10">
                <button
                    class="px-6 py-2 rounded-full bg-red-600 text-white font-medium shadow-md shadow-red-600/20 hover:bg-red-700 transition-all">ทั้งหมด</button>
                <button
                    class="px-6 py-2 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-50 border border-slate-200 transition-all">การสั่งซื้อ</button>
                <button
                    class="px-6 py-2 rounded-full bg-white text-slate-600 font-medium hover:bg-slate-50 border border-slate-200 transition-all">การชำระเงิน</button>
            </div>

            
            <div class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <div
                        class="collapse collapse-plus bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300 rounded-2xl group">

                        <input type="radio" name="faq-accordion" class="peer"
                            <?php if($loop->first): ?> checked="checked" <?php endif; ?> />

                        <div
                            class="collapse-title text-base md:text-lg font-semibold text-slate-800 peer-checked:text-red-600 flex items-center gap-4 py-5 pl-6 pr-12">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-red-50 group-hover:text-red-500 peer-checked:bg-red-100 peer-checked:text-red-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <span><?php echo e($faq->question); ?></span>
                        </div>

                        <div class="collapse-content text-slate-600">
                            <div class="pt-0 pb-5 px-6 md:pl-[4.5rem] md:pr-10">
                                <div class="w-full h-px bg-slate-100 mb-4"></div>
                                <div class="leading-relaxed text-sm md:text-base prose prose-red max-w-none">
                                    <?php echo nl2br(e($faq->answer)); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    
                    <div
                        class="text-center py-16 px-4 bg-white/60 backdrop-blur-sm rounded-3xl border-2 border-dashed border-slate-300">
                        <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-search text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700">ยังไม่มีข้อมูลคำถาม</h3>
                        <p class="text-slate-500">ระบบกำลังอัปเดตข้อมูล</p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div
                class="mt-12 relative overflow-hidden bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-8 md:p-12 shadow-lg text-center">
                <div
                    class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full transform translate-x-1/2 -translate-y-1/2">
                </div>

                <div class="relative z-10">
                    <h3 class="text-2xl font-bold text-white mb-2">ยังไม่พบคำตอบที่คุณต้องการ?</h3>
                    <p class="text-red-100 mb-6">ทีมงานของเราพร้อมช่วยเหลือคุณตลอดเวลา</p>
                    <a href="/contact"
                        class="inline-flex items-center justify-center gap-2 bg-white text-red-700 hover:bg-red-50 hover:scale-105 transition-all px-6 py-3 rounded-full font-bold shadow-md">
                        <i class="fas fa-comment-dots"></i> ติดต่อเรา
                    </a>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/faq/index.blade.php ENDPATH**/ ?>