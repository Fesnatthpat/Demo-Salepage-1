@extends('layout')

@section('content')
    <div x-data="orderManagement()" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header & Stats --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">จัดการคำสั่งซื้อ (Orders)</h1>
            <p class="text-gray-500 text-sm mt-1">ภาพรวมคำสั่งซื้อประจำวันนี้</p>
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-gray-500 text-xs">รอชำระเงิน</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-1">12</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-gray-500 text-xs">ที่ต้องจัดส่ง</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">5</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-gray-500 text-xs">จัดส่งแล้ว</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">28</div>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <div class="text-gray-500 text-xs">ยอดขายวันนี้</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">฿12,450</div>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-200 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            {{-- Tabs --}}
            <div class="tabs tabs-boxed bg-transparent p-0 w-full md:w-auto overflow-x-auto">
                <a class="tab" :class="filter === 'all' ? 'tab-active bg-emerald-600 text-white' : ''" @click="filter = 'all'">ทั้งหมด</a>
                <a class="tab" :class="filter === 'pending' ? 'tab-active bg-yellow-500 text-white' : ''" @click="filter = 'pending'">รอชำระ</a>
                <a class="tab" :class="filter === 'processing' ? 'tab-active bg-blue-500 text-white' : ''" @click="filter = 'processing'">เตรียมส่ง</a>
                <a class="tab" :class="filter === 'shipped' ? 'tab-active bg-emerald-600 text-white' : ''" @click="filter = 'shipped'">ส่งแล้ว</a>
                <a class="tab" :class="filter === 'cancelled' ? 'tab-active bg-red-500 text-white' : ''" @click="filter = 'cancelled'">ยกเลิก</a>
            </div>

            {{-- Search --}}
            <div class="relative w-full md:w-80 mr-2">
                <input type="text" x-model="search" placeholder="ค้นหาเลขคำสั่งซื้อ, ชื่อลูกค้า..." class="input input-bordered input-sm w-full pl-10 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-gray-50 text-gray-600 font-medium">
                        <tr>
                            <th>เลขคำสั่งซื้อ</th>
                            <th>ลูกค้า</th>
                            <th>ยอดรวม</th>
                            <th>การชำระเงิน</th>
                            <th>สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th class="text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="order in filteredOrders" :key="order.id">
                            <tr class="hover:bg-gray-50 transition cursor-pointer" @click="openModal(order)">
                                <td class="font-bold text-emerald-600" x-text="order.orderNumber"></td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral-focus text-neutral-content rounded-full w-8 h-8 flex items-center justify-center bg-gray-200 text-gray-600 text-xs">
                                                <span x-text="order.customer.name.substring(0,2)"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm" x-text="order.customer.name"></div>
                                            <div class="text-xs text-gray-500" x-text="order.customer.phone"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-bold" x-text="'฿' + order.total.toLocaleString()"></td>
                                <td>
                                    <div class="badge badge-sm gap-1" :class="order.paymentMethod === 'Bank Transfer' ? 'badge-outline' : 'badge-ghost'">
                                        <span x-text="order.paymentMethod"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge gap-1 border-none text-white text-xs py-3" 
                                          :class="{
                                              'bg-yellow-500': order.status === 'pending',
                                              'bg-blue-500': order.status === 'processing',
                                              'bg-emerald-500': order.status === 'shipped',
                                              'bg-red-500': order.status === 'cancelled'
                                          }">
                                        <span x-text="getStatusLabel(order.status)"></span>
                                    </span>
                                </td>
                                <td class="text-sm text-gray-500" x-text="order.date"></td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-square btn-ghost text-gray-500 hover:text-emerald-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MANAGEMENT MODAL ================= --}}
        <dialog class="modal" :class="showModal ? 'modal-open' : ''">
            <div class="modal-box w-11/12 max-w-5xl p-0 bg-gray-50 overflow-hidden" @click.outside="showModal = false">
                <template x-if="selectedOrder">
                    <div class="flex flex-col lg:flex-row h-full max-h-[85vh]">
                        
                        {{-- LEFT COLUMN: Order Details --}}
                        <div class="w-full lg:w-2/3 bg-white p-6 overflow-y-auto">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h3 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                                        คำสั่งซื้อ <span x-text="selectedOrder.orderNumber" class="text-emerald-600"></span>
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1" x-text="'สั่งซื้อเมื่อ: ' + selectedOrder.date"></p>
                                </div>
                                <span class="badge py-3 text-white border-none" 
                                      :class="{
                                          'bg-yellow-500': selectedOrder.status === 'pending',
                                          'bg-blue-500': selectedOrder.status === 'processing',
                                          'bg-emerald-500': selectedOrder.status === 'shipped',
                                          'bg-red-500': selectedOrder.status === 'cancelled'
                                      }" x-text="getStatusLabel(selectedOrder.status)"></span>
                            </div>

                            {{-- Product List --}}
                            <div class="border border-gray-100 rounded-lg overflow-hidden mb-6">
                                <table class="table w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th>สินค้า</th>
                                            <th class="text-center">ราคา</th>
                                            <th class="text-center">จำนวน</th>
                                            <th class="text-right">รวม</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in selectedOrder.items">
                                            <tr>
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <img :src="item.image" class="w-12 h-12 rounded border border-gray-200 object-cover">
                                                        <div>
                                                            <div class="font-bold text-sm" x-text="item.name"></div>
                                                            <div class="text-xs text-gray-500" x-text="item.option"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center" x-text="'฿' + item.price"></td>
                                                <td class="text-center" x-text="item.qty"></td>
                                                <td class="text-right font-bold" x-text="'฿' + (item.price * item.qty)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Payment Proof (Mockup) --}}
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start gap-4 mb-6" x-show="selectedOrder.status === 'pending'">
                                <div class="bg-white p-2 rounded border border-gray-200 shadow-sm cursor-pointer hover:scale-105 transition">
                                    <div class="w-16 h-24 bg-gray-200 flex items-center justify-center text-xs text-gray-500">สลิป</div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-blue-900">หลักฐานการชำระเงิน</h4>
                                    <p class="text-sm text-blue-700 mt-1">โอนเข้า: กสิกรไทย (001-x-xxxxx-x)</p>
                                    <p class="text-sm text-blue-700">เวลา: 14:30 น.</p>
                                    <button class="text-xs text-blue-600 underline mt-2">ดูรูปขยาย</button>
                                </div>
                            </div>

                            {{-- Totals --}}
                            <div class="flex justify-end">
                                <div class="w-64 space-y-2">
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>รวมการสั่งซื้อ</span>
                                        <span x-text="'฿' + selectedOrder.subtotal"></span>
                                    </div>
                                    <div class="flex justify-between text-sm text-gray-600">
                                        <span>ค่าจัดส่ง</span>
                                        <span x-text="'฿' + selectedOrder.shippingCost"></span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2 mt-2">
                                        <span>ยอดรวมสุทธิ</span>
                                        <span class="text-emerald-600" x-text="'฿' + selectedOrder.total.toLocaleString()"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Management Actions --}}
                        <div class="w-full lg:w-1/3 bg-gray-50 p-6 border-l border-gray-200 flex flex-col h-full">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-700">ข้อมูลลูกค้า</h4>
                                <button class="btn btn-xs btn-outline" @click="copyAddress()">Copy</button>
                            </div>
                            
                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6 text-sm">
                                <p class="font-bold text-gray-900 mb-1" x-text="selectedOrder.customer.name"></p>
                                <p class="text-gray-600 mb-1" x-text="selectedOrder.customer.address"></p>
                                <p class="text-emerald-600 font-bold" x-text="selectedOrder.customer.phone"></p>
                            </div>

                            <div class="divider">การจัดการ</div>

                            {{-- ACTION: Pending -> Processing --}}
                            <div x-show="selectedOrder.status === 'pending'" class="space-y-3">
                                <button @click="updateStatus('processing')" class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full shadow-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    ยืนยันการชำระเงิน
                                </button>
                                <button @click="updateStatus('cancelled')" class="btn btn-outline btn-error w-full">ยกเลิกคำสั่งซื้อ</button>
                            </div>

                            {{-- ACTION: Processing -> Shipped --}}
                            <div x-show="selectedOrder.status === 'processing'" class="space-y-4">
                                <div>
                                    <label class="label"><span class="label-text font-bold">บริษัทขนส่ง</span></label>
                                    <select class="select select-bordered w-full bg-white" x-model="trackingCourier">
                                        <option>Flash Express</option>
                                        <option>Kerry Express</option>
                                        <option>J&T Express</option>
                                        <option>Thai Post</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text font-bold">เลขพัสดุ (Tracking No.)</span></label>
                                    <input type="text" x-model="trackingNumber" placeholder="เช่น TH012345678" class="input input-bordered w-full bg-white focus:ring-emerald-500">
                                </div>
                                
                                <div class="form-control">
                                    <label class="cursor-pointer label justify-start gap-3">
                                        <input type="checkbox" checked="checked" class="checkbox checkbox-success checkbox-sm" />
                                        <span class="label-text text-xs">ส่ง SMS/LINE แจ้งลูกค้าอัตโนมัติ</span>
                                    </label>
                                </div>

                                <button @click="updateStatus('shipped')" class="btn bg-blue-600 hover:bg-blue-700 text-white w-full shadow-md" :disabled="!trackingNumber">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    ยืนยันการจัดส่ง
                                </button>
                            </div>

                            {{-- ACTION: Shipped / Completed --}}
                            <div x-show="selectedOrder.status === 'shipped'" class="space-y-4 text-center">
                                <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg border border-emerald-100">
                                    <p class="text-sm font-bold">จัดส่งแล้ว</p>
                                    <p class="text-xs mt-1">Tracking: <span x-text="trackingNumber || 'TH123456789'"></span></p>
                                </div>
                                <button @click="updateStatus('completed')" class="btn btn-outline btn-success w-full">ทำรายการเสร็จสิ้น</button>
                            </div>

                            <div class="mt-auto pt-6 flex justify-center">
                                <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 underline text-sm">ปิดหน้าต่าง</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </dialog>

    </div>

    <script>
        function orderManagement() {
            return {
                filter: 'all',
                search: '',
                showModal: false,
                selectedOrder: null,
                trackingNumber: '',
                trackingCourier: 'Flash Express',
                
                // Mock Data (ข้อมูลจำลอง)
                orders: [
                    {
                        id: 1,
                        orderNumber: '#ORD-2568001',
                        customer: { name: 'สมชาย ใจดี', phone: '081-234-5678', address: '99/9 ถ.สุขุมวิท เขตวัฒนา กทม 10110' },
                        total: 12450,
                        subtotal: 12400,
                        shippingCost: 50,
                        paymentMethod: 'Bank Transfer',
                        status: 'pending',
                        date: '09 ธ.ค. 2025',
                        items: [
                            { name: 'เสื้อยืด Oversize', option: 'สีดำ, XL', price: 390, qty: 2, image: '/images/T-Shirt-B.png' },
                            { name: 'กางเกงยีนส์', option: 'เอว 32', price: 990, qty: 1, image: '/images/T-Shirt-1.png' }
                        ]
                    },
                    {
                        id: 2,
                        orderNumber: '#ORD-2568002',
                        customer: { name: 'วิภาดา รักสวย', phone: '089-999-8888', address: '12 หมู่ 5 ต.สุเทพ อ.เมือง จ.เชียงใหม่ 50200' },
                        total: 390,
                        subtotal: 390,
                        shippingCost: 0,
                        paymentMethod: 'Credit Card',
                        status: 'processing',
                        date: '09 ธ.ค. 2025',
                        items: [
                            { name: 'หมวกแก๊ป', option: 'สีขาว', price: 390, qty: 1, image: '/images/T-Shirt-W.png' }
                        ]
                    },
                    {
                        id: 3,
                        orderNumber: '#ORD-2567999',
                        customer: { name: 'กิตติพงศ์', phone: '090-000-1111', address: '55/5 คอนโดลุมพินี กทม.' },
                        total: 780,
                        subtotal: 780,
                        shippingCost: 0,
                        paymentMethod: 'COD',
                        status: 'shipped',
                        date: '08 ธ.ค. 2025',
                        items: [
                             { name: 'เสื้อยืด Oversize', option: 'สีครีม, M', price: 390, qty: 2, image: '/images/T-Shirt-1.png' }
                        ]
                    }
                ],

                get filteredOrders() {
                    return this.orders.filter(order => {
                        const statusMatch = this.filter === 'all' || order.status === this.filter;
                        const searchMatch = order.orderNumber.toLowerCase().includes(this.search.toLowerCase()) || 
                                          order.customer.name.toLowerCase().includes(this.search.toLowerCase());
                        return statusMatch && searchMatch;
                    });
                },

                getStatusLabel(status) {
                    const labels = {
                        'pending': 'รอชำระเงิน',
                        'processing': 'กำลังเตรียมจัดส่ง',
                        'shipped': 'จัดส่งแล้ว',
                        'completed': 'สำเร็จ',
                        'cancelled': 'ยกเลิก'
                    };
                    return labels[status] || status;
                },

                openModal(order) {
                    this.selectedOrder = order;
                    this.trackingNumber = ''; // Reset tracking
                    this.showModal = true;
                },

                updateStatus(newStatus) {
                    if (this.selectedOrder) {
                        this.selectedOrder.status = newStatus;
                        // ถ้าเป็นการจัดส่ง ให้ปิด Modal จำลองการเสร็จสิ้น
                        if(newStatus === 'shipped') {
                            alert(`บันทึกสถานะจัดส่งเรียบร้อย! Tracking: ${this.trackingNumber}`);
                            this.showModal = false;
                        }
                    }
                },
                
                copyAddress() {
                    const text = `${this.selectedOrder.customer.name}\n${this.selectedOrder.customer.phone}\n${this.selectedOrder.customer.address}`;
                    navigator.clipboard.writeText(text);
                    alert('คัดลอกที่อยู่เรียบร้อย');
                }
            }
        }
    </script>
@endsection