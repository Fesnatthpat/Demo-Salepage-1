@extends('layouts.admin')

@section('title', 'ตั้งค่าจัดการค่าจัดส่ง')
@section('page-title', 'ตั้งค่าจัดการค่าจัดส่ง')

@section('content')
    <div class="container mx-auto pb-20" x-data="shippingManager()">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center shadow-lg border border-indigo-500/30 backdrop-blur-sm">
                    <i class="fas fa-truck text-indigo-400 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">ตั้งค่าจัดการค่าจัดส่ง</h1>
                    <p class="text-gray-400 mt-1 text-sm font-medium">จัดการเงื่อนไขการคิดค่าจัดส่งและบริษัทขนส่งสำหรับร้านค้าของคุณ</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="saveGlobalSettings()" :disabled="isSavingGlobal"
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all duration-200 flex items-center gap-2 shadow-lg shadow-emerald-900/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                    <i class="fas fa-save" x-show="!isSavingGlobal"></i>
                    <span class="loading loading-spinner loading-sm" x-show="isSavingGlobal" x-cloak></span>
                    บันทึกการตั้งค่าหลัก
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            {{-- Left: Global Settings (4 columns) --}}
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-gray-800/80 backdrop-blur-md rounded-2xl border border-gray-700/50 overflow-hidden shadow-xl sticky top-8">
                    <div class="px-6 py-5 bg-gray-800/50 border-b border-gray-700/50 flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-500/10 rounded-lg text-indigo-400">
                            <i class="fas fa-cog text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg text-white">รูปแบบการคิดค่าส่ง</h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">โหมดการคิดค่าส่งปัจจุบัน</label>
                            <div class="relative">
                                <select x-model="globalData.shipping_mode" 
                                    class="w-full bg-gray-900/80 border border-gray-700 rounded-xl py-3.5 px-4 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all font-semibold appearance-none cursor-pointer">
                                    <option value="global">ใช้ราคาคงที่ (Global)</option>
                                    <option value="methods">ใช้ตามบริษัทขนส่ง (ยืดหยุ่น)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-400">
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <div x-show="globalData.shipping_mode === 'global'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <div class="space-y-5 pt-5 border-t border-gray-700/50">
                                <div class="flex items-start gap-3 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                                    <i class="fas fa-info-circle text-amber-400 mt-0.5"></i>
                                    <p class="text-xs text-amber-200/80 leading-relaxed font-medium">
                                        โหมด Global จะใช้ราคาด้านล่างนี้เป็นมาตรฐานเดียวสำหรับทุกคำสั่งซื้อ
                                    </p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="group">
                                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider group-focus-within:text-indigo-400 transition-colors">ส่งฟรีเมื่อซื้อครบ (฿)</label>
                                        <div class="relative">
                                            <input type="number" x-model="globalData.free_shipping_threshold" 
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all font-mono text-lg placeholder-gray-600" placeholder="0">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider group-focus-within:text-indigo-400 transition-colors">ค่าส่ง กรุงเทพฯ และปริมณฑล (฿)</label>
                                        <div class="relative">
                                            <input type="number" x-model="globalData.bkk_flat_rate" 
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all font-mono text-lg placeholder-gray-600" placeholder="0">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500">
                                                <i class="fas fa-city"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="group">
                                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider group-focus-within:text-indigo-400 transition-colors">ค่าส่ง ต่างจังหวัด (฿)</label>
                                        <div class="relative">
                                            <input type="number" x-model="globalData.upc_flat_rate" 
                                                class="w-full bg-gray-900/50 border border-gray-700 rounded-xl py-3 pl-10 pr-4 text-white focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all font-mono text-lg placeholder-gray-600" placeholder="0">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-500">
                                                <i class="fas fa-map-marked-alt"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="globalData.shipping_mode === 'methods'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <div class="p-5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex gap-3">
                                <i class="fas fa-check-circle text-emerald-400 text-lg shrink-0 mt-0.5"></i>
                                <p class="text-sm text-emerald-200/80 leading-relaxed font-medium">
                                    ระบบจะคำนวณราคาตามบริษัทขนส่งที่เปิดใช้งานด้านขวามือ (ลูกค้าสามารถเลือกบริษัทขนส่งเองได้ในหน้าชำระเงิน)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Shipping Methods List (8 columns) --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-gray-800/80 backdrop-blur-md rounded-2xl border border-gray-700/50 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-800/50 border-b border-gray-700/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-blue-500/10 rounded-lg text-blue-400">
                                <i class="fas fa-truck-loading text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-white">บริษัทขนส่งที่รองรับ</h3>
                                <p class="text-xs text-gray-400 mt-0.5">จัดการตัวเลือกการจัดส่งแบบยืดหยุ่น</p>
                            </div>
                        </div>
                        <button type="button" @click="openAddModal()" 
                            class="px-4 py-2 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl text-sm font-bold flex items-center gap-2 transition-all duration-200 border border-indigo-500/20 hover:border-indigo-500">
                            <i class="fas fa-plus"></i> เพิ่มบริษัทขนส่ง
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-nowrap">
                            <thead class="bg-gray-900/40 text-gray-400 text-xs uppercase tracking-wider border-b border-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center w-24">สถานะหลัก</th>
                                    <th class="px-6 py-4 font-bold">ข้อมูลบริษัท</th>
                                    <th class="px-6 py-4 font-bold text-center">ค่าส่ง (กทม./ตจว.)</th>
                                    <th class="px-6 py-4 font-bold text-center">บวกเพิ่ม/ชิ้น</th>
                                    <th class="px-6 py-4 font-bold text-center">เงื่อนไขส่งฟรี</th>
                                    <th class="px-6 py-4 font-bold text-right">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50">
                                <template x-for="method in methods" :key="method.id">
                                    <tr class="hover:bg-gray-750/50 transition-colors duration-150 group" :class="method.is_default ? 'bg-indigo-500/5' : ''">
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center">
                                                <div x-show="method.is_default" class="bg-amber-500/10 text-amber-400 px-3 py-1.5 rounded-lg text-[11px] font-bold flex items-center gap-1.5 border border-amber-500/20 shadow-sm">
                                                    <i class="fas fa-star text-xs"></i> ค่าเริ่มต้น
                                                </div>
                                                <button @click="setAsDefault(method)" x-show="!method.is_default" 
                                                    class="text-gray-500 hover:text-amber-400 hover:bg-amber-400/10 px-3 py-1.5 rounded-lg transition-all text-xs flex items-center gap-1.5 opacity-50 group-hover:opacity-100 font-medium">
                                                    <i class="far fa-star"></i> <span>ตั้งเป็นหลัก</span>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-xl flex items-center justify-center border shadow-sm transition-colors" 
                                                    :class="method.is_active ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-gray-800 border-gray-700 text-gray-600'">
                                                    <i class="fas fa-shipping-fast text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-white flex items-center gap-2 text-base">
                                                        <span x-text="method.name"></span>
                                                        <span x-show="!method.is_active" class="bg-red-500/10 text-red-400 border border-red-500/20 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold tracking-wider">ปิดใช้งาน</span>
                                                    </div>
                                                    <div class="text-xs text-gray-400 uppercase tracking-wider font-mono mt-0.5 bg-gray-900/50 inline-block px-1.5 py-0.5 rounded" x-text="method.code"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="font-mono font-bold text-emerald-400 text-base">฿<span x-text="method.bkk_rate"></span> <span class="text-gray-500 font-sans font-normal mx-1">/</span> ฿<span x-text="method.upc_rate"></span></div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-block bg-blue-500/10 border border-blue-500/20 text-blue-400 px-3 py-1 rounded-lg font-mono font-bold text-sm">
                                                +฿<span x-text="method.per_item_rate"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <div x-show="method.free_threshold" class="bg-indigo-500/10 text-indigo-300 px-2 py-0.5 rounded text-xs border border-indigo-500/20">
                                                    ยอดครบ ฿<span class="font-mono font-bold" x-text="method.free_threshold"></span>
                                                </div>
                                                <div x-show="method.min_items_for_free_shipping" class="bg-pink-500/10 text-pink-300 px-2 py-0.5 rounded text-xs border border-pink-500/20">
                                                    ซื้อครบ <span class="font-mono font-bold" x-text="method.min_items_for_free_shipping"></span> ชิ้น
                                                </div>
                                                <span x-show="!method.free_threshold && !method.min_items_for_free_shipping" class="text-gray-500 text-sm italic">- ไม่มี -</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openEditModal(method)" 
                                                    class="w-9 h-9 flex items-center justify-center bg-gray-700/50 text-gray-300 hover:bg-blue-500 hover:text-white rounded-lg transition-all duration-200" title="แก้ไข">
                                                    <i class="fas fa-pen text-sm"></i>
                                                </button>
                                                <button @click="deleteMethod(method.id)" 
                                                    class="w-9 h-9 flex items-center justify-center bg-gray-700/50 text-gray-300 hover:bg-red-500 hover:text-white rounded-lg transition-all duration-200" title="ลบ">
                                                    <i class="fas fa-trash-alt text-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="methods.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
                                                <p class="text-base font-medium">ยังไม่มีข้อมูลบริษัทขนส่ง</p>
                                                <p class="text-xs mt-1">คลิกที่ปุ่ม "เพิ่มบริษัทขนส่ง" เพื่อเริ่มต้นใช้งาน</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal for Add/Edit Shipping Method --}}
        <div x-show="modalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/90 backdrop-blur-sm" @click="modalOpen = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                <div class="relative inline-block w-full max-w-3xl text-left align-middle transition-all transform bg-gray-800 border border-gray-700 shadow-2xl rounded-2xl z-10 sm:my-8"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="px-8 py-6 bg-gray-800 border-b border-gray-700 flex justify-between items-center rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <i class="fas" :class="editMode ? 'fa-pen' : 'fa-plus'"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-white" x-text="editMode ? 'แก้ไขข้อมูลบริษัทขนส่ง' : 'เพิ่มบริษัทขนส่งใหม่'"></h3>
                        </div>
                        <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-white bg-gray-700/50 hover:bg-gray-600 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="saveMethod()">
                        <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                            {{-- Section 1: General Info --}}
                            <div>
                                <h4 class="text-sm font-bold text-gray-300 flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                                    <i class="fas fa-info-circle text-indigo-400"></i> ข้อมูลทั่วไป
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">ชื่อบริษัทขนส่ง <span class="text-red-400">*</span></label>
                                        <input type="text" x-model="formData.name" required placeholder="เช่น Kerry Express, J&T" 
                                            class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">รหัสอ้างอิง (Code) <span class="text-red-400">*</span></label>
                                        <input type="text" x-model="formData.code" required placeholder="เช่น KERRY, JT" 
                                            class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all uppercase font-mono">
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">รายละเอียดเพิ่มเติม (แสดงให้ลูกค้าเห็น)</label>
                                        <textarea x-model="formData.description" placeholder="เช่น จัดส่งด่วนภายใน 1-2 วันทำการ" 
                                            class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 px-4 text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all resize-none" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Pricing --}}
                            <div>
                                <h4 class="text-sm font-bold text-gray-300 flex items-center gap-2 mb-4 border-b border-gray-700 pb-2">
                                    <i class="fas fa-money-bill-wave text-emerald-400"></i> โครงสร้างราคาค่าจัดส่ง
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div class="space-y-2 relative group">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">ค่าส่ง กทม. (฿) <span class="text-red-400">*</span></label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3.5 text-gray-500 font-mono">฿</span>
                                            <input type="number" x-model="formData.bkk_rate" required min="0" 
                                                class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 pl-8 pr-4 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all font-mono text-lg">
                                        </div>
                                    </div>
                                    <div class="space-y-2 group">
                                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">ค่าส่ง ตจว. (฿) <span class="text-red-400">*</span></label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3.5 text-gray-500 font-mono">฿</span>
                                            <input type="number" x-model="formData.upc_rate" required min="0" 
                                                class="w-full bg-gray-900 border border-gray-700 rounded-xl py-3 pl-8 pr-4 text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all font-mono text-lg">
                                        </div>
                                    </div>
                                    <div class="space-y-2 group">
                                        <label class="text-xs font-bold text-blue-400 uppercase tracking-wider">บวกเพิ่มต่อชิ้น (฿) <span class="text-red-400">*</span></label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-3.5 text-gray-500 font-mono">+฿</span>
                                            <input type="number" x-model="formData.per_item_rate" required min="0" 
                                                class="w-full bg-blue-900/20 border border-blue-800/50 rounded-xl py-3 pl-11 pr-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-mono text-lg">
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1">คิดเพิ่มตั้งแต่สินค้าชิ้นที่ 2</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Promotions & Status --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="bg-indigo-500/5 p-5 rounded-2xl border border-indigo-500/20">
                                    <h4 class="text-sm font-bold text-indigo-300 flex items-center gap-2 mb-4">
                                        <i class="fas fa-gift"></i> เงื่อนไขส่งฟรี (ไม่บังคับ)
                                    </h4>
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">เมื่อยอดซื้อครบ (฿)</label>
                                            <input type="number" x-model="formData.free_threshold" placeholder="เว้นว่างถ้าไม่มี" 
                                                class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2.5 px-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">หรือซื้อครบจำนวน (ชิ้น)</label>
                                            <input type="number" x-model="formData.min_items_for_free_shipping" placeholder="เว้นว่างถ้าไม่มี" 
                                                class="w-full bg-gray-900 border border-gray-700 rounded-xl py-2.5 px-4 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-mono">
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-900/30 p-5 rounded-2xl border border-gray-700/50 flex flex-col justify-center space-y-6">
                                    <h4 class="text-sm font-bold text-gray-300 flex items-center gap-2 mb-2">
                                        <i class="fas fa-toggle-on text-gray-400"></i> ตั้งค่าสถานะ
                                    </h4>
                                    
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <div class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">เปิดใช้งานขนส่งนี้</div>
                                            <div class="text-xs text-gray-500 mt-0.5">ลูกค้าสามารถเลือกใช้งานได้</div>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" x-model="formData.is_active" class="sr-only peer">
                                            <div class="w-12 h-6.5 bg-gray-700 rounded-full peer peer-checked:bg-emerald-500 transition-all duration-300"></div>
                                            <div class="absolute left-1 top-1 bg-white w-4.5 h-4.5 rounded-full transition-all duration-300 peer-checked:translate-x-5 shadow-sm"></div>
                                        </div>
                                    </label>

                                    <div class="w-full h-px bg-gray-700/50"></div>

                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <div>
                                            <div class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">ตั้งเป็นค่าเริ่มต้น</div>
                                            <div class="text-xs text-gray-500 mt-0.5">เลือกเป็น Default ในหน้าชำระเงิน</div>
                                        </div>
                                        <div class="relative">
                                            <input type="checkbox" x-model="formData.is_default" class="sr-only peer">
                                            <div class="w-12 h-6.5 bg-gray-700 rounded-full peer peer-checked:bg-amber-500 transition-all duration-300"></div>
                                            <div class="absolute left-1 top-1 bg-white w-4.5 h-4.5 rounded-full transition-all duration-300 peer-checked:translate-x-5 shadow-sm"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 bg-gray-800/80 border-t border-gray-700 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="modalOpen = false" class="px-6 py-2.5 rounded-xl font-bold text-gray-400 hover:bg-gray-700 hover:text-white transition-all duration-200">ยกเลิก</button>
                            <button type="submit" :disabled="isSavingMethod" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition-all duration-200 shadow-lg shadow-indigo-900/30 flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed transform active:scale-95">
                                <i class="fas fa-save" x-show="!isSavingMethod"></i>
                                <span class="loading loading-spinner loading-sm" x-show="isSavingMethod" x-cloak></span>
                                <span x-text="editMode ? 'บันทึกการแก้ไข' : 'เพิ่มบริษัทขนส่ง'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom Scrollbar for Modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #4b5563;
            border-radius: 20px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #6b7280;
        }
        [x-cloak] { display: none !important; }
        .h-6\.5 { height: 1.625rem; }
        .w-4\.5 { width: 1.125rem; }
        .h-4\.5 { height: 1.125rem; }
    </style>
@endsection

@push('scripts')
    <script>
        window.shippingManager = function() {
            return {
                globalData: {!! json_encode($globalSettings ?? ['shipping_mode' => 'global', 'free_shipping_threshold' => 0, 'bkk_flat_rate' => 0, 'upc_flat_rate' => 0]) !!},
                methods: {!! json_encode($methods ?? []) !!},
                isSavingGlobal: false,
                isSavingMethod: false,
                modalOpen: false,
                editMode: false,
                formData: { id: null, name: '', description: '', code: '', is_active: true, is_default: false, bkk_rate: null, upc_rate: null, per_item_rate: 0, free_threshold: null, min_items_for_free_shipping: null },

                getEmptyForm() {
                    return { id: null, name: '', description: '', code: '', is_active: true, is_default: false, bkk_rate: null, upc_rate: null, per_item_rate: 0, free_threshold: null, min_items_for_free_shipping: null };
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
                        if (data.success && typeof window.showNotification === 'function') {
                            window.showNotification('success', 'สำเร็จ', data.message || 'บันทึกการตั้งค่าเรียบร้อย');
                        }
                    } catch (e) { console.error(e); }
                    this.isSavingGlobal = false;
                },

                openAddModal() {
                    this.editMode = false;
                    this.formData = this.getEmptyForm();
                    this.modalOpen = true;
                },

                openEditModal(method) {
                    this.editMode = true;
                    this.formData = JSON.parse(JSON.stringify(method)); // Deep clone
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
                            location.reload(); // Reload เพื่อดึงค่าเรียงลำดับใหม่และอัปเดต Default ให้ชัวร์ที่สุด
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
                    if(typeof Swal === 'undefined') return alert('ยืนยันการลบ?');

                    const result = await Swal.fire({
                        title: 'ยืนยันการลบข้อมูล?',
                        text: "ข้อมูลบริษัทขนส่งนี้จะถูกลบถาวร ไม่สามารถกู้คืนได้",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#4b5563',
                        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> ลบข้อมูล',
                        cancelButtonText: 'ยกเลิก',
                        background: '#1f2937',
                        color: '#f3f4f6'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/shipping-settings/methods/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await response.json();
                            if (data.success) {
                                this.methods = this.methods.filter(m => m.id !== id);
                                if (typeof window.showNotification === 'function') {
                                    window.showNotification('success', 'ลบสำเร็จ', data.message || 'ลบข้อมูลบริษัทขนส่งเรียบร้อยแล้ว');
                                }
                            }
                        } catch (e) { console.error(e); }
                    }
                }
            }
        }
    </script>
@endpush