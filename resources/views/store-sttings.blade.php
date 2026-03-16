@extends('layout')

@section('content')
    <div x-data="storeSettings()" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    ตั้งค่าร้านค้า (Settings)
                </h1>
                <p class="text-gray-500 text-sm mt-1 ml-10">จัดการข้อมูลพื้นฐาน การจัดส่ง และนโยบายต่างๆ</p>
            </div>
            
            {{-- Save Button (Global) --}}
            <button @click="saveSettings()" class="btn bg-emerald-600 hover:bg-emerald-700 text-white gap-2 shadow-md w-full md:w-auto" :class="saving ? 'loading' : ''">
                <span x-show="!saving">บันทึกการเปลี่ยนแปลง</span>
                <span x-show="saving">กำลังบันทึก...</span>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- ================= SIDEBAR MENU ================= --}}
            <div class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                    <ul class="menu p-2 bg-base-100 w-full text-base-content">
                        <li>
                            <a @click="activeTab = 'general'" :class="activeTab === 'general' ? 'active bg-emerald-50 text-emerald-700 font-bold' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                ข้อมูลทั่วไป (General)
                            </a>
                        </li>
                        <li>
                            <a @click="activeTab = 'address'" :class="activeTab === 'address' ? 'active bg-emerald-50 text-emerald-700 font-bold' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                ที่อยู่ & ติดต่อ (Contact)
                            </a>
                        </li>
                        <li>
                            <a @click="activeTab = 'shipping'" :class="activeTab === 'shipping' ? 'active bg-emerald-50 text-emerald-700 font-bold' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                การจัดส่ง (Shipping)
                            </a>
                        </li>
                        <li>
                            <a @click="activeTab = 'tax'" :class="activeTab === 'tax' ? 'active bg-emerald-50 text-emerald-700 font-bold' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                ภาษี & ใบเสร็จ (Tax)
                            </a>
                        </li>
                        <li>
                            <a @click="activeTab = 'policy'" :class="activeTab === 'policy' ? 'active bg-emerald-50 text-emerald-700 font-bold' : ''">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                นโยบายร้าน (Policy)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ================= CONTENT AREA ================= --}}
            <div class="flex-1 bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200">
                
                {{-- TAB 1: General --}}
                <div x-show="activeTab === 'general'" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">ข้อมูลทั่วไป</h2>
                    
                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">โลโก้ร้านค้า</label>
                        <div class="flex items-center gap-6">
                            <div class="avatar">
                                <div class="w-24 h-24 rounded-full ring ring-emerald-500 ring-offset-base-100 ring-offset-2 bg-gray-100 overflow-hidden">
                                    <img :src="logoPreview" class="object-cover w-full h-full" />
                                </div>
                            </div>
                            <div>
                                <label class="btn btn-outline btn-sm gap-2">
                                    เปลี่ยนรูป
                                    <input type="file" class="hidden" @change="handleLogoUpload">
                                </label>
                                <p class="text-xs text-gray-500 mt-2">รองรับไฟล์: .jpg, .png ขนาดไม่เกิน 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Shop Name & Desc --}}
                    <div class="grid grid-cols-1 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">ชื่อร้านค้า (Shop Name)</span></label>
                            <input type="text" value="H&M-R Store Thailand" class="input input-bordered w-full focus:ring-emerald-500" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">คำอธิบายร้านค้า (Description)</span></label>
                            <textarea class="textarea textarea-bordered h-24 focus:ring-emerald-500" placeholder="ใส่รายละเอียดเกี่ยวกับร้านค้าของคุณ..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: Address & Contact --}}
                <div x-show="activeTab === 'address'" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">ที่อยู่และช่องทางติดต่อ</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">อีเมลติดต่อ</span></label>
                            <input type="email" value="support@hm-r.com" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">เบอร์โทรศัพท์</span></label>
                            <input type="tel" value="02-123-4567" class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">ที่อยู่ร้าน (สำหรับแสดงในหน้าเว็บและใบเสร็จ)</span></label>
                        <textarea class="textarea textarea-bordered h-24" placeholder="ที่อยู่..."></textarea>
                    </div>

                    <div class="divider">Social Media</div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg> Facebook Page URL</span></label>
                            <input type="text" placeholder="https://facebook.com/..." class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text flex items-center gap-2"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 5.92 2 10.75c0 4.3 3.52 7.9 8.35 8.63.38.08.9.23.68 1.05-.18.66-.4 1.76-1.5 2.45 2.14.22 5.86-1.6 7.6-3.76 3.1-1.83 4.87-4.66 4.87-8.37C22 5.92 17.52 2 12 2z"/></svg> LINE ID / Link</span></label>
                            <input type="text" placeholder="@yourshop" class="input input-bordered w-full" />
                        </div>
                    </div>
                </div>

                {{-- TAB 3: Shipping --}}
                <div x-show="activeTab === 'shipping'" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">ตั้งค่าการจัดส่ง</h2>

                    {{-- Free Shipping Rules --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-blue-800">ส่งฟรีเมื่อซื้อครบ (บาท)</span>
                            <p class="text-xs text-blue-600">ตั้งค่าเป็น 0 หากไม่ต้องการเปิดใช้</p>
                        </div>
                        <input type="number" value="1000" class="input input-sm input-bordered w-32 text-right font-bold" />
                    </div>

                    {{-- Couriers List --}}
                    <div class="space-y-3">
                        {{-- Flash --}}
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center font-bold text-white text-xs">FLASH</div>
                                <div>
                                    <p class="font-bold text-gray-800">Flash Express</p>
                                    <p class="text-xs text-gray-500">Standard Delivery</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">ค่าส่ง:</span>
                                    <input type="number" value="35" class="input input-xs input-bordered w-16 text-center">
                                </div>
                                <input type="checkbox" class="toggle toggle-success" checked />
                            </div>
                        </div>

                        {{-- Kerry --}}
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center font-bold text-white text-xs">KERRY</div>
                                <div>
                                    <p class="font-bold text-gray-800">Kerry Express</p>
                                    <p class="text-xs text-gray-500">Standard Delivery</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">ค่าส่ง:</span>
                                    <input type="number" value="45" class="input input-xs input-bordered w-16 text-center">
                                </div>
                                <input type="checkbox" class="toggle toggle-success" checked />
                            </div>
                        </div>

                        {{-- Thai Post --}}
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center font-bold text-white text-xs">EMS</div>
                                <div>
                                    <p class="font-bold text-gray-800">ไปรษณีย์ไทย (EMS)</p>
                                    <p class="text-xs text-gray-500">Express</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">ค่าส่ง:</span>
                                    <input type="number" value="50" class="input input-xs input-bordered w-16 text-center">
                                </div>
                                <input type="checkbox" class="toggle toggle-success" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 4: Tax & Invoice --}}
                <div x-show="activeTab === 'tax'" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">ภาษีและใบเสร็จ</h2>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <span class="font-bold text-gray-700">จดทะเบียนภาษีมูลค่าเพิ่ม (VAT)</span>
                        <input type="checkbox" class="toggle toggle-emerald" checked />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">เลขประจำตัวผู้เสียภาษี</span></label>
                            <input type="text" placeholder="xxxxxxxxxxxxx" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-bold">อัตราภาษี (%)</span></label>
                            <input type="number" value="7" class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">หมายเหตุท้ายใบเสร็จ</span></label>
                        <input type="text" value="ขอบคุณที่ใช้บริการ" class="input input-bordered w-full" />
                    </div>
                </div>

                {{-- TAB 5: Policy --}}
                <div x-show="activeTab === 'policy'" class="space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">นโยบายร้านค้า</h2>
                    
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">นโยบายการเปลี่ยน/คืนสินค้า</span></label>
                        <textarea class="textarea textarea-bordered h-32" placeholder="เช่น รับเปลี่ยนคืนภายใน 7 วัน..."></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">ข้อกำหนดและเงื่อนไข (Terms of Service)</span></label>
                        <textarea class="textarea textarea-bordered h-32" placeholder="ข้อกำหนดการใช้งาน..."></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function storeSettings() {
            return {
                activeTab: 'general',
                logoPreview: '/images/logo_hm.png', // Default image
                saving: false,

                handleLogoUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.logoPreview = URL.createObjectURL(file);
                    }
                },

                saveSettings() {
                    this.saving = true;
                    // Simulate API call
                    setTimeout(() => {
                        this.saving = false;
                        alert('บันทึกการตั้งค่าเรียบร้อยแล้ว!');
                    }, 1500);
                }
            }
        }
    </script>
@endsection