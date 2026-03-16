@extends('layout')

@section('content')
    {{-- Alpine.js Data สำหรับจัดการ Modal และสถานะ --}}
    <div x-data="{
        showModal: false,
        showDeleteModal: false,
        isEditMode: false,
        previewImage: null,
        
        // ข้อมูลจำลองสำหรับ Form
        formData: {
            name: '',
            category: '',
            price: '',
            original_price: '',
            stock: 0,
            status: true,
            description: ''
        },

        // ฟังก์ชันเปิด Modal เพิ่มสินค้า
        openAddModal() {
            this.isEditMode = false;
            this.formData = { name: '', category: '', price: '', original_price: '', stock: 0, status: true, description: '' };
            this.previewImage = null;
            this.showModal = true;
        },

        // ฟังก์ชันเปิด Modal แก้ไข (จำลองการดึงข้อมูล)
        openEditModal(product) {
            this.isEditMode = true;
            // ในการใช้งานจริง: บรรทัดนี้จะดึงข้อมูลจาก DB มาใส่
            this.formData = { 
                name: product.name, 
                category: 'T-Shirt', 
                price: 390, 
                original_price: 590, 
                stock: 50, 
                status: true,
                description: 'รายละเอียดสินค้า...' 
            };
            this.previewImage = product.image;
            this.showModal = true;
        },

        // ฟังก์ชันจำลองการอัปโหลดรูป (Preview)
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewImage = URL.createObjectURL(file);
            }
        }
    }" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">จัดการสินค้า (Products)</h1>
                <p class="text-gray-500 text-sm mt-1">รายการสินค้าทั้งหมด: <span class="font-bold text-emerald-600">124 รายการ</span></p>
            </div>
            <button @click="openAddModal()" 
                class="btn bg-emerald-600 hover:bg-emerald-700 text-white gap-2 shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                เพิ่มสินค้าใหม่
            </button>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
            {{-- Search --}}
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="ค้นหาชื่อสินค้า, SKU..." class="input input-bordered w-full pl-10 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            {{-- Filters --}}
            <div class="flex gap-3 w-full md:w-auto">
                <select class="select select-bordered w-full md:w-auto focus:ring-emerald-500">
                    <option disabled selected>หมวดหมู่ทั้งหมด</option>
                    <option>เสื้อยืด</option>
                    <option>กางเกง</option>
                    <option>หมวก</option>
                </select>
                <select class="select select-bordered w-full md:w-auto focus:ring-emerald-500">
                    <option disabled selected>สถานะ</option>
                    <option>วางขาย (Active)</option>
                    <option>ปิดการขาย (Draft)</option>
                    <option>สินค้าหมด (Out of Stock)</option>
                </select>
            </div>
        </div>

        {{-- Product Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    {{-- Table Head --}}
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs">
                        <tr>
                            <th class="py-4 pl-6">สินค้า</th>
                            <th>หมวดหมู่</th>
                            <th>ราคา</th>
                            <th>สต็อก</th>
                            <th>สถานะ</th>
                            <th class="text-right pr-6">จัดการ</th>
                        </tr>
                    </thead>
                    {{-- Table Body --}}
                    <tbody class="divide-y divide-gray-100">
                        {{-- Row 1 --}}
                        <tr class="hover:bg-gray-50 transition">
                            <td class="pl-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12 bg-gray-100">
                                            <img src="/images/T-Shirt-1.png" alt="Product" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">เสื้อยืด Oversize Cotton 100%</div>
                                        <div class="text-xs text-gray-500">SKU: HM-001-CR</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-ghost text-xs">เสื้อยืด</span></td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900">฿390</span>
                                    <span class="text-xs text-gray-400 line-through">฿590</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-emerald-600 font-bold">120 ชิ้น</span>
                            </td>
                            <td>
                                <label class="cursor-pointer label p-0 w-fit">
                                    <input type="checkbox" class="toggle toggle-success toggle-sm" checked />
                                </label>
                            </td>
                            <td class="text-right pr-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({name: 'เสื้อยืด Oversize', image: '/images/T-Shirt-1.png'})" class="btn btn-square btn-ghost btn-sm text-gray-500 hover:text-blue-600 hover:bg-blue-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="showDeleteModal = true" class="btn btn-square btn-ghost btn-sm text-gray-500 hover:text-red-600 hover:bg-red-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2 --}}
                        <tr class="hover:bg-gray-50 transition">
                            <td class="pl-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12 bg-gray-100">
                                            <img src="/images/T-Shirt-B.png" alt="Product" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">เสื้อยืด Oversize สีดำ</div>
                                        <div class="text-xs text-gray-500">SKU: HM-001-BK</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-ghost text-xs">เสื้อยืด</span></td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900">฿390</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-red-500 font-bold">2 ชิ้น</span>
                            </td>
                            <td>
                                <label class="cursor-pointer label p-0 w-fit">
                                    <input type="checkbox" class="toggle toggle-success toggle-sm" checked />
                                </label>
                            </td>
                            <td class="text-right pr-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEditModal({name: 'เสื้อยืด สีดำ', image: '/images/T-Shirt-B.png'})" class="btn btn-square btn-ghost btn-sm text-gray-500 hover:text-blue-600 hover:bg-blue-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="showDeleteModal = true" class="btn btn-square btn-ghost btn-sm text-gray-500 hover:text-red-600 hover:bg-red-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="flex justify-between items-center p-4 border-t border-gray-200 bg-gray-50">
                <span class="text-sm text-gray-500">แสดง 1-10 จาก 124 รายการ</span>
                <div class="join">
                    <button class="join-item btn btn-sm bg-white">«</button>
                    <button class="join-item btn btn-sm bg-emerald-600 text-white border-emerald-600">1</button>
                    <button class="join-item btn btn-sm bg-white">2</button>
                    <button class="join-item btn btn-sm bg-white">»</button>
                </div>
            </div>
        </div>


        {{-- ================= MODAL: ADD / EDIT PRODUCT ================= --}}
        <dialog class="modal" :class="showModal ? 'modal-open' : ''">
            <div class="modal-box w-11/12 max-w-4xl p-0 overflow-hidden bg-white">
                
                {{-- Modal Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800" x-text="isEditMode ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่'"></h3>
                    <button @click="showModal = false" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                {{-- Modal Body (Form) --}}
                <div class="p-6 md:p-8 overflow-y-auto max-h-[70vh]">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            
                            {{-- Left Column: Image Upload --}}
                            <div class="lg:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">รูปภาพสินค้า</label>
                                
                                <div class="relative w-full aspect-square bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 hover:border-emerald-500 transition group overflow-hidden">
                                    {{-- Preview Image --}}
                                    <template x-if="previewImage">
                                        <img :src="previewImage" class="absolute inset-0 w-full h-full object-cover">
                                    </template>

                                    {{-- Upload Placeholder --}}
                                    <div x-show="!previewImage" class="text-center p-4">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-sm text-gray-500 block">คลิกเพื่ออัปโหลด</span>
                                        <span class="text-xs text-gray-400 block mt-1">PNG, JPG สูงสุด 5MB</span>
                                    </div>
                                    <input type="file" class="absolute inset-0 opacity-0 cursor-pointer" @change="handleFileUpload">
                                </div>
                                <div class="mt-4 grid grid-cols-4 gap-2">
                                    {{-- Gallery Thumbs Mockup --}}
                                    <div class="aspect-square bg-gray-100 rounded-lg border border-gray-200"></div>
                                    <div class="aspect-square bg-gray-100 rounded-lg border border-gray-200"></div>
                                    <div class="aspect-square bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xl hover:text-emerald-600 cursor-pointer">+</div>
                                </div>
                            </div>

                            {{-- Right Column: Input Fields --}}
                            <div class="lg:col-span-2 space-y-5">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อสินค้า</label>
                                    <input type="text" x-model="formData.name" placeholder="เช่น เสื้อยืด Oversize Cotton 100%" class="input input-bordered w-full focus:ring-emerald-500 focus:border-emerald-500" required>
                                </div>

                                {{-- Category & Status --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                                        <select x-model="formData.category" class="select select-bordered w-full focus:ring-emerald-500">
                                            <option value="" disabled selected>เลือกหมวดหมู่</option>
                                            <option value="T-Shirt">เสื้อยืด</option>
                                            <option value="Pants">กางเกง</option>
                                            <option value="Accessories">เครื่องประดับ</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะสินค้า</label>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-sm" :class="formData.status ? 'text-gray-500' : 'text-gray-900 font-bold'">ปิด</span>
                                            <input type="checkbox" class="toggle toggle-success" x-model="formData.status" checked />
                                            <span class="text-sm" :class="formData.status ? 'text-emerald-600 font-bold' : 'text-gray-500'">เปิดขาย</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pricing & Stock --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ราคาเต็ม (ปกติ)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">฿</span>
                                            <input type="number" x-model="formData.original_price" placeholder="0.00" class="input input-bordered w-full pl-8">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">ราคาขาย (ลดราคา)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">฿</span>
                                            <input type="number" x-model="formData.price" placeholder="0.00" class="input input-bordered w-full pl-8 font-bold text-emerald-600" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">จำนวนสต็อก</label>
                                        <input type="number" x-model="formData.stock" placeholder="0" class="input input-bordered w-full" required>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียดสินค้า</label>
                                    <textarea x-model="formData.description" class="textarea textarea-bordered w-full h-32" placeholder="ใส่รายละเอียดสินค้า, ขนาด, วิธีดูแลรักษา..."></textarea>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                    <button @click="showModal = false" class="btn btn-ghost text-gray-500">ยกเลิก</button>
                    <button type="submit" class="btn bg-emerald-600 hover:bg-emerald-700 text-white gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        บันทึกข้อมูล
                    </button>
                </div>
            </div>
        </dialog>

        {{-- ================= MODAL: DELETE CONFIRMATION ================= --}}
        <dialog class="modal" :class="showDeleteModal ? 'modal-open' : ''">
            <div class="modal-box w-full max-w-sm text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">ยืนยันการลบสินค้า?</h3>
                <p class="text-sm text-gray-500 mt-2">คุณแน่ใจหรือไม่ที่จะลบสินค้านี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                <div class="modal-action justify-center mt-6 gap-3">
                    <button @click="showDeleteModal = false" class="btn btn-ghost text-gray-500">ยกเลิก</button>
                    <button @click="showDeleteModal = false" class="btn bg-red-600 hover:bg-red-700 text-white border-none">ยืนยันการลบ</button>
                </div>
            </div>
        </dialog>

    </div>
@endsection