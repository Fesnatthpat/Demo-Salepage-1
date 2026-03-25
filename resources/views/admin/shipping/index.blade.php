@extends('layouts.admin')

@section('title', 'ตั้งค่าจัดการค่าจัดส่ง')
@section('page-title', 'ตั้งค่าจัดการค่าจัดส่ง')

@section('content')
    <div class="container mx-auto pb-20" x-data="shippingManager()">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-100 flex items-center tracking-tight">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center mr-4 shadow-lg border border-indigo-500/30">
                        <i class="fas fa-truck text-indigo-400 text-xl"></i>
                    </div>
                    ตั้งค่าจัดการค่าจัดส่ง
                </h1>
                <p class="text-gray-400 mt-2 ml-16">จัดการเงื่อนไขการคิดค่าจัดส่งและบริษัทขนส่ง</p>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="saveGlobalSettings()" :disabled="isSavingGlobal"
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20 active:scale-95 disabled:opacity-50">
                    <i class="fas fa-save" x-show="!isSavingGlobal"></i>
                    <span class="loading loading-spinner loading-xs" x-show="isSavingGlobal"></span>
                    บันทึกการตั้งค่าหลัก
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Global Settings --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl sticky top-8">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">การตั้งค่าการคิดค่าส่ง</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-widest">โหมดการคิดค่าส่ง</label>
                            <select x-model="globalData.shipping_mode" 
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all font-bold">
                                <option value="global">ใช้ค่าคงที่ Global (เดิม)</option>
                                <option value="methods">ใช้ตามบริษัทขนส่ง (ยืดหยุ่น)</option>
                            </select>
                        </div>

                        <div x-show="globalData.shipping_mode === 'global'" x-transition>
                            <div class="space-y-4 pt-4 border-t border-gray-700">
                                <p class="text-xs text-amber-400 font-bold mb-4">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> โหมด Global จะใช้ค่าด้านล่างนี้กับทุกออเดอร์
                                </p>
                                <div>
                                    <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-widest">ส่งฟรีเมื่อซื้อครบ (฿)</label>
                                    <input type="number" x-model="globalData.free_shipping_threshold" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all font-mono text-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-widest">ค่าส่ง กทม. (฿)</label>
                                    <input type="number" x-model="globalData.bkk_flat_rate" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all font-mono text-lg">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-400 mb-2 uppercase tracking-widest">ค่าส่ง ตจว. (฿)</label>
                                    <input type="number" x-model="globalData.upc_flat_rate" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all font-mono text-lg">
                                </div>
                            </div>
                        </div>

                        <div x-show="globalData.shipping_mode === 'methods'" x-transition>
                            <div class="p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-2xl">
                                <p class="text-xs text-emerald-200/70 leading-relaxed">
                                    <i class="fas fa-check-circle mr-1 text-emerald-400"></i> โหมดยืดหยุ่น จะคำนวณตามบริษัทขนส่งที่คุณตั้งค่าไว้ในรายการด้านขวา 
                                    (หากมีหลายบริษัท ลูกค้าสามารถเลือกได้ในหน้าชำระเงิน)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Shipping Methods List --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">รายการบริษัทขนส่ง</h3>
                        </div>
                        <button type="button" @click="openAddModal()" 
                            class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center gap-1 transition-colors">
                            <i class="fas fa-plus-circle"></i> เพิ่มบริษัทใหม่
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-900/50 text-gray-400 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center w-20">หลัก</th>
                                    <th class="px-6 py-4 font-bold">ชื่อบริษัท / รายละเอียด</th>
                                    <th class="px-6 py-4 font-bold text-center">ค่าส่ง (BKK/UPC)</th>
                                    <th class="px-6 py-4 font-bold text-center">เพิ่มต่อชิ้น</th>
                                    <th class="px-6 py-4 font-bold text-center">ส่งฟรี</th>
                                    <th class="px-6 py-4 font-bold text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <template x-for="method in methods" :key="method.id">
                                    <tr class="hover:bg-gray-700/30 transition-colors" :class="method.is_default ? 'bg-indigo-500/5' : ''">
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center">
                                                <i class="fas fa-star text-amber-400" x-show="method.is_default"></i>
                                                <button @click="setAsDefault(method)" x-show="!method.is_default" class="text-gray-600 hover:text-amber-400 transition-colors">
                                                    <i class="far fa-star"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center border border-gray-700" :class="method.is_active ? 'text-emerald-400' : 'text-gray-500'">
                                                    <i class="fas fa-shipping-fast"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-100 flex items-center gap-2">
                                                        <span x-text="method.name"></span>
                                                        <span x-show="!method.is_active" class="bg-gray-700 text-[9px] px-1.5 py-0.5 rounded uppercase font-black">ปิด</span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 uppercase tracking-tighter" x-text="method.code"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="font-mono font-bold text-emerald-400">฿<span x-text="method.bkk_rate"></span> / ฿<span x-text="method.upc_rate"></span></div>
                                            <div class="text-[10px] text-gray-500">กทม. / ตจว.</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono font-bold text-blue-400">
                                            +฿<span x-text="method.per_item_rate"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono">
                                            <div x-show="method.free_threshold" class="text-indigo-400 font-bold">฿<span x-text="method.free_threshold"></span></div>
                                            <div x-show="method.min_items_for_free_shipping" class="text-pink-400 text-xs"><span x-text="method.min_items_for_free_shipping"></span> ชิ้นขึ้นไป</div>
                                            <span x-show="!method.free_threshold && !method.min_items_for_free_shipping" class="text-gray-600 text-xs">-</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openEditModal(method)" 
                                                    class="p-2 bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white rounded-lg transition-all">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="deleteMethod(method.id)" 
                                                    class="p-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-lg transition-all">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="methods.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">ไม่พบข้อมูลบริษัทขนส่ง</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal for Add/Edit Shipping Method --}}
        <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-black/80 backdrop-blur-sm" @click="modalOpen = false"></div>

                <div class="inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-gray-800 border border-gray-700 shadow-2xl rounded-3xl z-10"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100">
                    
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-100" x-text="editMode ? 'แก้ไขบริษัทขนส่ง' : 'เพิ่มบริษัทขนส่งใหม่'"></h3>
                        <button @click="modalOpen = false" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form @submit.prevent="saveMethod()">
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">ชื่อบริษัท</label>
                                    <input type="text" x-model="formData.name" required class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">รหัสเรียก (Code)</label>
                                    <input type="text" x-model="formData.code" required class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">รายละเอียด (แสดงให้ลูกค้าเห็น)</label>
                                    <textarea x-model="formData.description" class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all" rows="2"></textarea>
                                </div>

                                <div class="p-4 bg-gray-900/50 rounded-2xl md:col-span-2 border border-gray-700">
                                    <h4 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-4">การคำนวณราคา</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">ค่าส่ง กทม. (฿)</label>
                                            <input type="number" x-model="formData.bkk_rate" required class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2 px-3 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">ค่าส่ง ตจว. (฿)</label>
                                            <input type="number" x-model="formData.upc_rate" required class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2 px-3 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest">บวกเพิ่มต่อชิ้น (฿)</label>
                                            <input type="number" x-model="formData.per_item_rate" required class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2 px-3 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                            <p class="text-[10px] text-gray-500 italic">บวกเพิ่มตั้งแต่ชิ้นที่ 2 เป็นต้นไป</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-indigo-500/5 rounded-2xl md:col-span-2 border border-indigo-500/10">
                                    <h4 class="text-xs font-black text-indigo-400 uppercase tracking-widest mb-4">เงื่อนไขส่งฟรี (เลือกอย่างใดอย่างหนึ่ง)</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">เมื่อยอดซื้อครบ (฿)</label>
                                            <input type="number" x-model="formData.free_threshold" class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2 px-3 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">เมื่อจำนวนสินค้าครบ (ชิ้น)</label>
                                            <input type="number" x-model="formData.min_items_for_free_shipping" class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2 px-3 text-gray-100 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4 md:col-span-2 flex flex-wrap gap-8">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest block mb-3">สถานะการใช้งาน</label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="formData.is_active" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-emerald-600 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-300" x-text="formData.is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน'"></span>
                                        </label>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest block mb-3">บริษัทเริ่มต้น (Default)</label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="formData.is_default" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-700 rounded-full peer peer-checked:bg-amber-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                            <span class="ms-3 text-sm font-medium text-gray-300" x-text="formData.is_default ? 'เป็นบริษัทหลัก' : 'บริษัททั่วไป'"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-6 bg-gray-900/50 border-t border-gray-700 flex justify-end gap-3">
                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 rounded-xl font-bold text-gray-400 hover:text-white transition-all">ยกเลิก</button>
                            <button type="submit" :disabled="isSavingMethod" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-indigo-900/30 disabled:opacity-50">
                                <span class="loading loading-spinner loading-xs" x-show="isSavingMethod"></span>
                                <span x-text="editMode ? 'อัปเดตข้อมูล' : 'บันทึกข้อมูล'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.shippingManager = function() {
            return {
                globalData: @json($globalSettings),
                methods: @json($methods),
                isSavingGlobal: false,
                isSavingMethod: false,
                modalOpen: false,
                editMode: false,
                formData: {
                    id: null,
                    name: '',
                    description: '',
                    code: '',
                    is_active: true,
                    is_default: false,
                    bkk_rate: 0,
                    upc_rate: 0,
                    per_item_rate: 0,
                    free_threshold: null,
                    min_items_for_free_shipping: null
                },

                async saveGlobalSettings() {
                    this.isSavingGlobal = true;
                    try {
                        const response = await fetch('{{ route('admin.shipping.updateGlobal') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.globalData)
                        });
                        const data = await response.json();
                        if (data.success) window.showNotification('success', 'สำเร็จ', data.message);
                    } catch (e) { console.error(e); }
                    this.isSavingGlobal = false;
                },

                openAddModal() {
                    this.editMode = false;
                    this.formData = { id: null, name: '', description: '', code: '', is_active: true, is_default: false, bkk_rate: 0, upc_rate: 0, per_item_rate: 0, free_threshold: null, min_items_for_free_shipping: null };
                    this.modalOpen = true;
                },

                openEditModal(method) {
                    this.editMode = true;
                    this.formData = { ...method };
                    this.modalOpen = true;
                },

                async saveMethod() {
                    this.isSavingMethod = true;
                    try {
                        const response = await fetch('{{ route('admin.shipping.methods.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.formData)
                        });
                        const data = await response.json();
                        if (data.success) {
                            if (this.editMode || data.method.id) {
                                // Refresh methods to ensure is_default is correct across all items
                                location.reload();
                            } else {
                                this.methods.unshift(data.method);
                                this.modalOpen = false;
                                window.showNotification('success', 'สำเร็จ', data.message);
                            }
                        }
                    } catch (e) { console.error(e); }
                    this.isSavingMethod = false;
                },

                async setAsDefault(method) {
                    try {
                        const response = await fetch('{{ route('admin.shipping.methods.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ ...method, is_default: true })
                        });
                        const data = await response.json();
                        if (data.success) location.reload();
                    } catch (e) { console.error(e); }
                },

                async deleteMethod(id) {
                    const result = await Swal.fire({
                        title: 'ยืนยันการลบ?',
                        text: "ข้อมูลนี้จะถูกลบถาวร",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'ลบเลย',
                        cancelButtonText: 'ยกเลิก'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/shipping-settings/methods/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.methods = this.methods.filter(m => m.id !== id);
                                window.showNotification('success', 'สำเร็จ', data.message);
                            }
                        } catch (e) { console.error(e); }
                    }
                }
            }
        }
    </script>
@endpush
