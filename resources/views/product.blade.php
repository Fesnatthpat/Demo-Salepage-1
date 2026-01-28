@extends('layout')

@section('title', $product->pd_name . ' | Salepage Demo')

@section('content')
    @php
        $originalPrice = (float) $product->pd_price;
        $discountAmount = (float) $product->pd_sp_discount;
        $finalPrice = max(0, $originalPrice - $discountAmount);
        $allImages = $product->images->pluck('image_url')->all();
        if (empty($allImages)) {
            $allImages[] = $product->cover_image_url;
        }

        // เตรียมข้อมูลโปรโมชั่น
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
    @endphp

    {{-- ✅ UPDATE: เพิ่ม currentProductId และ bundleAddUrl ใน config --}}
    <div x-data="productPage({
        currentProductId: @js($product->pd_sp_id),
        initialImage: @js($product->cover_image_url),
        allImages: @js($allImages),
        standardAction: @js(route('cart.add', ['id' => $product->pd_sp_id])),
        bundleAddUrl: @js(route('cart.addBundle')),
        checkoutUrl: @js(route('payment.checkout')),
        promotions: @js($promotionsData)
    })" class="max-w-6xl mx-auto px-4 py-8 font-sans antialiased">

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                {{-- Image Gallery --}}
                <div class="lg:col-span-5 p-8 bg-gray-50/50">
                    <div class="sticky top-8">
                        <div
                            class="relative aspect-square rounded-2xl bg-white overflow-hidden shadow-sm border border-gray-100">
                            <img :src="activeImage" class="w-full h-full object-contain p-4 transition-all duration-300">
                        </div>
                        @if (count($allImages) > 1)
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
                        @endif
                    </div>
                </div>

                {{-- Product Details --}}
                <div class="lg:col-span-7 p-8 lg:p-12 flex flex-col">
                    <div class="flex-1">
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">{{ $product->pd_name }}</h1>

                        @if ($product->pd_details)
                            <div class="prose max-w-none text-gray-600 mb-8">
                                {!! nl2br(e($product->pd_details)) !!}
                            </div>
                        @endif

                        {{-- Stock --}}
                        <div class="mb-4 text-sm font-semibold">
                            จำนวนสินค้าคงเหลือ:
                            <span class="{{ $product->pd_sp_stock > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ number_format($product->pd_sp_stock) }} ชิ้น
                            </span>
                        </div>

                        <div class="inline-flex flex-col items-start bg-gray-50 rounded-2xl p-4 mb-8">
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-emerald-600">฿{{ number_format($finalPrice) }}</span>
                                @if ($discountAmount > 0)
                                    <span
                                        class="text-lg text-gray-400 line-through">฿{{ number_format($originalPrice) }}</span>
                                @endif
                            </div>
                            @if ($discountAmount > 0)
                                <span class="text-sm font-semibold text-red-500 mt-1">ประหยัด
                                    ฿{{ number_format($discountAmount) }}</span>
                            @endif
                        </div>

                        {{-- Promotion UI --}}
                        <template x-if="activePromotion">
                            <div id="promotion-section" class="mb-10 scroll-mt-24">

                                {{-- 1. ของแถม --}}
                                <div class="p-6 rounded-2xl border-2 border-dashed bg-red-50/30 mb-4 transition-all duration-500"
                                    :class="isConditionMet ? 'border-red-300 shadow-inner' : 'border-gray-200 opacity-75'">

                                    <h3 class="text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                                        <span>🎉 ของแถมโปรโมชั่น</span>
                                        <template x-if="activePromotion && activePromotion.gifts_per_item > 0">
                                            <span class="badge badge-lg badge-success text-white font-bold ml-2">
                                                แถม <span x-text="activePromotion.gifts_per_item"></span> ชิ้น
                                            </span>
                                        </template>
                                        <span x-show="isConditionMet"
                                            class="badge badge-success badge-sm text-white">ปลดล็อคแล้ว!</span>
                                    </h3>

                                    <template x-if="isConditionMet">
                                        <div class="mb-4 text-sm text-gray-700">
                                            ✅ เงื่อนไขครบถ้วน! คุณได้รับสิทธิ์เลือกฟรี <strong class="text-red-600 text-lg"
                                                x-text="giftLimit"></strong> ชิ้น
                                        </div>
                                    </template>

                                    <template x-if="!isConditionMet">
                                        <div class="mb-4">
                                            <p class="text-sm text-red-500 font-bold"><i class="fas fa-lock mr-1"></i>
                                                เงื่อนไขยังไม่ครบ</p>
                                        </div>
                                    </template>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <template x-for="gift in activePromotion.gifts" :key="gift.id">
                                            <label
                                                class="flex items-center gap-4 p-3 rounded-xl border-2 transition-all duration-300"
                                                :class="{
                                                    'bg-gray-100 border-gray-200 opacity-60 cursor-not-allowed grayscale': isGiftDisabled(
                                                        gift.id) && !selectedGifts.includes(gift.id),
                                                    'bg-white cursor-pointer border-emerald-200 ring-2 ring-emerald-100 hover:border-emerald-300': isConditionMet &&
                                                        !isGiftDisabled(gift.id),
                                                    'border-emerald-500 bg-emerald-50 ring-0': selectedGifts.includes(
                                                        gift.id)
                                                }">
                                                <input type="checkbox"
                                                    :disabled="!isConditionMet || (selectedGifts.length >= giftLimit && !
                                                        selectedGifts.includes(gift.id))"
                                                    @click="toggleGift(gift.id)" :checked="selectedGifts.includes(gift.id)"
                                                    class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 disabled:bg-gray-200 disabled:cursor-not-allowed">

                                                <div
                                                    class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-gray-100">
                                                    <img :src="gift.image" class="w-full h-full object-cover">
                                                </div>
                                                <div class="flex-1 overflow-hidden">
                                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="gift.name">
                                                    </p>
                                                    <span class="text-xs font-bold"
                                                        :class="!isConditionMet ? 'text-gray-400' : 'text-red-500'"
                                                        x-text="!isConditionMet ? 'ล็อค' : 'เลือกฟรี'"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                {{-- 2. ซื้อคู่ (Partner Products) --}}
                                <template
                                    x-if="!activePromotion.logic.other_rules_met && activePromotion.partner_products.length > 0">
                                    <div class="p-5 bg-orange-50 border border-orange-200 rounded-2xl animate-pulse-once">
                                        <h3 class="text-sm font-bold text-orange-800 mb-3 flex items-center">
                                            <span class="bg-orange-200 text-orange-700 p-1 rounded mr-2"><i
                                                    class="fas fa-exclamation"></i></span>
                                            ซื้อคู่กับสินค้าด้านล่าง เพื่อรับของแถม:
                                        </h3>
                                        <div class="space-y-3">
                                            <template x-for="partner in activePromotion.partner_products"
                                                :key="partner.id">
                                                <div
                                                    class="flex items-center gap-4 bg-white p-3 rounded-xl border border-orange-100 shadow-sm hover:shadow-md transition-shadow">
                                                    <img :src="partner.image"
                                                        class="w-16 h-16 rounded-lg object-cover border border-gray-100">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-bold text-gray-800" x-text="partner.name">
                                                        </p>
                                                        <p class="text-sm text-emerald-600 font-bold">฿<span
                                                                x-text="partner.price"></span></p>
                                                    </div>

                                                    {{-- ปุ่มกดใส่ตะกร้า (แก้ให้ใช้ addToCartPartner แบบ Bundle) --}}
                                                    <div class="flex flex-col gap-2">
                                                        <button @click="addToCartPartner(partner.id)" type="button"
                                                            class="btn btn-sm btn-primary text-white shadow-sm flex items-center gap-1 border-none bg-emerald-600 hover:bg-emerald-700">
                                                            <i class="fas fa-plus"></i> เพิ่มทันที
                                                        </button>
                                                        <a :href="partner.url"
                                                            class="text-[10px] text-gray-400 text-center hover:text-emerald-600 underline">รายละเอียด</a>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>

                    {{-- Main Actions --}}
                    <div class="pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex items-center bg-gray-100 rounded-2xl p-1.5 shadow-inner">
                            <button @click="quantity > 1 ? quantity-- : null"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors font-bold text-lg">-</button>
                            <input type="number" x-model.number="quantity"
                                class="w-12 text-center bg-transparent border-none font-black text-gray-900 focus:ring-0 text-lg"
                                readonly>
                            <button @click="quantity++"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-emerald-600 transition-colors font-bold text-lg">+</button>
                        </div>
                        <div class="flex-1 w-full grid grid-cols-2 gap-4">
                            <button @click="handleAddToCartClick(false)"
                                class="h-14 rounded-2xl border-2 border-emerald-600 text-emerald-600 font-bold hover:bg-emerald-50 transition-all flex items-center justify-center gap-2 text-lg">
                                <span x-show="!isLoading">ใส่ตะกร้า</span>
                                <span x-show="isLoading" class="loading loading-spinner"></span>
                            </button>
                            <button @click="handleAddToCartClick(true)"
                                class="h-14 rounded-2xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg transition-all text-lg">Buy
                                Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('productPage', (config) => ({
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

                    isGiftDisabled(giftId) {
                        if (!this.isConditionMet) return true;
                        if (this.selectedGifts.includes(giftId)) return false;
                        return this.selectedGifts.length >= this.giftLimit;
                    },

                    init() {
                        this.$watch('quantity', () => {
                            this.validateSelection();
                        });
                        if (localStorage.getItem('justAddedPartner') === 'true') {
                            localStorage.removeItem('justAddedPartner');
                            setTimeout(() => {
                                const promoSection = document.getElementById('promotion-section');
                                if (promoSection) {
                                    promoSection.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'ปลดล็อคของแถมแล้ว!',
                                        text: 'คุณสามารถเลือกของแถมได้เลย',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            }, 500);
                        }
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
                                    confirmButtonColor: '#10b981'
                                });
                            }
                        }
                    },

                    // ✅ UPDATE: แก้ไขให้ยิงเข้า Route Bundle
                    async addToCartPartner(partnerId) {
                        if (this.isLoading) return;
                        this.isLoading = true;
                        try {
                            // ใช้ URL ใหม่ (cart.addBundle)
                            const response = await fetch(config.bundleAddUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    main_product_id: config
                                        .currentProductId, // สินค้าหลัก (หน้าปัจจุบัน)
                                    secondary_product_id: partnerId, // สินค้าคู่ (ที่กดเพิ่ม)
                                    gift_ids: this.selectedGifts // ของแถม (ถ้าเลือกไว้)
                                })
                            });
                            const data = await response.json();
                            if (data.success) {
                                localStorage.setItem('justAddedPartner', 'true');
                                window.location.reload();
                            } else {
                                throw new Error(data.message);
                            }
                        } catch (e) {
                            Swal.fire('ข้อผิดพลาด', e.message || 'ไม่สามารถทำรายการได้', 'error');
                            this.isLoading = false;
                        }
                    },

                    async handleAddToCartClick(isBuyNow) {
                        if (this.isConditionMet && this.giftLimit > 0 && this.selectedGifts.length !==
                            this.giftLimit) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'กรุณาเลือกของแถม',
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
    @endpush
@endsection
