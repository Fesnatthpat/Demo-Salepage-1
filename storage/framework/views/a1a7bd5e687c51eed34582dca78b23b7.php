<?php $__env->startSection('title', $product->pd_name . ' | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        // --- 1. เตรียมข้อมูล PHP ---
        $initialBasePrice = (float) $product->pd_price;
        $initialBasePrice2 = (float) $product->pd_price2;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $initialBasePrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }

        // เตรียมข้อมูลโปรโมชั่น
        $promotionsData = [];
        if (isset($promotions) && $promotions->isNotEmpty()) {
            foreach ($promotions as $promo) {
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
                    'partner_products' => ($promo->partner_products ?? collect())
                        ->map(function ($p) {
                            return [
                                'id' => $p->pd_sp_id,
                                'name' => $p->pd_sp_name,
                                'price' => number_format($p->pd_sp_price, 0),
                                'image' => $p->display_image,
                                'url' => route('product.show', $p->pd_sp_id),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            }
        }

        $optionsData = $product->options->map(function ($option) {
            return [
                'id' => $option->option_id,
                'name' => $option->option_name,
                'price' => (float) $option->option_price,
                'final_price' => (float) $option->final_price,
                'price2' => (float) $option->option_price2,
                'stock' => (int) $option->option_stock,
                // ✅ เพิ่ม URL รูปภาพของ Option เพื่อให้ JS นำไปเปลี่ยนรูปหลัก
                'image_url' => $option->option_image_url,
            ];
        });

        $reviewImagesList = [];
        if ($product->reviewImages && $product->reviewImages->count() > 0) {
            $reviewImagesList = $product->reviewImages
                ->map(function ($img) {
                    return filter_var($img->image_url, FILTER_VALIDATE_URL)
                        ? $img->image_url
                        : asset('storage/' . $img->image_url);
                })
                ->all();
        }
    ?>

    <div x-data="productPage({
        currentProductId: <?php echo \Illuminate\Support\Js::from($product->pd_sp_id)->toHtml() ?>,
        initialImage: <?php echo \Illuminate\Support\Js::from($product->cover_image_url)->toHtml() ?>,
        allImages: <?php echo \Illuminate\Support\Js::from($allImages)->toHtml() ?>,
        initialBasePrice: <?php echo \Illuminate\Support\Js::from($initialBasePrice)->toHtml() ?>,
        initialBasePrice2: <?php echo \Illuminate\Support\Js::from($initialBasePrice2)->toHtml() ?>,
        initialDisplayPrice: <?php echo \Illuminate\Support\Js::from($product->display_price)->toHtml() ?>,
        initialStock: <?php echo \Illuminate\Support\Js::from($product->pd_sp_stock)->toHtml() ?>,
        discountAmount: <?php echo \Illuminate\Support\Js::from($discountAmount)->toHtml() ?>,
        options: <?php echo \Illuminate\Support\Js::from($optionsData)->toHtml() ?>,
        standardAction: <?php echo \Illuminate\Support\Js::from(route('cart.add', ['id' => $product->pd_sp_id]))->toHtml() ?>,
        bundleAddUrl: <?php echo \Illuminate\Support\Js::from(route('cart.addBundle'))->toHtml() ?>,
        checkoutUrl: <?php echo \Illuminate\Support\Js::from(route('payment.checkout'))->toHtml() ?>,
        promotions: <?php echo \Illuminate\Support\Js::from($promotionsData)->toHtml() ?>,
        reviewImages: <?php echo \Illuminate\Support\Js::from($reviewImagesList)->toHtml() ?>
    })" class="max-w-6xl mx-auto px-4 py-8 font-sans antialiased"
        @keydown.escape.window="isModalOpen = false; isReviewModalOpen = false;"
        @keydown.arrow-left.window="if(isModalOpen) prevImage(); if(isReviewModalOpen) prevReviewImage();"
        @keydown.arrow-right.window="if(isModalOpen) nextImage(); if(isReviewModalOpen) nextReviewImage();">

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-12">

                
                <div class="lg:col-span-5 p-8 bg-gray-50/50">
                    <div class="sticky top-8">
                        <div x-show="!imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-gray-200 animate-pulse flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-4xl"></i>
                        </div>

                        <div x-show="imagesLoaded"
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100 cursor-zoom-in group"
                            @click="isModalOpen = true">
                            <button x-show="images.length > 1" @click.stop="prevImage()"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            
                            <img :src="activeImage" @load="imagesLoaded = true"
                                class="w-full h-full object-contain p-4 transition-all duration-500 group-hover:scale-105"
                                onerror="this.src='https://via.placeholder.com/600?text=Image+Error'">

                            <button x-show="images.length > 1" @click.stop="nextImage()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 p-2 rounded-full bg-white/90 text-gray-800 shadow-md transition-all duration-300 hover:bg-red-600 hover:text-white transform hover:scale-110 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($allImages) > 1): ?>
                            <div x-show="imagesLoaded" class="grid grid-cols-5 gap-3 mt-6">
                                <template x-for="img in images" :key="img">
                                    <button @click="activeImage = img"
                                        class="aspect-square rounded-xl border-2 overflow-hidden bg-white transition-all"
                                        :class="activeImage === img ? 'border-red-500 shadow-md transform scale-105' :
                                            'border-transparent opacity-60 hover:opacity-100'">
                                        <img :src="img" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col">
                    <div class="flex-1">
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-6"><?php echo e($product->pd_name); ?></h1>

                        <div class="mb-4 text-sm font-semibold text-gray-500">
                            จำนวนสินค้าคงเหลือ:
                            <span :class="currentStock > 0 ? 'text-emerald-600' : 'text-red-500'"
                                x-text="currentStock.toLocaleString() + ' ชิ้น'"></span>
                        </div>

                        <div class="inline-flex flex-col items-start bg-gray-50 rounded-2xl p-4 mb-8">
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-red-600" x-text="finalPrice"></span>
                                <template x-if="originalPriceDisplay">
                                    <span class="text-lg text-gray-400 line-through" x-text="originalPriceDisplay"></span>
                                </template>
                            </div>
                            <template x-if="discountDisplay">
                                <span class="text-sm font-semibold text-red-500 mt-1" x-text="discountDisplay"></span>
                            </template>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->pd_details): ?>
                            <div x-data="{ expanded: false }" class="mb-8">
                                <h2 class="text-lg font-bold text-gray-800 mb-4">รายละเอียดสินค้า:</h2>
                                <div class="relative">
                                    <div class="prose max-w-none text-gray-600 transition-all duration-300"
                                        :class="expanded ? '' : 'max-h-[150px] overflow-hidden'">
                                        <?php echo nl2br(e($product->pd_details)); ?>

                                    </div>
                                    <div x-show="!expanded"
                                        class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white to-transparent pointer-events-none">
                                    </div>
                                </div>
                                <button @click="expanded = !expanded"
                                    class="text-red-600 font-bold hover:underline mt-2 flex items-center gap-1">
                                    <span x-text="expanded ? 'ย่อน้อยลง' : 'แสดงเพิ่มเติม'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform"
                                        :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <template x-if="options.length > 0">
                            <div class="mb-8">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">เลือกตัวเลือก:</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="option in options" :key="option.id">
                                        <label
                                            class="flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-300 cursor-pointer"
                                            :class="selectedOption == option.id ? 'border-red-500 bg-red-50 shadow-sm' :
                                                'border-gray-200 bg-white hover:border-red-200'">
                                            <input type="radio" name="product_option" x-model="selectedOption"
                                                :value="option.id"
                                                class="h-5 w-5 border-gray-300 text-red-600 focus:ring-red-500">
                                            <div class="flex-1">
                                                <p class="font-bold text-gray-800" x-text="option.name"></p>
                                                <p class="text-sm text-red-600 font-bold"
                                                    x-text="'฿' + option.price.toLocaleString()"></p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        
                    </div>

                    
                    <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex items-center bg-gray-100 rounded-2xl p-1.5 shadow-inner">
                            <button @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 font-bold text-lg">-</button>
                            <input type="number" x-model.number="quantity"
                                class="w-12 text-center bg-transparent border-none font-black text-gray-900 focus:ring-0 text-lg"
                                readonly>
                            <button @click="quantity++"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 font-bold text-lg">+</button>
                        </div>
                        <div class="flex-1 w-full grid grid-cols-2 gap-4">
                            <button @click="handleAddToCartClick(false)" :disabled="currentStock <= 0 || isLoading"
                                :class="currentStock > 0 ? 'border-red-600 text-red-600 hover:bg-red-50' :
                                    'border-gray-300 bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="h-14 rounded-2xl border-2 font-bold transition-all flex items-center justify-center gap-2 text-lg">
                                <span x-show="!isLoading && currentStock > 0">ใส่ตะกร้า</span>
                                <span x-show="!isLoading && currentStock <= 0">สินค้าหมด</span>
                                <span x-show="isLoading" class="loading loading-spinner"></span>
                            </button>
                            <button @click="handleAddToCartClick(true)" :disabled="currentStock <= 0 || isLoading"
                                :class="currentStock > 0 ?
                                    'bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-500/30' :
                                    'bg-gray-400 text-gray-100 cursor-not-allowed'"
                                class="h-14 rounded-2xl font-bold transition-all text-lg">
                                <span x-show="currentStock > 0">สั่งเลย</span>
                                <span x-show="currentStock <= 0">สินค้าหมด</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPage', (config) => ({
                isModalOpen: false,
                activeImage: config.initialImage,
                images: config.allImages,
                reviewImages: config.reviewImages || [],
                isReviewModalOpen: false,
                activeReviewImage: null,
                quantity: 1,
                isLoading: false,
                imagesLoaded: false,
                basePrice: config.initialBasePrice || 0,
                basePrice2: config.initialBasePrice2 || 0,
                initialStock: config.initialStock || 0,
                discountAmount: config.discountAmount || 0,
                options: config.options || [],
                selectedOption: null,
                promotions: config.promotions || [],
                selectedGifts: [],

                init() {
                    this.imagesLoaded = true;
                    this.$watch('quantity', () => this.validateSelection());

                    // ✅ ส่วนที่แก้ไข: เปลี่ยนรูปภาพหลักตามตัวเลือกที่ลูกค้าคลิกเลือก
                    this.$watch('selectedOption', (newId) => {
                        if (newId) {
                            const foundOption = this.options.find(o => o.id == newId);
                            if (foundOption && foundOption.image_url) {
                                this.activeImage = foundOption.image_url;
                            }
                        }
                    });
                },

                get currentStock() {
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        return option ? option.stock : 0;
                    }
                    return this.initialStock;
                },

                get finalPrice() {
                    if (this.options.length > 0 && !this.selectedOption)
                    return `฿${config.initialDisplayPrice}`;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option) return `฿${option.final_price.toLocaleString()}`;
                    }
                    let final = Math.max(0, this.basePrice - this.discountAmount);
                    return `฿${final.toLocaleString()}`;
                },

                get originalPriceDisplay() {
                    if (this.options.length > 0 && !this.selectedOption) return null;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option && option.price > option.final_price)
                        return `฿${option.price.toLocaleString()}`;
                        return null;
                    }
                    return this.discountAmount > 0 ? `฿${this.basePrice.toLocaleString()}` : null;
                },

                get discountDisplay() {
                    if (this.options.length > 0 && !this.selectedOption) return null;
                    if (this.selectedOption) {
                        const option = this.options.find(o => o.id == this.selectedOption);
                        if (option && option.price > option.final_price)
                        return `ประหยัด ฿${(option.price - option.final_price).toLocaleString()}`;
                        return null;
                    }
                    return this.discountAmount > 0 ?
                        `ประหยัด ฿${this.discountAmount.toLocaleString()}` : null;
                },

                prevImage() {
                    let idx = this.images.indexOf(this.activeImage);
                    this.activeImage = this.images[(idx - 1 + this.images.length) % this.images.length];
                },
                nextImage() {
                    let idx = this.images.indexOf(this.activeImage);
                    this.activeImage = this.images[(idx + 1) % this.images.length];
                },
                openReviewModal(url) {
                    this.activeReviewImage = url;
                    this.isReviewModalOpen = true;
                },
                validateSelection() {
                    if (!this.isConditionMet) this.selectedGifts = [];
                },

                async handleAddToCartClick(isBuyNow) {
                    if (this.currentStock <= 0) return;
                    if (this.options.length > 0 && !this.selectedOption) {
                        Swal.fire('กรุณาเลือกตัวเลือก', 'เลือกสินค้าก่อนหยิบใส่ตะกร้า', 'warning');
                        return;
                    }
                    if (this.isLoading) return;
                    this.isLoading = true;
                    try {
                        const response = await fetch(config.standardAction, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                quantity: this.quantity,
                                selected_option_id: this.selectedOption,
                                selected_gift_ids: this.selectedGifts
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            if (isBuyNow) window.location.href = config.checkoutUrl;
                            else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'เพิ่มสินค้าแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                Livewire.dispatch('cartUpdated');
                            }
                        } else throw new Error(data.message);
                    } catch (e) {
                        Swal.fire('ข้อผิดพลาด', e.message, 'error');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/product.blade.php ENDPATH**/ ?>