

<?php $__env->startSection('title', 'ติดตามคำสั่งซื้อ | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #fef2f2;
        }

        /* พื้นหลังแดงอ่อนมากๆ */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Red Pulse Animation */
        .timeline-pulse {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            animation: pulse-red 2s infinite;
            border-radius: 50%;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }
    </style>

    <div class="container mx-auto px-4 py-10 min-h-screen">
        <div class="max-w-3xl mx-auto">

            
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-slate-800 mb-6 uppercase tracking-tight">Track Your Order</h1>

                <div class="bg-white p-2 rounded-2xl shadow-xl shadow-red-100 border border-red-50">
                    <form action="<?php echo e(route('order.tracking')); ?>" method="GET" class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-grow">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-red-300"></i>
                            <input type="text" name="order_code" placeholder="กรอกรหัสการจัดส่งของคุณ..."
                                class="input border-none bg-red-50/50 w-full pl-12 focus:ring-2 focus:ring-red-500 focus:outline-none h-14 rounded-xl text-lg text-red-900"
                                value="<?php echo e(request('order_code')); ?>" required />
                        </div>
                        <button type="submit"
                            class="btn btn-primary bg-red-600 hover:bg-red-700 border-none text-white h-14 px-10 rounded-xl text-lg transition-all shadow-lg shadow-red-200">
                            ค้นหาพัสดุ
                        </button>
                    </form>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div
                    class="alert alert-error bg-rose-100 border-rose-200 text-rose-700 mb-8 rounded-xl animate-fadeIn shadow-sm">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($trackingData)): ?>
                <div class="animate-fadeIn">

                    
                    <div class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden mb-6">

                        
                        <div class="bg-red-600 p-6 flex justify-between items-center text-white">
                            <div>
                                <p class="text-red-100 text-xs font-bold uppercase tracking-wider mb-1">สถานะปัจจุบัน</p>
                                <h3 class="text-xl font-bold flex items-center gap-2">
                                    <span class="w-3 h-3 bg-white rounded-full inline-block animate-pulse"></span>
                                    <?php echo e($trackingData['status_text'] ?? 'จัดส่งสำเร็จ'); ?>

                                </h3>
                            </div>
                            <div class="text-right">
                                <p class="text-red-100 text-xs font-bold uppercase tracking-wider mb-1">เลขพัสดุ</p>
                                <p class="font-black tracking-widest uppercase"><?php echo e($trackingData['trackingNumber']); ?></p>
                            </div>
                        </div>

                        
                        <div class="px-8 py-12 bg-white">
                            <div class="relative flex justify-between items-center w-full">
                                
                                <div class="absolute h-1 bg-red-50 left-0 right-0 top-1/2 -translate-y-1/2 rounded-full">
                                </div>
                                
                                <div class="absolute h-1 bg-red-600 left-0 top-1/2 -translate-y-1/2 rounded-full transition-all duration-1000"
                                    style="width: 100%"></div>

                                <?php
                                    $steps = [
                                        ['icon' => 'fa-box-open', 'label' => 'เตรียมของ'],
                                        ['icon' => 'fa-warehouse', 'label' => 'เข้าระบบ'],
                                        ['icon' => 'fa-shipping-fast', 'label' => 'กำลังส่ง'],
                                        ['icon' => 'fa-home', 'label' => 'รับแล้ว'],
                                    ];
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="relative z-10 flex flex-col items-center">
                                        <div
                                            class="w-12 h-12 rounded-full flex items-center justify-center border-4 border-white transition-all shadow-md
                                            <?php echo e($index <= 3 ? 'bg-red-600 text-white' : 'bg-red-100 text-red-400'); ?>

                                            <?php echo e($index == 3 ? 'ring-4 ring-red-50' : ''); ?>">
                                            <i class="fas <?php echo e($step['icon']); ?> text-sm"></i>
                                        </div>
                                        <span
                                            class="absolute -bottom-8 text-[11px] font-bold <?php echo e($index <= 3 ? 'text-red-600' : 'text-slate-400'); ?> whitespace-nowrap uppercase tracking-tighter"><?php echo e($step['label']); ?></span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 md:grid-cols-2 border-t border-red-50 bg-red-50/30">
                            <div class="p-6 border-b md:border-b-0 md:border-r border-red-50">
                                <p class="text-red-400 text-[11px] font-bold uppercase mb-2"><i
                                        class="fas fa-map-marker-alt mr-1"></i> ต้นทาง</p>
                                <p class="text-slate-800 font-bold text-sm"><?php echo e($trackingData['origin']); ?></p>
                            </div>
                            <div class="p-6">
                                <p class="text-red-400 text-[11px] font-bold uppercase mb-2"><i
                                        class="fas fa-flag-checkered mr-1"></i> ปลายทาง</p>
                                <p class="text-slate-800 font-bold text-sm"><?php echo e($trackingData['destination']); ?></p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="bg-white rounded-3xl shadow-sm border border-red-50 p-6 md:p-10 mb-8">
                        <h3 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-3">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <i class="fas fa-stream text-red-600"></i>
                            </div>
                            บันทึกการเดินทาง
                        </h3>

                        <div class="relative">
                            <div class="absolute left-[17px] top-2 bottom-2 w-[2px] bg-red-50"></div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $trackingData['events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <div class="relative flex items-start mb-10 last:mb-0 group">
                                    <div class="absolute left-0 top-1.5 z-10 w-[36px] flex justify-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($event['is_latest']): ?>
                                            <div class="w-4 h-4 bg-red-600 border-4 border-white timeline-pulse"></div>
                                        <?php else: ?>
                                            <div class="w-4 h-4 bg-red-200 border-4 border-white rounded-full"></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <div class="pl-14 w-full">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-2 gap-1">
                                            <h4
                                                class="text-base font-bold <?php echo e($event['is_latest'] ? 'text-red-700' : 'text-slate-700'); ?>">
                                                <?php echo e($event['status']); ?>

                                            </h4>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-400"><?php echo e($event['date']); ?></span>
                                                <span
                                                    class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded-full"><?php echo e($event['time']); ?></span>
                                            </div>
                                        </div>
                                        <p class="text-sm text-slate-500 flex items-start gap-2 italic">
                                            <i class="fas fa-map-pin mt-1 text-red-200"></i>
                                            <?php echo e($event['location']); ?>

                                        </p>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="text-center pb-10">
                        <p class="text-slate-400 text-sm">พบปัญหาในการจัดส่ง? <a href="#"
                                class="text-red-600 font-bold hover:underline">ติดต่อเรา 24 ชม.</a></p>
                    </div>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/ordertracking.blade.php ENDPATH**/ ?>