@extends('layout')

@section('content')
    <div x-data="promotionSystem()" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    จัดการโปรโมชั่น (Marketing Tools)
                </h1>
                <p class="text-gray-500 text-sm mt-1 ml-10">สร้างแคมเปญ คูปอง และส่วนลดเพื่อกระตุ้นยอดขาย</p>
            </div>
            <button @click="openCreateModal()" class="btn bg-red-600 hover:bg-red-700 text-white gap-2 shadow-lg shadow-red-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                สร้างโปรโมชั่นใหม่
            </button>
        </div>

        {{-- Dashboard Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                </div>
                <div>
                    <div class="text-gray-500 text-xs">คูปองที่ใช้งานอยู่</div>
                    <div class="text-2xl font-bold text-gray-800">8</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <div class="text-gray-500 text-xs">Flash Sale (Active)</div>
                    <div class="text-2xl font-bold text-gray-800">12 รายการ</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <div>
                    <div class="text-gray-500 text-xs">Pre-order</div>
                    <div class="text-2xl font-bold text-gray-800">5 รายการ</div>
                </div>
            </div>
             <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <div class="text-gray-500 text-xs">ยอดขายจากโปรฯ</div>
                    <div class="text-2xl font-bold text-gray-800">฿45,200</div>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="mb-6">
            <div class="tabs tabs-boxed bg-white p-2 w-full md:w-fit shadow-sm border border-gray-100">
                <a class="tab tab-lg" :class="activeTab === 'coupons' ? 'tab-active bg-red-600 text-white' : ''" @click="activeTab = 'coupons'">คูปองส่วนลด</a>
                <a class="tab tab-lg" :class="activeTab === 'flashsale' ? 'tab-active bg-yellow-500 text-white' : ''" @click="activeTab = 'flashsale'">Flash Sale</a>
                <a class="tab tab-lg" :class="activeTab === 'bundles' ? 'tab-active bg-purple-600 text-white' : ''" @click="activeTab = 'bundles'">จัดเซต (Bundle)</a>
                <a class="tab tab-lg" :class="activeTab === 'preorder' ? 'tab-active bg-blue-600 text-white' : ''" @click="activeTab = 'preorder'">Pre-order</a>
            </div>
        </div>

        {{-- ================= TAB 1: COUPONS ================= --}}
        <div x-show="activeTab === 'coupons'" class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="table w-full">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th>ชื่อคูปอง / รหัส</th>
                            <th>ประเภทส่วนลด</th>
                            <th>เงื่อนไข</th>
                            <th>การใช้งาน</th>
                            <th>ระยะเวลา</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div class="font-bold">ส่วนลดต้อนรับ</div>
                                <div class="badge badge-outline gap-1 mt-1 text-xs">
                                    Code: <span class="font-bold text-red-500">WELCOME100</span>
                                </div>
                            </td>
                            <td><span class="text-emerald-600 font-bold">฿100</span></td>
                            <td class="text-xs text-gray-500">ขั้นต่ำ ฿500</td>
                            <td>
                                <div class="flex flex-col text-xs">
                                    <span>ใช้ไป: 45/100</span>
                                    <progress class="progress progress-error w-20 h-1.5" value="45" max="100"></progress>
                                </div>
                            </td>
                            <td class="text-xs">01/12/68 - 31/12/68</td>
                            <td><input type="checkbox" class="toggle toggle-success toggle-sm" checked /></td>
                            <td>
                                <button class="btn btn-sm btn-ghost">แก้ไข</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div class="font-bold">ลดทั้งร้าน 10%</div>
                                <div class="badge badge-outline gap-1 mt-1 text-xs">
                                    Code: <span class="font-bold text-red-500">HM10OFF</span>
                                </div>
                            </td>
                            <td><span class="text-emerald-600 font-bold">10%</span></td>
                            <td class="text-xs text-gray-500">ลดสูงสุด ฿200</td>
                            <td>
                                <div class="flex flex-col text-xs">
                                    <span>ใช้ไป: 120/500</span>
                                    <progress class="progress progress-error w-20 h-1.5" value="120" max="500"></progress>
                                </div>
                            </td>
                            <td class="text-xs">ตลอดปี 2025</td>
                            <td><input type="checkbox" class="toggle toggle-success toggle-sm" checked /></td>
                            <td>
                                <button class="btn btn-sm btn-ghost">แก้ไข</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TAB 2: FLASH SALE ================= --}}
        <div x-show="activeTab === 'flashsale'">
            
            {{-- Flash Sale Header --}}
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-xl shadow-lg mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold flex items-center gap-2">
                        <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        กำลังจัดรายการ (Live Now)
                    </h2>
                    <p class="text-yellow-100 mt-1">รอบเวลา 12:00 - 15:00 น.</p>
                </div>
                <div class="text-center bg-white/20 p-3 rounded-lg backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide">Ends in</p>
                    <div class="text-3xl font-mono font-bold">01:45:20</div>
                </div>
            </div>

            {{-- Flash Sale Items --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="relative">
                        <img src="/images/T-Shirt-1.png" class="w-full h-40 object-cover rounded-lg mb-3">
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">-50%</span>
                    </div>
                    <h3 class="font-bold text-gray-900 truncate">เสื้อยืด Oversize Cotton</h3>
                    <div class="flex items-end gap-2 mt-2">
                        <span class="text-xl font-bold text-red-600">฿195</span>
                        <span class="text-sm text-gray-400 line-through mb-1">฿390</span>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>ขายแล้ว 85%</span>
                            <span class="text-red-500 font-bold">เหลือ 5 ชิ้น</span>
                        </div>
                        <progress class="progress progress-error w-full h-2" value="85" max="100"></progress>
                    </div>
                </div>

                 <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="relative">
                        <img src="/images/T-Shirt-B.png" class="w-full h-40 object-cover rounded-lg mb-3">
                        <span class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">-30%</span>
                    </div>
                    <h3 class="font-bold text-gray-900 truncate">กางเกงยีนส์ Slim Fit</h3>
                    <div class="flex items-end gap-2 mt-2">
                        <span class="text-xl font-bold text-red-600">฿690</span>
                        <span class="text-sm text-gray-400 line-through mb-1">฿990</span>
                    </div>
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>ขายแล้ว 40%</span>
                            <span>เหลือ 30 ชิ้น</span>
                        </div>
                        <progress class="progress progress-error w-full h-2" value="40" max="100"></progress>
                    </div>
                </div>
                
                {{-- Add New Flash Sale Slot --}}
                <div class="border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center text-gray-400 min-h-[300px] cursor-pointer hover:bg-gray-50 hover:border-emerald-500 hover:text-emerald-500 transition">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="font-bold">เพิ่มสินค้า Flash Sale</span>
                </div>
            </div>
        </div>

        {{-- ================= TAB 3: BUNDLES ================= --}}
        <div x-show="activeTab === 'bundles'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Bundle Card --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="badge bg-purple-100 text-purple-600 border-none font-bold">ชุดสุดคุ้ม (Set)</span>
                            <div class="flex gap-2">
                                <button class="btn btn-xs btn-circle btn-ghost"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Summer Set A</h3>
                        <p class="text-sm text-gray-500 mb-4">เสื้อยืด + หมวก ลดพิเศษ 15%</p>
                        
                        {{-- Visual Math --}}
                        <div class="flex items-center justify-center gap-3 bg-gray-50 p-4 rounded-lg mb-4">
                            <div class="text-center">
                                <img src="/images/T-Shirt-1.png" class="w-16 h-16 rounded object-cover mx-auto bg-white border border-gray-200">
                                <span class="text-xs text-gray-500 mt-1">฿390</span>
                            </div>
                            <span class="font-bold text-gray-400 text-xl">+</span>
                            <div class="text-center">
                                <img src="/images/T-Shirt-W.png" class="w-16 h-16 rounded object-cover mx-auto bg-white border border-gray-200">
                                <span class="text-xs text-gray-500 mt-1">฿250</span>
                            </div>
                            <span class="font-bold text-gray-400 text-xl">=</span>
                            <div class="text-center">
                                <div class="w-16 h-16 rounded bg-purple-600 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                    ฿550
                                </div>
                                <span class="text-xs text-red-500 font-bold mt-1 line-through">฿640</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500 border-t border-gray-100 pt-4">
                        <span>ขายไปแล้ว: 12 เซต</span>
                        <input type="checkbox" class="toggle toggle-success toggle-sm" checked />
                    </div>
                </div>

                {{-- Bundle Card 2 --}}
                 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="badge bg-blue-100 text-blue-600 border-none font-bold">ซื้อเยอะคุ้มกว่า (Volume)</span>
                            <div class="flex gap-2">
                                <button class="btn btn-xs btn-circle btn-ghost"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Buy 3 Get 10% Off</h3>
                        <p class="text-sm text-gray-500 mb-4">ซื้อสินค้าหมวดเสื้อยืดครบ 3 ตัว ลดทันที</p>
                        
                        <div class="bg-gray-50 p-4 rounded-lg mb-4 text-center">
                            <p class="text-gray-600">ซื้อ <span class="font-bold text-gray-900">3 ชิ้น</span> ขึ้นไป</p>
                            <p class="text-emerald-600 font-bold text-xl mt-1">ลด 10% ทันที</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm text-gray-500 border-t border-gray-100 pt-4">
                        <span>ใช้ไปแล้ว: 45 ออเดอร์</span>
                        <input type="checkbox" class="toggle toggle-success toggle-sm" checked />
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= TAB 4: PRE-ORDER ================= --}}
        <div x-show="activeTab === 'preorder'">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="table w-full">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th>สินค้า</th>
                            <th>ระยะเวลา Pre-order</th>
                            <th>ยอดจองปัจจุบัน</th>
                            <th>สถานะของเข้า</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50">
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="/images/T-Shirt-B.png" class="w-10 h-10 rounded object-cover">
                                    <div>
                                        <div class="font-bold">Collection หน้าหนาว 2025</div>
                                        <div class="text-xs text-gray-500">Limited Edition</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm">
                                <div>เปิดจอง: 1-15 ธ.ค.</div>
                                <div class="text-emerald-600">ส่งของ: 25 ธ.ค.</div>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-primary w-20" value="70" max="100"></progress>
                                    <span class="text-xs font-bold">70/100</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning text-xs">รอของเข้าคลัง</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-ghost">ปิดรับจอง</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MODAL: CREATE PROMOTION ================= --}}
        <dialog class="modal" :class="showCreateModal ? 'modal-open' : ''">
            <div class="modal-box w-11/12 max-w-2xl bg-white">
                <h3 class="font-bold text-lg mb-4 text-gray-800">สร้างโปรโมชั่นใหม่</h3>
                
                {{-- Type Selection --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-6">
                    <button @click="promoType = 'coupon'" class="btn btn-sm" :class="promoType === 'coupon' ? 'btn-neutral' : 'btn-outline'">คูปอง</button>
                    <button @click="promoType = 'flash'" class="btn btn-sm" :class="promoType === 'flash' ? 'btn-neutral' : 'btn-outline'">Flash Sale</button>
                    <button @click="promoType = 'bundle'" class="btn btn-sm" :class="promoType === 'bundle' ? 'btn-neutral' : 'btn-outline'">Bundle</button>
                    <button @click="promoType = 'preorder'" class="btn btn-sm" :class="promoType === 'preorder' ? 'btn-neutral' : 'btn-outline'">Pre-order</button>
                </div>

                {{-- Dynamic Form Fields --}}
                <div class="space-y-4">
                    
                    {{-- COUPON FIELDS --}}
                    <div x-show="promoType === 'coupon'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">ชื่อคูปอง</span></label>
                                <input type="text" placeholder="เช่น ลดต้อนรับปีใหม่" class="input input-bordered w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">รหัสคูปอง (Code)</span></label>
                                <input type="text" placeholder="NEWYEAR25" class="input input-bordered w-full uppercase" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div class="form-control">
                                <label class="label"><span class="label-text">ประเภท</span></label>
                                <select class="select select-bordered w-full">
                                    <option>ส่วนลดบาท (Fixed)</option>
                                    <option>ส่วนลดเปอร์เซ็นต์ (%)</option>
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">มูลค่าส่วนลด</span></label>
                                <input type="number" placeholder="100" class="input input-bordered w-full" />
                            </div>
                        </div>
                    </div>

                    {{-- FLASH SALE FIELDS --}}
                    <div x-show="promoType === 'flash'" class="space-y-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text">เลือกสินค้า</span></label>
                            <input type="text" placeholder="ค้นหาสินค้า..." class="input input-bordered w-full" />
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">ราคา Flash Sale</span></label>
                                <input type="number" class="input input-bordered w-full text-red-600 font-bold" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">จำนวนจำกัด (Stock)</span></label>
                                <input type="number" class="input input-bordered w-full" />
                            </div>
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text">เริ่มเวลา</span></label>
                                <input type="datetime-local" class="input input-bordered w-full" />
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">สิ้นสุดเวลา</span></label>
                                <input type="datetime-local" class="input input-bordered w-full" />
                            </div>
                        </div>
                    </div>

                    {{-- Placeholder for others --}}
                    <div x-show="promoType === 'bundle' || promoType === 'preorder'">
                        <p class="text-gray-500 text-center py-4">ฟอร์มสำหรับ <span x-text="promoType"></span> กำลังพัฒนา...</p>
                    </div>

                </div>

                <div class="modal-action mt-8">
                    <button @click="showCreateModal = false" class="btn btn-ghost text-gray-500">ยกเลิก</button>
                    <button @click="showCreateModal = false; alert('สร้างโปรโมชั่นสำเร็จ!')" class="btn bg-emerald-600 hover:bg-emerald-700 text-white">บันทึก</button>
                </div>
            </div>
        </dialog>

    </div>

    <script>
        function promotionSystem() {
            return {
                activeTab: 'coupons',
                showCreateModal: false,
                promoType: 'coupon',
                
                openCreateModal() {
                    this.showCreateModal = true;
                    // Reset to default
                    this.promoType = 'coupon'; 
                }
            }
        }
    </script>
@endsection