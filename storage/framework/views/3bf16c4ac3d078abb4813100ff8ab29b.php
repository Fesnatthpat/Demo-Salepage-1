

<?php $__env->startSection('title', 'จัดการโปรโมชั่น'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="{ 
        copyCode(code) {
            navigator.clipboard.writeText(code);
            // Optional: You could trigger a toast here
        }
    }">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">Campaign Manager</h1>
                <p class="text-gray-400 text-sm mt-1">จัดการแคมเปญส่วนลด คูปอง และโปรโมชั่นของแถม</p>
            </div>
            <a href="<?php echo e(route('admin.promotions.create')); ?>"
                class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-emerald-600 rounded-xl hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 shadow-lg shadow-emerald-900/30 overflow-hidden">
                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                <i class="fas fa-plus mr-2 transition-transform group-hover:rotate-90"></i> สร้างแคมเปญใหม่
            </a>
        </div>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 animate-fade-in-up">
            <div class="bg-gray-800 rounded-2xl p-5 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-emerald-500/30 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-10 -mt-10 group-hover:bg-emerald-500/10 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 shadow-inner">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">ใช้งานอยู่ (Active)</p>
                    <p class="text-3xl font-black text-white mt-1"><?php echo e($promotions->where('is_active', true)->count()); ?></p>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-2xl p-5 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-blue-500/30 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/5 rounded-full -mr-10 -mt-10 group-hover:bg-blue-500/10 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 shadow-inner">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">แบบใช้โค้ด (Coupon)</p>
                    <p class="text-3xl font-black text-white mt-1"><?php echo e($promotions->whereNotNull('code')->count()); ?></p>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-2xl p-5 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-pink-500/30 transition-colors">
                <div class="absolute right-0 top-0 w-32 h-32 bg-pink-500/5 rounded-full -mr-10 -mt-10 group-hover:bg-pink-500/10 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-pink-500/10 flex items-center justify-center text-pink-400 shadow-inner">
                    <i class="fas fa-gifts text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">โปรฯ ของแถม (BxGy)</p>
                    <p class="text-3xl font-black text-white mt-1">
                        <?php echo e($promotions->filter(fn($p) => $p->rules->count() > 0)->count()); ?>

                    </p>
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="flex items-center p-4 mb-4 text-emerald-400 rounded-xl bg-emerald-900/20 border border-emerald-500/20 shadow-lg backdrop-blur-sm" role="alert">
                <div class="p-2 bg-emerald-500/20 rounded-lg mr-3">
                    <i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i>
                </div>
                <div class="text-sm font-medium"><?php echo e(session('success')); ?></div>
                <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-emerald-400 rounded-lg p-1.5 hover:bg-emerald-900/40 inline-flex h-8 w-8 items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900/80 border-b border-gray-700 text-xs uppercase text-gray-400 font-bold tracking-wider">
                            <th class="px-6 py-5">แคมเปญ</th>
                            <th class="px-6 py-5 text-center">ประเภท</th>
                            <th class="px-6 py-5 text-center">มูลค่า/เงื่อนไข</th>
                            <th class="px-6 py-5 text-center">การใช้งาน</th>
                            <th class="px-6 py-5 text-center">สถานะ</th>
                            <th class="px-6 py-5 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                            <tr class="hover:bg-gray-700/30 transition-colors duration-150 group">
                                
                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col">
                                        <span class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors mb-1.5">
                                            <?php echo e($promo->name); ?>

                                        </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->description): ?>
                                            <span class="text-xs text-gray-500 line-clamp-1 mb-2" title="<?php echo e($promo->description); ?>">
                                                <?php echo e($promo->description); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="flex items-center gap-2 mt-auto text-[10px] text-gray-400 font-mono bg-gray-900/50 w-fit px-2 py-1 rounded border border-gray-700/50">
                                            <i class="far fa-clock"></i>
                                            <span>
                                                <?php echo e($promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('d M y') : 'Now'); ?> 
                                                <i class="fas fa-arrow-right mx-1 text-gray-600"></i>
                                                <?php echo e($promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('d M y') : '∞'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-5 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->code): ?>
                                        <button @click="copyCode('<?php echo e($promo->code); ?>')" class="group/btn inline-flex flex-col items-center gap-1 cursor-pointer transition-transform active:scale-95" title="คลิกเพื่อคัดลอก">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 group-hover/btn:bg-blue-500/20 group-hover/btn:border-blue-500/40 transition-all">
                                                <i class="fas fa-ticket-alt"></i> Code
                                            </span>
                                            <span class="font-mono text-sm font-bold text-white group-hover/btn:text-blue-300"><?php echo e($promo->code); ?></span>
                                        </button>
                                    <?php elseif($promo->rules->count() > 0): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-pink-500/10 text-pink-400 border border-pink-500/20 shadow-sm shadow-pink-900/20">
                                            <i class="fas fa-gift"></i> Buy X Get Y
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-sm shadow-emerald-900/20">
                                            <i class="fas fa-bolt"></i> Auto
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-5 text-center align-middle">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($promo->discount_value)): ?>
                                        <div class="flex flex-col items-center">
                                            <span class="text-white font-mono text-xl font-bold tracking-tight">
                                                <?php echo e(number_format($promo->discount_value, 0)); ?><span class="text-sm text-gray-500 ml-0.5"><?php echo e($promo->discount_type === 'percentage' ? '%' : '฿'); ?></span>
                                            </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->min_order_value > 0): ?>
                                                <span class="text-[10px] text-gray-500 bg-gray-900 px-1.5 py-0.5 rounded mt-1">
                                                    ขั้นต่ำ ฿<?php echo e(number_format($promo->min_order_value)); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php elseif($promo->rules->count() > 0): ?>
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <div class="flex flex-col items-center bg-gray-900/50 px-2 py-1 rounded border border-gray-700/50">
                                                <span class="text-gray-400 text-[10px] uppercase">Buy</span>
                                                <span class="text-white font-bold"><?php echo e($promo->rules->sum('quantity')); ?> ชิ้น</span>
                                            </div>
                                            <i class="fas fa-arrow-right text-gray-600 text-[10px]"></i>
                                            <div class="flex flex-col items-center bg-pink-900/20 px-2 py-1 rounded border border-pink-500/20">
                                                <span class="text-pink-400 text-[10px] uppercase">Get</span>
                                                <span class="text-pink-300 font-bold"><?php echo e($promo->actions->sum('quantity')); ?> ชิ้น</span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-600">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-5 align-middle">
                                    <div class="flex flex-col gap-2">
                                        
                                        <div class="flex justify-center gap-3">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs text-gray-500 uppercase font-bold tracking-tighter">ครั้ง</span>
                                                <span class="text-emerald-400 font-bold"><?php echo e(number_format($promo->usage_count)); ?></span>
                                            </div>
                                            <div class="w-px h-8 bg-gray-700"></div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs text-gray-500 uppercase font-bold tracking-tighter">คน</span>
                                                <span class="text-blue-400 font-bold"><?php echo e(number_format($promo->unique_users_count)); ?></span>
                                            </div>
                                        </div>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promo->usage_limit): ?>
                                            <div class="w-full max-w-[120px] mx-auto">
                                                <div class="w-full bg-gray-700 rounded-full h-1 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-400 h-1 rounded-full transition-all duration-500"
                                                        style="width: <?php echo e(min(100, ($promo->used_count / $promo->usage_limit) * 100)); ?>%">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-[8px] font-medium text-gray-500 mt-1 uppercase">
                                                    <span>Limit</span>
                                                    <span><?php echo e(number_format($promo->usage_limit)); ?></span>
                                                </div>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="text-center">
                                            <a href="<?php echo e(route('admin.promotions.logs', ['promotion' => $promo->name])); ?>" 
                                               class="text-[10px] text-emerald-500/70 hover:text-emerald-400 underline underline-offset-2 transition-colors">
                                                <i class="fas fa-history mr-1"></i>ดูประวัติการใช้
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="px-6 py-5 text-center align-middle">
                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($promo->is_active ? 'bg-emerald-900/30 text-emerald-400' : 'bg-gray-700/50 text-gray-500'); ?>">
                                        <div class="w-1.5 h-1.5 rounded-full mr-1.5 <?php echo e($promo->is_active ? 'bg-emerald-400 shadow-[0_0_5px_rgba(16,185,129,0.8)]' : 'bg-gray-500'); ?>"></div>
                                        <?php echo e($promo->is_active ? 'Active' : 'Inactive'); ?>

                                    </div>
                                </td>

                                
                                <td class="px-6 py-5 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <a href="<?php echo e(route('admin.promotions.edit', $promo->id)); ?>"
                                            class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-indigo-600 rounded-lg transition-all shadow-sm"
                                            title="แก้ไข">
                                            <i class="fas fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.promotions.destroy', $promo->id)); ?>" method="POST"
                                            onsubmit="return confirm('ยืนยันการลบ? ข้อมูลนี้ไม่สามารถกู้คืนได้');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-red-600 rounded-lg transition-all shadow-sm"
                                                title="ลบ">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-inner border border-gray-700">
                                            <i class="fas fa-inbox text-4xl opacity-30"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-300">ยังไม่มีข้อมูลโปรโมชั่น</p>
                                        <p class="text-sm mt-1 text-gray-500">เริ่มต้นสร้างแคมเปญใหม่ได้เลย</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promotions->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
                    <?php echo e($promotions->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>