<?php $__env->startSection('title', $product->pd_name . ' | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $originalPrice = (float) $product->pd_price;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $originalPrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }
        $discountPercentage = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;

        // --- New Promotion Logic for Product Page ---
        $giftableProducts = collect();
        $giftsPerItem = 0;
        if (isset($promotions) && $promotions->isNotEmpty()) {
            // Get a unique list of all possible giftable products from all actions
            $giftableProducts = $promotions->flatMap(function ($promo) {
                return $promo->actions->flatMap(function ($action) {
                    $gifts = collect();
                    if (isset($action->productToGet)) {
                        $gifts->push($action->productToGet);
                    }
                    if (isset($action->giftableProducts) && $action->giftableProducts->isNotEmpty()) {
                        return $gifts->merge($action->giftableProducts);
                    }
                    return $gifts;
                });
            })->unique('pd_sp_id');

            // Calculate how many gifts the user gets for ONE main item.
            $giftsPerItem = $promotions->sum(function ($promo) {
                return $promo->actions->sum(fn($action) => (int)($action->actions['quantity_to_get'] ?? 0));
            });
        }
    ?>

    <div x-data="productPage({
        initialImage: <?php echo \Illuminate\Support\Js::from($product->cover_image_url)->toHtml() ?>,
        allImages: <?php echo \Illuminate\Support\Js::from($allImages)->toHtml() ?>,
        standardAction: <?php echo \Illuminate\Support\Js::from(route('cart.add', ['id' => $product->pd_sp_id]))->toHtml() ?>,
        checkoutUrl: <?php echo \Illuminate\Support\Js::from(route('payment.checkout'))->toHtml() ?>,
        giftsPerItem: <?php echo e($giftsPerItem); ?>

    })" class="max-w-6xl mx-auto px-4 py-8 font-sans antialiased">

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-12">

                
                <div class="lg:col-span-5 p-8 bg-gray-50/50">
                    <div class="sticky top-8">
                        <div
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100">
                            <img :src="activeImage" class="w-full h-full object-contain p-4 transition-all duration-300">
                            <?php if($discountAmount > 0): ?>
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="bg-red-500 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-lg">SAVE
                                        <?php echo e($discountPercentage); ?>%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if(count($allImages) > 1): ?>
                            <div class="grid grid-cols-5 gap-3 mt-6">
                                <template x-for="img in images" :key="img">
                                    <button @click="activeImage = img"
                                        class="aspect-square rounded-xl border-2 overflow-hidden bg-white transition-all"
                                        :class="activeImage === img ? 'border-emerald-500 shadow-md transform scale-105' :
                                            'border-transparent opacity-60'">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col">
                    <div class="flex-1">
                        <nav class="flex text-xs font-medium text-gray-400 mb-4 uppercase tracking-widest">
                            <span>Code: <?php echo e($product->pd_code); ?></span>
                            <span class="mx-2">•</span>
                            <span class="<?php echo e($product->pd_sp_stock > 0 ? 'text-emerald-500' : 'text-red-400'); ?>">
                                <?php echo e($product->pd_sp_stock > 0 ? 'In Stock (' . $product->pd_sp_stock . ')' : 'Out of Stock'); ?>

                            </span>
                        </nav>

                        <h1 class="text-3xl font-extrabold text-gray-900 mb-6"><?php echo e($product->pd_name); ?></h1>

                        <div class="inline-flex items-center bg-gray-50 rounded-2xl p-4 mb-8">
                            <div class="mr-6">
                                <span class="block text-xs font-bold text-gray-400 uppercase">Price</span>
                                <span class="text-4xl font-black text-emerald-600">฿<?php echo e(number_format($finalPrice)); ?></span>
                            </div>
                            <?php if($discountAmount > 0): ?>
                                <div class="border-l border-gray-200 pl-6">
                                    <span class="block text-xs font-bold text-gray-400 uppercase">Was</span>
                                    <span
                                        class="text-xl text-gray-300 line-through">฿<?php echo e(number_format($originalPrice)); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <?php if($giftableProducts->isNotEmpty() && $giftsPerItem > 0): ?>
                            <div class="mb-10 p-6 rounded-2xl border-2 border-dashed border-red-100 bg-red-50/30">
                                <h3 class="text-sm font-bold text-gray-800 mb-1">
                                    🎉 โปรโมชั่นพิเศษ!
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    ซื้อสินค้านี้ <span x-text="quantity">1</span> ชิ้น
                                    รับฟรี <strong class="text-red-600" x-text="giftLimit"></strong> ชิ้น
                                    (เลือกแล้ว <span x-text="selectedGifts.length">0</span>/<span x-text="giftLimit"></span>)
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <?php $__currentLoopData = $giftableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label
                                            class="flex items-center gap-4 p-3 bg-white rounded-xl border-2 cursor-pointer transition-all duration-300"
                                            :class="selectedGifts.includes(<?php echo e($gift->pd_sp_id); ?>) ? 'border-emerald-500 bg-emerald-50' : 'border-gray-100 hover:border-gray-300'">
                                            <input type="checkbox"
                                                   @click="toggleGift(<?php echo e($gift->pd_sp_id); ?>)"
                                                   :checked="selectedGifts.includes(<?php echo e($gift->pd_sp_id); ?>)"
                                                   class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                            <div
                                                class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-100">
                                                <img src="<?php echo e($gift->cover_image_url ?? 'https://via.placeholder.com/150'); ?>" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-xs font-bold text-gray-800 truncate">
                                                    <?php echo e($gift->pd_sp_name); ?></p>
                                                <span class="text-xs text-red-500 font-bold">ฟรี</span>
                                            </div>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                    </div>
                    
                    
                    <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex items-center bg-gray-100 rounded-2xl p-1.5 shadow-inner">
                            <button @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors">-</button>
                            <input type="number" x-model.number="quantity"
                                class="w-12 text-center bg-transparent border-none font-black text-gray-900 focus:ring-0"
                                readonly>
                            <button @click="quantity++"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors">+</button>
                        </div>
                        <div class="flex-1 w-full grid grid-cols-2 gap-4">
                            <button @click="handleAddToCartClick(false)"
                                class="h-14 rounded-2xl border-2 border-emerald-600 text-emerald-600 font-bold hover:bg-emerald-50 transition-all flex items-center justify-center gap-2">
                                <span x-show="!isLoading">ใส่ตะกร้า</span>
                                <span x-show="isLoading" class="loading loading-spinner"></span>
                            </button>
                            <button @click="handleAddToCartClick(true)"
                                class="h-14 rounded-2xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg transition-all">Buy
                                Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productPage', (config) => ({
                    activeImage: config.initialImage,
                    images: config.allImages,
                    quantity: 1,
                    isLoading: false,
                    giftsPerItem: config.giftsPerItem || 0,
                    selectedGifts: [],
                    giftLimit: 0,
                    
                    init() {
                        this.calculateGiftLimit();
                        this.$watch('quantity', () => {
                            this.calculateGiftLimit();
                        });
                    },

                    calculateGiftLimit() {
                        this.giftLimit = this.quantity * this.giftsPerItem;
                        // If limit decreases, slice the array to fit
                        if (this.selectedGifts.length > this.giftLimit) {
                            this.selectedGifts.splice(this.giftLimit);
                        }
                    },

                    toggleGift(id) {
                        const index = this.selectedGifts.indexOf(id);
                        if (index > -1) {
                            this.selectedGifts.splice(index, 1);
                        } else {
                            if (this.selectedGifts.length < this.giftLimit) {
                                this.selectedGifts.push(id);
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'เลือกของแถมครบแล้ว',
                                    text: `คุณสามารถเลือกของแถมได้สูงสุด ${this.giftLimit} ชิ้น`,
                                    confirmButtonColor: '#10b981'
                                });
                            }
                        }
                    },

                    async handleAddToCartClick(isBuyNow) {
                        if (this.isLoading) return;
                        this.isLoading = true;
                        try {
                            const response = await fetch(config.standardAction, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    quantity: this.quantity,
                                    selected_gift_ids: this.selectedGifts
                                })
                            });
                            const data = await response.json();
                            if (data.success) {
                                if (isBuyNow) window.location.href = config.checkoutUrl;
                                else Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มสินค้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                throw new Error(data.message);
                            }
                        } catch (e) {
                            Swal.fire('ข้อผิดพลาด', e.message || 'ไม่สามารถทำรายการได้', 'error');
                        } finally {
                            this.isLoading = false;
                        }
                    }
                }));
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/product.blade.php ENDPATH**/ ?>