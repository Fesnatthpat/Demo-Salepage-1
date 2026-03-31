@extends('layout')

@section('title', 'ชำระเงินและที่อยู่จัดส่ง | Salepage Demo')

@section('content')
    <div class="bg-gray-50/50 min-h-screen py-4 sm:py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">

            {{-- Alerts --}}
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-6 flex items-center gap-3 text-sm sm:text-base">
                    <i class="fas fa-exclamation-circle text-lg sm:text-xl"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-4 rounded-lg shadow-sm mb-6 text-sm sm:text-base">
                    <strong class="font-bold flex items-center gap-2 mb-2"><i class="fas fa-times-circle"></i> เกิดข้อผิดพลาด!</strong>
                    <ul class="list-disc list-inside ml-2 sm:ml-6 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $grandTotal = $totalAmount;
                $shippingCostInitial = $shippingCost ?? 0;
                $discount = $totalDiscount;
                $finalTotal = $grandTotal + $shippingCostInitial;
                $shippingMode = \App\Models\ShippingSetting::get('shipping_mode', 'global');
            @endphp

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">ชำระเงิน (Checkout)</h1>
                <div class="hidden sm:block h-6 w-px bg-gray-300"></div>
                <span class="text-gray-500 font-medium text-sm sm:text-base">ขั้นตอนสุดท้าย</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start" 
                x-data="paymentSummaryPage({
                    initialTotalOriginalAmount: {{ $totalOriginalAmount }},
                    initialGrandTotal: {{ $totalAmount }},
                    initialShippingCost: {{ $shippingCostInitial }},
                    initialTotalDiscount: {{ $totalDiscount }},
                    initialFinalTotal: {{ $finalTotal }},
                    initialDiscountCode: @js(app(\App\Services\CartService::class)->getAppliedPromoCode()),
                    selectedItems: @js($selectedItems),
                    selectedFreebies: @js($selectedFreebies),
                    initialShippingMethodId: {{ $defaultMethod->id ?? 'null' }},
                    itemCount: {{ $totalItemCount ?? 1 }},
                    availableCoupons: @js($availableCoupons->map(fn($c) => ['code' => $c->code, 'name' => $c->name]))
                })">
                
                {{-- ⬅️ ฝั่งซ้าย: ข้อมูลที่อยู่, รายการสินค้า, ช่องทางชำระ --}}   
                <div class="lg:col-span-8 space-y-6">

                    {{-- 📍 1. ส่วนที่อยู่ --}}
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-5 sm:p-6 md:p-8">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-red-500"></i> ที่อยู่จัดส่ง
                            </h2>
                            @if ($addresses->count() > 0)
                                <button type="button" onclick="document.getElementById('modal_add_new').showModal()" class="w-full sm:w-auto text-sm font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl sm:rounded-full transition-colors flex justify-center items-center">
                                    <i class="fas fa-plus mr-1"></i> เพิ่มที่อยู่
                                </button>
                            @endif
                        </div>

                        <div x-data="{
                            addresses: @js($addresses->load(['province', 'amphure', 'district'])),
                            init() {
                                let stored = localStorage.getItem('selected_address_id');
                                let defaultId = this.addresses.length > 0 ? this.addresses[0].id : null;
                                let activeId = stored ? parseInt(stored) : defaultId;
                                if (activeId) {
                                    $nextTick(() => { this.selectAddress(activeId); });
                                }
                            },
                            selectAddress(id) {
                                this.setActiveAddress(id);
                            },
                            async deleteAddress(id) {
                                const result = await Swal.fire({
                                    title: 'ลบที่อยู่นี้?',
                                    text: 'คุณไม่สามารถกู้คืนข้อมูลนี้ได้',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#ef4444',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'ใช่, ลบเลย',
                                    cancelButtonText: 'ยกเลิก',
                                    borderRadius: '1rem'
                                });

                                if (result.isConfirmed) {
                                    showLoading();
                                    try {
                                        const response = await fetch(`/address/${id}`, {
                                            method: 'DELETE',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest',
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                            }
                                        });
                                        const data = await response.json();
                                        if (data.success) {
                                            this.addresses = this.addresses.filter(a => a.id !== id);
                                            if (this.activeAddressId === id) {
                                                let nextId = this.addresses.length > 0 ? this.addresses[0].id : null;
                                                this.selectAddress(nextId);
                                            }
                                            Swal.fire('สำเร็จ', data.message, 'success');
                                        }
                                    } catch (e) {
                                        Swal.fire('ผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                                    } finally {
                                        hideLoading();
                                    }
                                }
                            },
                            async submitAddressForm(e, mode, id = null) {
                                if (document.getElementById('loading-overlay').classList.contains('hidden') === false && e.isTrusted) return;
                                
                                e.preventDefault();
                                const form = e.target;
                                const formData = new FormData(form);
                                showLoading();

                                try {
                                    const url = mode === 'add' ? '{{ route('address.save') }}' : `/address/${id}`;
                                    if (mode === 'edit') formData.append('_method', 'PUT');

                                    const response = await fetch(url, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                        }
                                    });

                                    const data = await response.json();
                                    if (data.success) {
                                        if (mode === 'add') {
                                            this.addresses.unshift(data.address);
                                            this.selectAddress(data.address.id);
                                            document.getElementById('modal_add_new').close();
                                        } else {
                                            const index = this.addresses.findIndex(a => a.id === id);
                                            this.addresses[index] = data.address;
                                            document.getElementById(`modal_edit_${id}`).close();
                                            if (this.activeAddressId === id) {
                                                this.selectAddress(id);
                                            }
                                        }
                                        form.reset();
                                        Swal.fire('สำเร็จ', data.message, 'success');
                                    } else {
                                        Swal.fire('ผิดพลาด', data.message || 'กรุณาตรวจสอบข้อมูลอีกครั้ง', 'error');
                                    }
                                } catch (e) {
                                    Swal.fire('ผิดพลาด', 'กรุณาตรวจสอบข้อมูลอีกครั้ง', 'error');
                                } finally {
                                    hideLoading();
                                }
                            }
                        }" @submit-address.window="submitAddressForm($event.detail.event, $event.detail.mode, $event.detail.id)" x-init="init()" class="space-y-4">

                            <template x-if="addresses.length > 0">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="(address, index) in addresses" :key="address.id">
                                        <div class="relative border-2 rounded-2xl p-4 sm:p-5 transition-all duration-200 cursor-pointer overflow-hidden group flex flex-col h-full"
                                            :class="activeAddressId === address.id ? 'border-red-500 bg-red-50/50 shadow-md ring-1 ring-red-100' : 'border-gray-100 hover:border-red-200 hover:bg-gray-50'"
                                            @click="selectAddress(address.id)">
                                            
                                            <div class="absolute top-4 sm:top-5 right-4 sm:right-5">
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                                    :class="activeAddressId === address.id ? 'border-red-500' : 'border-gray-300'">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500 transition-transform duration-200"
                                                        :class="activeAddressId === address.id ? 'scale-100' : 'scale-0'"></div>
                                                </div>
                                            </div>

                                            <div class="pr-8 flex-grow">
                                                <div class="flex items-center gap-2 mb-2 sm:mb-3">
                                                    <h3 class="font-bold text-gray-900 text-sm sm:text-base" x-text="address.fullname"></h3>
                                                    <span x-show="index === 0" class="text-[9px] bg-gray-800 text-white px-2 py-0.5 rounded uppercase font-bold tracking-wider">ล่าสุด</span>
                                                </div>

                                                <div class="text-xs sm:text-sm text-gray-600 space-y-1.5 leading-relaxed">
                                                    <p class="flex items-start gap-2"><i class="fas fa-phone-alt mt-1 w-3 sm:w-4 text-gray-400"></i> <span x-text="address.phone"></span></p>
                                                    <p class="flex items-start gap-2"><i class="fas fa-map-pin mt-1 w-3 sm:w-4 text-gray-400"></i> 
                                                        <span>
                                                            <span x-text="address.address_line1"></span> <span x-text="address.address_line2 || ''"></span><br>
                                                            <span x-text="address.district.name_th"></span>, <span x-text="address.amphure.name_th"></span><br>
                                                            <span x-text="address.province.name_th"></span> <span x-text="address.zipcode"></span>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex justify-end gap-2 transition-opacity opacity-100 lg:opacity-0 lg:group-hover:opacity-100" :class="activeAddressId === address.id ? 'lg:opacity-100' : ''">
                                                <button type="button" @click.stop="document.getElementById(`modal_edit_${address.id}`).showModal()"
                                                    class="w-8 h-8 rounded-full bg-white shadow border border-gray-200 text-gray-600 hover:text-blue-600 flex items-center justify-center transition-colors" title="แก้ไข">
                                                    <i class="fas fa-pen text-xs"></i>
                                                </button>
                                                <button type="button" @click.stop="deleteAddress(address.id)"
                                                    class="w-8 h-8 rounded-full bg-white shadow border border-gray-200 text-gray-600 hover:text-red-600 flex items-center justify-center transition-colors" title="ลบ">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>

                                            {{-- Modal Edit Address --}}
                                            <dialog :id="`modal_edit_${address.id}`" class="modal modal-bottom sm:modal-middle" x-data="addressDropdown()" x-init="loadEditData(address.province_id, address.amphure_id, address.district_id)" @click.stop>
                                                <div class="modal-box w-full sm:w-11/12 max-w-4xl p-0 bg-white sm:rounded-3xl shadow-2xl overflow-hidden cursor-default">
                                                    <div class="px-5 py-4 sm:px-8 sm:py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                                        <h3 class="font-bold text-lg sm:text-xl text-gray-900 flex items-center gap-2"><i class="fas fa-edit text-red-500"></i> แก้ไขที่อยู่จัดส่ง</h3>
                                                        <button @click="document.getElementById(`modal_edit_${address.id}`).close()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 hover:bg-gray-300 transition-colors"><i class="fas fa-times"></i></button>
                                                    </div>
                                                    <div class="p-5 sm:p-8 max-h-[80vh] overflow-y-auto text-left">
                                                        <form @submit.prevent="window.dispatchEvent(new CustomEvent('submit-address', { detail: { event: $event, mode: 'edit', id: address.id } }))">
                                                            <div class="mb-6 sm:mb-8">
                                                                <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4 border-b pb-2">ข้อมูลผู้รับ</h4>
                                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">ชื่อ-นามสกุล <span class="text-red-500">*</span></label><input type="text" name="fullname" :value="address.fullname" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label><input type="tel" name="phone" :value="address.phone" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-4">
                                                                <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4 border-b pb-2">ที่อยู่จัดส่ง</h4>
                                                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                                                    <div class="sm:col-span-3 form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">บ้านเลขที่ / อาคาร / ถนน <span class="text-red-500">*</span></label><input type="text" name="address_line1" :value="address.address_line1" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                                                                    <div class="sm:col-span-1 form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">หมู่ที่</label><input type="text" name="address_line2" :value="address.address_line2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" /></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">จังหวัด <span class="text-red-500">*</span></label><select name="province_id" x-model="selectedProvince" @change="fetchAmphures()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required><option value="">-- เลือกจังหวัด --</option>@foreach ($provinces as $province)<option value="{{ $province->id }}">{{ $province->name_th }}</option>@endforeach</select></div>
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">อำเภอ/เขต <span class="text-red-500">*</span></label><select name="amphure_id" x-model="selectedAmphure" @change="fetchDistricts()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required><option value="">-- เลือกอำเภอ --</option><template x-for="amphure in amphures" :key="amphure.id"><option :value="amphure.id" x-text="amphure.name_th" :selected="amphure.id == selectedAmphure"></option></template></select></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">ตำบล/แขวง <span class="text-red-500">*</span></label><select name="district_id" x-model="selectedDistrict" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required><option value="">-- เลือกตำบล --</option><template x-for="district in districts" :key="district.id"><option :value="district.id" x-text="district.name_th" :selected="district.id == selectedDistrict"></option></template></select></div>
                                                                    <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">รหัสไปรษณีย์ <span class="text-red-500">*</span></label><input type="text" name="zipcode" :value="getZipCode()" readonly class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-600 font-bold focus:outline-none" required/></div>
                                                                </div>
                                                                <div class="form-control mt-4 sm:mt-6"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">หมายเหตุการจัดส่ง (ไม่บังคับ)</label><textarea name="note" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all h-20 sm:h-24" placeholder="เช่น ฝากไว้ที่ป้อมยาม..." :value="address.note"></textarea></div>
                                                            </div>
                                                            <div class="pt-6 sm:pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 mt-6">
                                                                <button type="button" @click="document.getElementById(`modal_edit_${address.id}`).close()" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors">ยกเลิก</button>
                                                                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all transform active:scale-95">บันทึกการแก้ไข</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </dialog>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="addresses.length === 0">
                                <div class="text-center py-10 sm:py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 px-4">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-gray-400">
                                        <i class="fas fa-home text-xl sm:text-2xl"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1 text-base sm:text-lg">ยังไม่มีข้อมูลที่อยู่จัดส่ง</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mb-6">กรุณาเพิ่มที่อยู่เพื่อทำการจัดส่งสินค้า</p>
                                    <button type="button" @click="document.getElementById('modal_add_new').showModal()" class="w-full sm:w-auto btn bg-red-600 hover:bg-red-700 text-white border-none rounded-full px-8 shadow-lg shadow-red-500/30">
                                        <i class="fas fa-plus mr-2"></i> เพิ่มที่อยู่จัดส่ง
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 📦 2. รายการสั่งซื้อ --}}
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 sm:p-6 md:p-8 border-b border-gray-100 flex items-center gap-2">
                            <i class="fas fa-box-open text-red-500 text-lg"></i>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-800">สินค้าที่สั่งซื้อ</h2>
                        </div>
                        
                        <div class="p-4 sm:p-6 md:px-8 space-y-4">
                            @if (isset($cartItems) && count($cartItems) > 0)
                                @foreach ($cartItems as $item)
                                    @php
                                        $attrs = $item->attributes;
                                        $originalPrice = $attrs->get('original_price') ?? $item->price;
                                        $totalPrice = $item->price * $item->quantity;
                                        $imgUrl = $attrs->get('image');
                                        $displayImage = (strpos($imgUrl ?? '', 'http') === 0) ? $imgUrl : asset($imgUrl ?: 'https://via.placeholder.com/150?text=No+Image');
                                    @endphp

                                    <div class="flex flex-row gap-3 sm:gap-4 p-3 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-xl border border-gray-200 flex-shrink-0 overflow-hidden">
                                            <img src="{{ $displayImage }}" class="w-full h-full object-cover" alt="{{ $item->name }}" onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=Error';" />
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div class="flex flex-col sm:flex-row justify-between items-start gap-1 sm:gap-4">
                                                <p class="font-bold text-gray-900 text-sm sm:text-base leading-tight line-clamp-2">{{ $item->name }}</p>
                                                <div class="text-left sm:text-right flex-shrink-0 mt-1 sm:mt-0">
                                                    <p class="font-black text-gray-900 text-sm sm:text-base">฿{{ number_format($totalPrice, 2) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex justify-between items-end mt-2">
                                                <p class="text-xs sm:text-sm font-bold text-gray-500 bg-white px-2 sm:px-3 py-1 rounded-lg border border-gray-200 shadow-sm">x{{ $item->quantity }}</p>
                                                @if ($originalPrice > $item->price)
                                                    <p class="text-[10px] sm:text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded font-medium border border-red-100">ประหยัด ฿{{ number_format(($originalPrice - $item->price) * $item->quantity, 2) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            {{-- ของแถม --}}
                            @if (isset($selectedFreebies) && is_array($selectedFreebies) && count($selectedFreebies) > 0)
                                <div class="mt-6">
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2"><i class="fas fa-gift text-pink-500"></i> ของแถมที่ได้รับ</h4>
                                    <div class="space-y-3">
                                        @foreach ($selectedFreebies as $freeId)
                                            @php
                                                $freeProduct = isset($products) ? $products->get($freeId) : \App\Models\ProductSalepage::find($freeId);
                                                if (!$freeProduct) continue;
                                                $imgUrl = $freeProduct->cover_image_url;
                                                $displayImage = (strpos($imgUrl ?? '', 'http') === 0) ? $imgUrl : asset($imgUrl ?: 'https://via.placeholder.com/150');
                                            @endphp
                                            <div class="flex gap-3 sm:gap-4 p-3 bg-gradient-to-r from-pink-50 to-red-50 rounded-2xl border border-pink-100 relative overflow-hidden">
                                                <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-white/40 to-transparent pointer-events-none"></div>
                                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-xl border border-pink-200 flex-shrink-0 overflow-hidden shadow-sm">
                                                    <img src="{{ $displayImage }}" class="w-full h-full object-cover" alt="{{ $freeProduct->pd_sp_name }}" onerror="this.src='https://via.placeholder.com/150?text=Gift';" />
                                                </div>
                                                <div class="flex-1 flex flex-col justify-center">
                                                    <p class="font-bold text-gray-800 text-xs sm:text-sm line-clamp-2 sm:line-clamp-1">{{ $freeProduct->pd_sp_name }}</p>
                                                    <div class="flex items-center gap-2 mt-1 sm:mt-1.5">
                                                        <span class="text-[10px] sm:text-xs font-black text-pink-600 uppercase tracking-wide bg-white px-2 py-0.5 rounded border border-pink-100 shadow-sm">ฟรี!</span>
                                                        <span class="text-[10px] sm:text-xs text-gray-500 font-bold">x1</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 💳 3. วิธีชำระเงิน --}}
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-5 sm:p-6 md:p-8">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
                            <i class="fas fa-wallet text-red-500"></i> วิธีชำระเงิน
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                            <label class="cursor-pointer">
                                <div class="relative p-4 sm:p-5 border-2 rounded-2xl border-red-500 bg-red-50/30 flex items-center gap-3 sm:gap-4 transition-all">
                                    <input type="radio" name="payment_method_display" checked class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 focus:ring-red-500 shrink-0" />
                                    <div class="bg-white p-1.5 sm:p-2 rounded-lg border border-gray-200 shadow-sm shrink-0">
                                        <img src="{{ asset('images/ci-qrpayment-img-01.png') }}" alt="PromptPay" class="h-6 sm:h-8 object-contain" onerror="this.style.display='none'">
                                    </div>
                                    <span class="font-bold text-gray-900 text-sm sm:text-base">พร้อมเพย์ (QR Code)</span>
                                    <div class="absolute top-0 right-0 px-2 py-1 bg-red-500 text-white text-[9px] sm:text-[10px] font-bold rounded-bl-lg rounded-tr-lg">แนะนำ</div>
                                </div>
                            </label>
                            
                            {{-- ตัวอย่างวิธีชำระเงินอื่นๆ ทำเป็นสีเทาไว้ (สามารถเปิดใช้ทีหลังได้) --}}
                            <label class="cursor-pointer opacity-50">
                                <div class="p-4 sm:p-5 border-2 rounded-2xl border-gray-200 bg-gray-50 flex items-center gap-3 sm:gap-4">
                                    <input type="radio" name="payment_method_display" disabled class="w-4 h-4 sm:w-5 sm:h-5 shrink-0" />
                                    <div class="w-10 h-8 sm:w-12 sm:h-10 bg-white rounded-lg border border-gray-300 flex items-center justify-center text-gray-400 shrink-0">
                                        <i class="fas fa-truck text-lg sm:text-xl"></i>
                                    </div>
                                    <span class="font-bold text-gray-600 text-sm sm:text-base">เก็บเงินปลายทาง (COD)</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 🧾 ฝั่งขวา: สรุปยอดชำระเงิน (Sticky) --}}
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 p-5 sm:p-6 lg:p-8 sticky top-4 sm:top-8 mb-6 lg:mb-0">
                        <h3 class="font-extrabold text-lg sm:text-xl text-gray-900 mb-5 sm:mb-6 flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-red-500"></i> สรุปคำสั่งซื้อ
                        </h3>

                        {{-- 🎫 ช่องรหัสส่วนลด --}}
                        <div class="mb-5 sm:mb-6 p-3 sm:p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <label class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">มีรหัสส่วนลดไหม?</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <input type="text" x-model="discountCode" :readonly="appliedCode !== ''" placeholder="กรอกรหัสที่นี่" 
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 uppercase font-bold text-gray-800 placeholder-gray-400 transition-all disabled:bg-gray-100"
                                    @keyup.enter="applyPromoCode()" />

                                <button x-show="appliedCode === ''" type="button" @click="applyPromoCode()" :disabled="!discountCode || isApplyingDiscount" 
                                    class="w-full sm:w-auto shrink-0 bg-gray-800 hover:bg-gray-900 text-white px-5 py-2.5 sm:py-0 rounded-xl font-bold text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!isApplyingDiscount">ใช้โค้ด</span>
                                    <span x-show="isApplyingDiscount" class="loading loading-spinner loading-sm"></span>
                                </button>

                                <button x-show="appliedCode !== ''" type="button" @click="removePromoCode()" :disabled="isApplyingDiscount" 
                                    class="w-full sm:w-auto shrink-0 bg-red-100 hover:bg-red-200 text-red-600 px-5 py-2.5 sm:py-0 rounded-xl font-bold text-sm transition-colors">
                                    <span x-show="!isApplyingDiscount">ลบออก</span>
                                    <span x-show="isApplyingDiscount" class="loading loading-spinner loading-sm"></span>
                                </button>
                            </div>
                            
                            <p x-show="appliedCode !== ''" x-transition
                                class="text-emerald-600 bg-emerald-50 border-emerald-200 text-[11px] sm:text-xs font-bold mt-2 sm:mt-3 p-2 rounded-lg border flex items-center gap-1.5">
                                <i class="fas fa-check-circle"></i> ใช้คูปองสำเร็จ: <span x-text="appliedPromoName || appliedCode"></span>
                            </p>

                            {{-- ✅ เพิ่ม Mockup Coupons ตรงนี้ --}}
                            <div class="mt-4 pt-4 border-t border-gray-200" x-show="appliedCode === ''">
                                <p class="text-[11px] font-bold text-gray-500 mb-2">คูปองแนะนำ (คลิกเพื่อใช้)</p>
                                <div class="flex gap-2 overflow-x-auto pb-2 -mx-1 px-1 hide-scrollbar">
                                    <template x-for="coupon in availableCoupons" :key="coupon.code">
                                        <button type="button" @click="applyPromoCode(coupon.code, coupon.name)"
                                            :disabled="isApplyingDiscount"
                                            class="shrink-0 flex items-center gap-3 border border-red-200 bg-red-50/50 hover:bg-red-100 px-3 py-2 rounded-xl transition-all disabled:opacity-50 text-left active:scale-95 shadow-sm">
                                            <div class="w-10 h-10 rounded-lg bg-red-500 flex items-center justify-center text-white shrink-0">
                                                <i class="fas fa-ticket-alt text-lg"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-red-700 uppercase" x-text="coupon.name"></div>
                                                <div class="text-[9px] font-medium text-red-500 mt-0.5">คลิกเพื่อรับส่วนลด</div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- สรุปตัวเลข --}}
                        <div class="space-y-3 sm:space-y-4 text-sm mb-5 sm:mb-6">
                            <div class="flex justify-between items-center text-gray-600 text-xs sm:text-sm">
                                <span>ราคาปกติ ({{ count($cartItems) }} ชิ้น)</span>
                                <span class="font-medium" x-text="'฿' + formatNumber(totalOriginalAmount)"></span>
                            </div>
                            <div class="flex justify-between items-center text-gray-600 text-xs sm:text-sm">
                                <span>ราคาสินค้า</span>
                                <span class="font-bold text-gray-900" x-text="'฿' + formatNumber(grandTotal)"></span>
                            </div>
                            <div class="flex justify-between items-center text-gray-600 text-xs sm:text-sm">
                                <span>ค่าจัดส่ง</span>
                                <span class="font-bold text-emerald-600" x-show="shippingCost == 0">ส่งฟรี!</span>
                                <span class="font-medium" x-show="shippingCost > 0" x-text="'฿' + formatNumber(shippingCost)"></span>
                            </div>
                            <div class="flex justify-between items-center text-red-500 font-bold bg-red-50 p-2 rounded-lg text-xs sm:text-sm" x-show="totalDiscount > 0" x-transition>
                                <span>
                                    <i class="fas fa-tags mr-1"></i> ส่วนลดรวม
                                    <span class="ml-1 text-[10px] bg-red-500 text-white px-1.5 py-0.5 rounded-full" 
                                          x-show="totalOriginalAmount > 0"
                                          x-text="'-' + Math.round((totalDiscount / totalOriginalAmount) * 100) + '%'">
                                    </span>
                                </span>
                                <span x-text="'-฿' + formatNumber(totalDiscount)"></span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-300 pt-4 mb-5 sm:mb-6">
                            <div class="flex justify-between items-end">
                                <span class="font-bold text-gray-800 text-base sm:text-lg">ยอดที่ต้องชำระ</span>
                                <div class="text-right">
                                    <span class="font-black text-2xl sm:text-3xl text-red-600 tracking-tight block leading-none drop-shadow-sm" x-text="'฿' + formatNumber(finalTotal)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- 🛠️ Form ส่งข้อมูล (ผูกกับ Logic เดิมที่ถูกต้อง) --}}
                        <form action="{{ route('payment.process') }}" method="POST" @submit.prevent="submitCheckoutForm($event)">
                            @csrf
                            @foreach ($selectedItems as $id) <input type="hidden" name="selected_items[]" value="{{ $id }}"> @endforeach
                            @foreach ($selectedFreebies as $id) <input type="hidden" name="selected_freebies[]" value="{{ $id }}"> @endforeach
                            <input type="hidden" name="delivery_address_id" :value="activeAddressId">
                            
                            <button type="submit" class="w-full h-12 sm:h-14 bg-red-600 hover:bg-red-700 text-white rounded-xl sm:rounded-2xl font-bold text-base sm:text-lg shadow-lg shadow-red-600/30 transition-all transform active:scale-95 flex items-center justify-center gap-2 group">
                                <i class="fas fa-lock opacity-70"></i> 
                                <span>ชำระเงินอย่างปลอดภัย</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Add New Address --}}
        <dialog id="modal_add_new" class="modal modal-bottom sm:modal-middle" x-data="addressDropdown()">
            <div class="modal-box w-full sm:w-11/12 max-w-4xl p-0 bg-white sm:rounded-3xl shadow-2xl overflow-hidden cursor-default">
                <div class="px-5 py-4 sm:px-8 sm:py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-lg sm:text-xl text-gray-900 flex items-center gap-2"><i class="fas fa-map-marker-alt text-red-500"></i> เพิ่มที่อยู่จัดส่งใหม่</h3>
                    <button type="button" @click="document.getElementById('modal_add_new').close()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 hover:bg-gray-300 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 sm:p-8 max-h-[80vh] overflow-y-auto">
                    <form @submit.prevent="window.dispatchEvent(new CustomEvent('submit-address', { detail: { event: $event, mode: 'add' } }))" id="form_add_new">
                        <div class="mb-6 sm:mb-8">
                            <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4 border-b pb-2">ข้อมูลผู้รับ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">ชื่อ-นามสกุล <span class="text-red-500">*</span></label><input type="text" name="fullname" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label><input type="tel" name="phone" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-xs sm:text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 sm:mb-4 border-b pb-2">ที่อยู่จัดส่ง</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                <div class="sm:col-span-3 form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">บ้านเลขที่ / อาคาร / ถนน <span class="text-red-500">*</span></label><input type="text" name="address_line1" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required/></div>
                                <div class="sm:col-span-1 form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">หมู่ที่</label><input type="text" name="address_line2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" /></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">จังหวัด <span class="text-red-500">*</span></label><select name="province_id" x-model="selectedProvince" @change="fetchAmphures()" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all" required><option value="">-- เลือกจังหวัด --</option>@foreach ($provinces as $province)<option value="{{ $province->id }}">{{ $province->name_th }}</option>@endforeach</select></div>
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">อำเภอ/เขต <span class="text-red-500">*</span></label><select name="amphure_id" x-model="selectedAmphure" @change="fetchDistricts()" :disabled="!selectedProvince" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all disabled:opacity-50" required><option value="">-- เลือกอำเภอ --</option><template x-for="amphure in amphures" :key="amphure.id"><option :value="amphure.id" x-text="amphure.name_th"></option></template></select></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-4 sm:mb-5">
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">ตำบล/แขวง <span class="text-red-500">*</span></label><select name="district_id" x-model="selectedDistrict" :disabled="!selectedAmphure" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all disabled:opacity-50" required><option value="">-- เลือกตำบล --</option><template x-for="district in districts" :key="district.id"><option :value="district.id" x-text="district.name_th"></option></template></select></div>
                                <div class="form-control"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">รหัสไปรษณีย์ <span class="text-red-500">*</span></label><input type="text" name="zipcode" :value="getZipCode()" readonly class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-600 font-bold focus:outline-none" required/></div>
                            </div>
                            <div class="form-control mt-4 sm:mt-6"><label class="text-xs sm:text-sm font-bold text-gray-700 mb-2">หมายเหตุการจัดส่ง (ไม่บังคับ)</label><textarea name="note" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all h-20 sm:h-24" placeholder="เช่น ฝากไว้ที่ป้อมยาม..."></textarea></div>
                        </div>
                        <div class="pt-6 sm:pt-8 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 mt-6">
                            <button type="button" @click="document.getElementById('modal_add_new').close()" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors">ยกเลิก</button>
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/30 transition-all transform active:scale-95">บันทึกที่อยู่</button>
                        </div>
                    </form>
                </div>
            </div>
        </dialog>

        {{-- Loading Overlay แบบหน้าตาใหม่ --}}
        <div id="loading-overlay" class="fixed inset-0 z-[9999] bg-white/80 backdrop-blur-sm flex items-center justify-center hidden">
            <div class="bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col items-center gap-4 sm:gap-6 border border-gray-100 mx-4">
                <span class="loading loading-spinner loading-lg text-red-600 scale-125 sm:scale-150"></span>
                <p class="text-gray-800 font-extrabold text-base sm:text-lg tracking-wide text-center">กำลังประมวลผลคำสั่งซื้อ...</p>
            </div>
        </div>

    </div>

    {{-- Script ที่สมบูรณ์แล้วของเดิมร้อยเปอร์เซ็นต์ --}}
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    
    <script>
        function showLoading() { document.getElementById('loading-overlay').classList.remove('hidden'); }
        function hideLoading() { document.getElementById('loading-overlay').classList.add('hidden'); }

        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentSummaryPage', (config) => ({
                activeAddressId: null,
                activeAddressIsBkk: false,
                totalOriginalAmount: config.initialTotalOriginalAmount,
                grandTotal: config.initialGrandTotal,
                shippingCost: config.initialShippingCost,
                totalDiscount: config.initialTotalDiscount,
                finalTotal: config.initialFinalTotal,
                itemCount: config.itemCount,
                discountCode: config.initialDiscountCode || '',
                appliedCode: config.initialDiscountCode || '',
                isApplyingDiscount: false,
                selectedItems: config.selectedItems || [],
                selectedFreebies: config.selectedFreebies || [],
                availableCoupons: config.availableCoupons || [],

                // ฟังก์ชันจัดการอัปเดต Address (ให้ scope เข้าใจตรงกัน)
                setActiveAddress(id) {
                    this.activeAddressId = id;
                    localStorage.setItem('selected_address_id', id);
                    this.fetchShippingCost(id);
                },

                // ดักจับการกดยืนยัน ถ้าไม่เลือกที่อยู่ให้แจ้งเตือน
                submitCheckoutForm(e) {
                    if (!this.activeAddressId) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'กรุณาเพิ่มและเลือกที่อยู่จัดส่งก่อนดำเนินการชำระเงิน',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#ef4444',
                            borderRadius: '1rem'
                        });
                        return;
                    }
                    showLoading();
                    e.target.submit(); // สั่งให้แบบฟอร์มทำงานต่อ
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('th-TH', { minimumFractionDigits: 2 }).format(value);
                },

                async fetchShippingCost(addressId) {
                    if (!addressId) return;
                    this.activeAddressId = addressId;
                    
                    try {
                        const addrResponse = await fetch(`/api/address-info/${addressId}`, { headers: { 'Accept': 'application/json' } });
                        if (addrResponse.ok) {
                            const addrData = await addrResponse.json();
                            this.activeAddressIsBkk = addrData.isBkk;
                        }

                        const response = await fetch('{{ route('payment.calculateShipping') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                address_id: addressId,
                                subtotal: this.grandTotal,
                                item_count: this.itemCount
                            }),
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.shippingCost = parseFloat(data.shippingCost);
                            this.recalculateFinalTotal();
                        }
                    } catch (error) { console.error(error); }
                },

                async applyPromoCode(providedCode = null, providedName = null) {
                    const codeToApply = providedCode || this.discountCode;
                    if (!codeToApply) return;
                    this.isApplyingDiscount = true;
                    
                    try {
                        const response = await fetch('{{ route('payment.applyDiscount') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                code: codeToApply,
                                selected_items: this.selectedItems,
                                selected_freebies: this.selectedFreebies,
                                address_id: this.activeAddressId
                            }),
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            this.appliedCode = codeToApply;
                            this.appliedPromoName = providedName || data.promoName || codeToApply;
                            
                            if (!providedCode) {
                                this.discountCode = '';
                            }

                            this.totalOriginalAmount = data.totalOriginalAmount;
                            this.grandTotal = data.grandTotal;
                            this.shippingCost = data.shippingCost;
                            this.totalDiscount = data.totalDiscount;
                            this.finalTotal = data.finalTotal;
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'ใช้ส่วนลดสำเร็จ',
                                text: this.appliedPromoName,
                                timer: 1500,
                                showConfirmButton: false,
                                borderRadius: '1rem'
                            });
                        } else {
                            Swal.fire('ผิดพลาด', data.message, 'error');
                        }
                    } catch (error) {
                        console.error(error);
                        Swal.fire('ผิดพลาด', 'ไม่สามารถใช้รหัสส่วนลดได้', 'error');
                    } finally {
                        this.isApplyingDiscount = false;
                    }
                },

                async removePromoCode() {
                    this.isApplyingDiscount = true;
                    try {
                        const response = await fetch('{{ route('payment.applyDiscount') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                code: '',
                                selected_items: this.selectedItems,
                                selected_freebies: this.selectedFreebies,
                                address_id: this.activeAddressId
                            }),
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.appliedCode = '';
                            this.appliedPromoName = '';
                            this.discountCode = '';
                            this.totalOriginalAmount = data.totalOriginalAmount;
                            this.grandTotal = data.grandTotal;
                            this.shippingCost = data.shippingCost;
                            this.totalDiscount = data.totalDiscount;
                            this.finalTotal = data.finalTotal;
                        }
                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.isApplyingDiscount = false;
                    }
                },

                recalculateFinalTotal() {
                    this.finalTotal = this.grandTotal + this.shippingCost;
                }
            }));
        });

        function addressDropdown() {
            return {
                selectedProvince: '',
                selectedAmphure: '',
                selectedDistrict: '',
                amphures: [],
                districts: [],
                loadEditData(provinceId, amphureId, districtId) {
                    this.selectedProvince = provinceId;
                    fetch(`/api/amphures/${provinceId}`).then(r => r.json()).then(d => {
                        this.amphures = Array.isArray(d) ? d : (d.data || []);
                        this.selectedAmphure = amphureId;
                        if (amphureId) {
                            fetch(`/api/districts/${amphureId}`).then(r => r.json()).then(d => {
                                this.districts = Array.isArray(d) ? d : (d.data || []);
                                this.selectedDistrict = districtId;
                            });
                        }
                    });
                },
                fetchAmphures() {
                    this.selectedAmphure = '';
                    this.selectedDistrict = '';
                    this.amphures = [];
                    this.districts = [];
                    if (this.selectedProvince) {
                        fetch(`/api/amphures/${this.selectedProvince}`).then(r => r.json()).then(d => {
                            this.amphures = Array.isArray(d) ? d : (d.data || []);
                        });
                    }
                },
                fetchDistricts() {
                    this.selectedDistrict = '';
                    this.districts = [];
                    if (this.selectedAmphure) {
                        fetch(`/api/districts/${this.selectedAmphure}`).then(r => r.json()).then(d => {
                            this.districts = Array.isArray(d) ? d : (d.data || []);
                        });
                    }
                },
                getZipCode() {
                    if (!this.selectedDistrict || this.districts.length === 0) return '';
                    const district = this.districts.find(d => d.id == this.selectedDistrict);
                    return district ? (district.zip_code || district.zipcode) : '';
                }
            }
        }
    </script>
@endsection