@extends('layouts.admin')

@section('title', 'ตั้งค่าจัดการค่าจัดส่ง')
@section('page-title', 'ตั้งค่าจัดการค่าจัดส่ง')

@section('content')
    <div class="container mx-auto pb-20" x-data="shippingSettings()">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-100 flex items-center tracking-tight">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center mr-4 shadow-lg border border-indigo-500/30">
                        <i class="fas fa-truck text-indigo-400 text-xl"></i>
                    </div>
                    ตั้งค่าจัดการค่าจัดส่ง
                </h1>
                <p class="text-gray-400 mt-2 ml-16">จัดการช่องทางการจัดส่งและคำนวณราคาค่าขนส่ง</p>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="saveSettings()" 
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-900/20 active:scale-95">
                    <i class="fas fa-save"></i>
                    บันทึกการตั้งค่า
                </button>
            </div>
        </div>

        {{-- Main Settings Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Left Column: Shipping Methods List --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                                <i class="fas fa-list-ul"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">ช่องทางการจัดส่งทั้งหมด</h3>
                        </div>
                        <button type="button" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> เพิ่มช่องทางใหม่
                        </button>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-900/50 text-gray-400 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center w-20">เปิดใช้งาน</th>
                                    <th class="px-6 py-4 font-bold">ชื่อบริษัทขนส่ง</th>
                                    <th class="px-6 py-4 font-bold">รูปแบบการคิดเงิน</th>
                                    <th class="px-6 py-4 font-bold">ค่าส่งเริ่มต้น</th>
                                    <th class="px-6 py-4 font-bold">ส่งฟรีเมื่อซื้อครบ</th>
                                    <th class="px-6 py-4 font-bold text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <template x-for="method in methods" :key="method.id">
                                    <tr class="hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 text-center">
                                            <button @click="method.is_active = !method.is_active" 
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                :class="method.is_active ? 'bg-emerald-600' : 'bg-gray-600'">
                                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                    :class="method.is_active ? 'translate-x-6' : 'translate-x-1'"></span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center border border-gray-700">
                                                    <i class="fas fa-shipping-fast text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-100" x-text="method.name"></div>
                                                    <div class="text-xs text-gray-500 uppercase tracking-tighter" x-text="method.code"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                                :class="{
                                                    'bg-blue-500/10 text-blue-400 border border-blue-500/20': method.type === 'flat',
                                                    'bg-purple-500/10 text-purple-400 border border-purple-500/20': method.type === 'weight',
                                                    'bg-amber-500/10 text-amber-400 border border-amber-500/20': method.type === 'distance'
                                                }"
                                                x-text="method.type === 'flat' ? 'เหมาจ่าย' : (method.type === 'weight' ? 'ตามน้ำหนัก' : 'ตามระยะทาง')">
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-mono font-bold text-emerald-400">
                                            ฿<span x-text="method.base_rate"></span>
                                        </td>
                                        <td class="px-6 py-4 font-mono">
                                            <span x-show="method.free_shipping_threshold">฿<span x-text="method.free_shipping_threshold"></span></span>
                                            <span x-show="!method.free_shipping_threshold" class="text-gray-500 italic text-sm">ไม่มีส่งฟรี</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button class="p-2 bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white rounded-lg transition-all">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="p-2 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-lg transition-all">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Global Settings Card --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">การตั้งค่าทั่วโลก (Global Settings)</h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">จัดส่งฟรีทั่วประเทศ (โปรโมชั่นหลัก)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-bold">฿</span>
                                    </div>
                                    <input type="number" placeholder="เช่น 999" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-gray-100 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition-all font-mono">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">หากยอดซื้อถึงยอดนี้ จะส่งฟรีทันทีทุกช่องทาง</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-400 mb-2 uppercase tracking-wider">ภาษีค่าขนส่ง (%)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-percent text-gray-500"></i>
                                    </div>
                                    <input type="number" placeholder="เช่น 7" 
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-gray-100 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition-all font-mono">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-2xl">
                            <div class="p-2 bg-emerald-500/20 rounded-full text-emerald-400">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="text-sm text-emerald-200/80">การตั้งค่าส่วนนี้จะมีผลเหนือกว่าการตั้งค่ารายบริษัทขนส่ง หากเปิดใช้งานโปรโมชั่นส่งฟรีส่วนกลาง</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Flexible Calculation Rules (Mockup) --}}
            <div class="space-y-6">
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl sticky top-6">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2 bg-amber-500/10 rounded-lg text-amber-400">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">กฎการคำนวณที่ยืดหยุ่น</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-900/50 rounded-2xl border border-gray-700 group hover:border-amber-500/50 transition-all cursor-pointer">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-amber-500 uppercase">ตามน้ำหนักสินค้า</span>
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-100 mb-1">Weight-based Rules</h4>
                                <p class="text-xs text-gray-500 leading-relaxed">0-1kg = 35฿, 1-3kg = 55฿, >3kg +20฿/kg</p>
                            </div>

                            <div class="p-4 bg-gray-900/50 rounded-2xl border border-gray-700 group hover:border-amber-500/50 transition-all cursor-pointer">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-blue-500 uppercase">แยกตามภูมิภาค</span>
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-100 mb-1">Regional Surcharges</h4>
                                <p class="text-xs text-gray-500 leading-relaxed">กทม. ปกติ, ตจว. +20฿, พื้นที่ห่างไกล +50฿</p>
                            </div>

                            <div class="p-4 bg-gray-900/50 rounded-2xl border border-gray-700 group hover:border-amber-500/50 transition-all cursor-pointer opacity-50">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black text-gray-500 uppercase">ตามจำนวนชิ้น</span>
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 rounded-full bg-gray-600"></div>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-400 mb-1">Quantity-based Rules</h4>
                                <p class="text-xs text-gray-600 leading-relaxed">ชิ้นแรก 40฿, ชิ้นถัดไป +10฿/ชิ้น</p>
                            </div>
                        </div>

                        <button class="w-full py-4 border-2 border-dashed border-gray-700 rounded-2xl text-gray-500 hover:text-amber-400 hover:border-amber-500/50 transition-all font-bold text-sm">
                            <i class="fas fa-plus-circle mr-2"></i> สร้างกฎใหม่
                        </button>

                        <div class="pt-4 border-t border-gray-700">
                            <h5 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-4">ตัวช่วยตั้งค่าด่วน</h5>
                            <div class="grid grid-cols-2 gap-3">
                                <button class="px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-xs font-bold text-gray-300 hover:bg-gray-700 transition-colors">ล้างค่าทั้งหมด</button>
                                <button class="px-4 py-2 bg-gray-900 border border-gray-700 rounded-xl text-xs font-bold text-gray-300 hover:bg-gray-700 transition-colors">ใช้ค่าเริ่มต้น</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shippingSettings() {
            return {
                methods: @json($shippingMethods),
                isSaving: false,
                saveSettings() {
                    window.showNotification('success', 'บันทึกสำเร็จ', 'ระบบได้บันทึกการตั้งค่าค่าจัดส่งของคุณแล้ว (Mockup Mode)');
                }
            }
        }
    </script>
@endsection
