

<?php $__env->startSection('title', 'ติดตามคำสั่งซื้อ | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="container mx-auto px-4 py-8 lg:py-12 min-h-screen">
        <div class="max-w-4xl mx-auto">
            
            
            <div class="mb-8">
                <span class="text-sm font-bold text-gray-800">รหัสการจัดส่ง</span>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mt-1">
                    <h2 class="text-3xl font-black text-gray-900 tracking-wide uppercase">
                        <?php echo e($trackingData['trackingNumber'] ?? request('order_code') ?? 'N/A'); ?>

                    </h2>
                    <div class="flex gap-2">
                        <button class="btn btn-outline btn-square text-red-600 border-red-200 hover:bg-red-50 hover:border-red-600" title="พิมพ์">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="btn btn-outline btn-square text-red-600 border-red-200 hover:bg-red-50 hover:border-red-600" title="แชร์">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
            </div>

            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
                <form action="<?php echo e(route('order.tracking')); ?>" method="GET">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="form-control flex-grow relative">
                            <input type="text" name="order_code" placeholder="กรอกรหัสการจัดส่ง เพื่อติดตามสถานะ"
                                class="input input-bordered w-full" value="<?php echo e(request('order_code')); ?>" required />
                        </div>
                        <button type="submit" class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white sm:w-auto w-full px-8">
                            ค้นหา
                        </button>
                    </div>
                </form>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="alert alert-error mb-8">
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($trackingData)): ?>
                <div class="animate-fadeIn">
                    
                    
                    <div class="bg-white border border-gray-200 border-l-[3px] border-l-green-700 mb-8 shadow-sm">
                        
                        
                        <div class="p-4 flex flex-col md:flex-row items-start md:items-center gap-4 border-b border-gray-100">
                            <span class="bg-green-700 text-white px-3 py-1 text-sm font-bold rounded">จัดส่งสำเร็จ</span>
                            <span class="text-xs font-bold text-gray-800">จัดส่งภายใน: <span class="font-normal"><?php echo e($trackingData['deliveredAt']); ?></span></span>
                        </div>

                        
                        <div class="bg-gray-50 p-6 flex flex-col md:flex-row gap-6 border-b border-gray-200">
                            <div class="flex-1 text-center border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0">
                                <p class="text-red-600 font-bold text-sm mb-2">
                                    <i class="fas fa-plane-departure"></i> ที่มา
                                </p>
                                <p class="text-[11px] text-gray-800 font-bold uppercase"><?php echo e($trackingData['origin']); ?></p>
                            </div>
                            <div class="flex-1 text-center">
                                <p class="text-red-600 font-bold text-sm mb-2">
                                    <i class="fas fa-plane-arrival"></i> ปลายทาง
                                </p>
                                <p class="text-[11px] text-gray-800 font-bold uppercase"><?php echo e($trackingData['destination']); ?></p>
                            </div>
                        </div>

                        
                        <div class="p-6 md:p-8 border-b border-gray-200">
                            <h3 class="font-bold text-gray-800 text-lg mb-8">สถานะการจัดส่ง</h3>

                            <div class="relative flex justify-between items-center max-w-4xl mx-auto px-4 mt-8">
                                <div class="absolute left-0 right-0 top-1/2 h-1 bg-green-700 -z-10 -translate-y-1/2"></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $trackingData['timelineSteps']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    <div class="flex flex-col items-center relative bg-white px-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white
                                            <?php echo e($step['active'] ? 'bg-green-700' : 'bg-gray-300'); ?>

                                            <?php echo e(isset($step['is_truck']) ? 'w-12 h-12 ring-4 ring-green-100' : ''); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($step['is_truck'])): ?>
                                                <i class="fas fa-truck text-lg"></i>
                                            <?php else: ?>
                                                <i class="fas fa-check text-sm"></i>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <span class="text-[10px] font-bold <?php echo e(isset($step['is_truck']) ? 'text-gray-900' : 'text-gray-500'); ?> absolute top-12 w-24 text-center">
                                            <?php echo e($step['label']); ?>

                                        </span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                            <div class="h-12 md:hidden"></div>
                        </div>

                        
                        <div class="collapse collapse-arrow bg-white border-b border-gray-200 rounded-none">
                            <input type="checkbox" checked />
                            <div class="collapse-title text-base font-bold text-gray-800">
                                ข้อมูลการจัดส่งเพิ่มเติม
                            </div>
                            <div class="collapse-content text-sm text-gray-600 bg-white">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-4">
                                    <div>
                                        <p class="text-gray-500 text-[11px] mb-1">รหัสการจัดส่ง</p>
                                        <p class="text-xs text-gray-800"><?php echo e($trackingData['trackingNumber']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-[11px] mb-1">รหัสการติดตามสินค้า</p>
                                        <p class="text-xs text-gray-800"><?php echo e($trackingData['referenceId']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-[11px] mb-1">น้ำหนัก (กรัม)</p>
                                        <p class="text-xs text-gray-800"><?php echo e($trackingData['weight']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-[11px] mb-1">บริการจัดส่ง</p>
                                        <p class="text-xs text-gray-800"><?php echo e($trackingData['service']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="p-6 md:p-10">
                            <div class="relative ml-2 md:ml-6">
                                
                                <div class="absolute left-[11px] top-2 bottom-2 w-[2px] bg-gray-200"></div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $trackingData['events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                    
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($event['date'] && ($loop->first || $event['date'] != $trackingData['events'][$loop->index - 1]['date'])): ?>
                                        <div class="relative flex items-center mb-5 mt-8 first:mt-0">
                                            <div class="bg-gray-100 text-gray-700 text-xs font-bold px-4 py-1.5 rounded-full z-10 ml-[-4px]">
                                                <?php echo e($event['date']); ?>

                                            </div>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="relative flex items-start mb-8 last:mb-0 group">
                                        
                                        <div class="absolute left-0 top-1 z-10 w-6 flex justify-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($event['is_latest']): ?>
                                                <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-white"></div>
                                            <?php else: ?>
                                                <div class="w-3.5 h-3.5 rounded-full bg-gray-300 ring-4 ring-white"></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <div class="pl-10 w-full">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-0.5">
                                                <h4 class="text-[15px] font-bold text-gray-800">
                                                    <?php echo e($event['status']); ?>

                                                </h4>
                                                <span class="text-[13px] font-semibold text-emerald-500">
                                                    <?php echo e($event['time']); ?>

                                                </span>
                                            </div>
                                            
                                            <p class="text-[13px] text-gray-500 flex items-center gap-1.5 mt-1">
                                                <i class="fas fa-map-marker-alt text-gray-300"></i> <?php echo e($event['location']); ?>

                                            </p>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500">
                            มีข้อสงสัยเกี่ยวกับพัสดุ? <a href="/contact" class="text-emerald-600 font-semibold hover:underline">ติดต่อฝ่ายสนับสนุน</a>
                        </p>
                    </div>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out forwards;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/ordertracking.blade.php ENDPATH**/ ?>