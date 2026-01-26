<?php $__env->startSection('title', $product->pd_name . ' | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        // ... (PHP ส่วนบนเหมือนเดิม) ...
        $originalPrice = (float) $product->pd_price;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $originalPrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }

        // แปลงข้อมูลโปรโมชั่นเป็น JSON (เพิ่ม partner_products)
        $promotionsData = [];
        if (isset($promotions) && $promotions->isNotEmpty()) {
            foreach ($promotions as $promo) {
                // รวบรวมของแถม
                $gifts = collect();
                foreach ($promo->actions as $action) {
                    if ($action->productToGet) {
                        $gifts->push($action->productToGet);
                    }
                    if ($action->giftableProducts) {
                        $gifts = $gifts->merge($action->giftableProducts);
                    }
                }

                $promotionsData[] = [
                    'id' => $promo->id,
                    'logic' => $promo->frontend_logic,
                    'gifts_per_item' => $promo->actions->sum(fn($a) => (int) ($a->actions['quantity_to_get'] ?? 0)),
                    'gifts' => $gifts
                        ->unique('pd_sp_id')
                        ->map(function ($g) {
                            return [
                                'id' => $g->pd_sp_id,
                                'name' => $g->pd_sp_name,
                                'image' => $g->cover_image_url ?? 'https://via.placeholder.com/150',
                            ];
                        })
                        ->values()
                        ->all(),
                    // ★ เพิ่มข้อมูลสินค้าคู่ขา ★
                    'partner_products' => $promo->partner_products
                        ->map(function ($p) {
                            return [
                                'id' => $p->pd_sp_id,
                                'name' => $p->pd_sp_name,
                                'price' => $p->pd_sp_price,
                                'image' => $p->display_image,
                                'url' => route('product.show', $p->pd_sp_id), // สมมติว่ามี Route นี้
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            }
        }
    ?>

    <div x-data="productPage({
        // ... (Config เหมือนเดิม) ...
        initialImage: <?php echo \Illuminate\Support\Js::from($product->cover_image_url)->toHtml() ?>,
        allImages: <?php echo \Illuminate\Support\Js::from($allImages)->toHtml() ?>,
        standardAction: <?php echo \Illuminate\Support\Js::from(route('cart.add', ['id' => $product->pd_sp_id]))->toHtml() ?>,
        checkoutUrl: <?php echo \Illuminate\Support\Js::from(route('payment.checkout'))->toHtml() ?>,
        promotions: <?php echo \Illuminate\Support\Js::from($promotionsData)->toHtml() ?>
    })" class="max-w-6xl mx-auto px-4 py-8 font-sans antialiased">

        

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <div class="lg:col-span-5 p-8 bg-gray-50/50">
                    
                    <div class="sticky top-8">
                        <div
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100">
                            <img :src="activeImage" class="w-full h-full object-contain p-4 transition-all duration-300">
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
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-6"><?php echo e($product->pd_name); ?></h1>
                        <div class="inline-flex items-center bg-gray-50 rounded-2xl p-4 mb-8">
                            <span class="text-4xl font-black text-emerald-600">฿<?php echo e(number_format($finalPrice)); ?></span>
                        </div>

                        
                        <template x-if="activePromotion">
                            <div class="mb-10">
                                
                                <div class="p-6 rounded-2xl border-2 border-dashed bg-red-50/30 mb-4"
                                    :class="isConditionMet ? 'border-red-300' : 'border-gray-200 opacity-75'">

                                    <h3 class="text-sm font-bold text-gray-800 mb-2">🎉 ของแถมโปรโมชั่น</h3>

                                    <template x-if="isConditionMet">
                                        <p class="text-sm text-gray-600 mb-4">
                                            ได้รับสิทธิ์เลือกฟรี <strong class="text-red-600" x-text="giftLimit"></strong>
                                            ชิ้น
                                        </p>
                                    </template>

                                    <template x-if="!isConditionMet">
                                        <div class="mb-4">
                                            <p class="text-sm text-red-500 font-bold"><i class="fas fa-lock mr-1"></i>
                                                ยังไม่ครบเงื่อนไข</p>
                                        </div>
                                    </template>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <template x-for="gift in activePromotion.gifts" :key="gift.id">
                                            <label
                                                class="flex items-center gap-4 p-3 rounded-xl border-2 transition-all duration-300"
                                                :class="{ 'bg-gray-100 border-gray-200 opacity-60 cursor-not-allowed grayscale':
                                                        !
                                                        isConditionMet, 'bg-white cursor-pointer border-gray-100 hover:border-gray-300': isConditionMet, 'border-emerald-500 bg-emerald-50': selectedGifts
                                                        .includes(gift.id) }">
                                                <input type="checkbox" :disabled="!isConditionMet"
                                                    @click="toggleGift(gift.id)" :checked="selectedGifts.includes(gift.id)"
                                                    class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 disabled:bg-gray-200">
                                                <div
                                                    class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-100">
                                                    <img :src="gift.image" class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1 overflow-hidden">
                                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="gift.name">
                                                    </p>
                                                    <span class="text-xs text-red-500 font-bold"
                                                        x-text="!isConditionMet ? 'ล็อค' : 'ฟรี'"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                
                                <template
                                    x-if="!activePromotion.logic.other_rules_met && activePromotion.partner_products.length > 0">
                                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-2xl">
                                        <h3 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                                            </svg>
                                            ต้องซื้อคู่กับสินค้าเหล่านี้ ถึงจะได้ของแถม:
                                        </h3>
                                        <div class="space-y-3">
                                            <template x-for="partner in activePromotion.partner_products"
                                                :key="partner.id">
                                                <div
                                                    class="flex items-center gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                                    <img :src="partner.image" class="w-12 h-12 rounded-lg object-cover">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-bold text-gray-800" x-text="partner.name">
                                                        </p>
                                                        <p class="text-xs text-emerald-600 font-bold">฿<span
                                                                x-text="partner.price"></span></p>
                                                    </div>
                                                    <a :href="partner.url"
                                                        class="btn btn-sm btn-outline btn-warning">ดูสินค้า</a>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

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
                    // ... (Logic เดิมทั้งหมด) ...
                    activeImage: config.initialImage,
                    images: config.allImages,
                    quantity: 1,
                    isLoading: false,
                    promotions: config.promotions || [],
                    selectedGifts: [],

                    get activePromotion() {
                        return this.promotions.length > 0 ? this.promotions[0] : null;
                    },

                    get isConditionMet() {
                        if (!this.activePromotion) return false;
                        const logic = this.activePromotion.logic;
                        const totalQty = logic.cart_qty + this.quantity;

                        if (logic.condition_type === 'all') {
                            return logic.other_rules_met && (totalQty >= logic.required_qty);
                        } else {
                            return totalQty >= logic.required_qty;
                        }
                    },

                    get giftLimit() {
                        if (!this.activePromotion || !this.isConditionMet) return 0;
                        return this.activePromotion.gifts_per_item;
                    },

                    init() {
                        this.$watch('quantity', () => {
                            this.validateSelection();
                        });
                    },

                    validateSelection() {
                        if (!this.isConditionMet) this.selectedGifts = [];
                        else if (this.selectedGifts.length > this.giftLimit) this.selectedGifts.splice(this
                            .giftLimit);
                    },

                    toggleGift(id) {
                        if (!this.isConditionMet) return;
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
                                    text: `คุณได้รับสิทธิ์ ${this.giftLimit} ชิ้น`,
                                    confirmButtonColor: '#10b981'
                                });
                            }
                        }
                    },

                    async handleAddToCartClick(isBuyNow) {
                        if (this.isConditionMet && this.giftLimit > 0 && this.selectedGifts.length !==
                            this.giftLimit) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'กรุณาเลือกของแถม',
                                text: `คุณได้รับสิทธิ์เลือกของแถมฟรี ${this.giftLimit} ชิ้น`,
                                confirmButtonColor: '#10b981'
                            });
                            return;
                        }
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