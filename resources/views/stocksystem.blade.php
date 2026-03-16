@extends('layout')

@section('content')
    <div x-data="inventorySystem()" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">คลังสินค้า (Inventory)</h1>
                <p class="text-gray-500 text-sm mt-1">จัดการจำนวนสินค้า ตรวจสอบสต็อก และประวัติการเคลื่อนไหว</p>
            </div>
            <div class="flex gap-2">
                <button @click="activeTab = 'history'" class="btn btn-outline gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ดูประวัติย้อนหลัง
                </button>
                <button @click="openAdjustModal()" class="btn bg-emerald-600 hover:bg-emerald-700 text-white gap-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    ปรับสต็อกด่วน
                </button>
            </div>
        </div>

        {{-- Stats Cards (Real-time Alerts) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Items --}}
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">รายการสินค้าทั้งหมด</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1" x-text="products.length"></h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>

            {{-- Low Stock Alert --}}
            <div class="bg-white p-5 rounded-xl shadow-sm border border-red-100 relative overflow-hidden">
                <div class="absolute right-0 top-0 p-3 opacity-10">
                    <svg class="w-24 h-24 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                </div>
                <div>
                    <p class="text-red-500 text-sm font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        สินค้าใกล้หมด (Low Stock)
                    </p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1" x-text="lowStockCount"></h3>
                    <p class="text-xs text-gray-400 mt-1">ควรเติมสต็อกทันที</p>
                </div>
            </div>

            {{-- Out of Stock --}}
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">สินค้าหมด (Out of Stock)</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1" x-text="outOfStockCount"></h3>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="mb-6 border-b border-gray-200">
            <div class="flex gap-6">
                <button @click="activeTab = 'stock'" 
                    class="pb-3 text-sm font-medium transition-colors relative"
                    :class="activeTab === 'stock' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                    สต็อกปัจจุบัน (Current Stock)
                </button>
                <button @click="activeTab = 'history'" 
                    class="pb-3 text-sm font-medium transition-colors relative"
                    :class="activeTab === 'history' ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-gray-500 hover:text-gray-700'">
                    ประวัติการเข้า-ออก (History Logs)
                </button>
            </div>
        </div>

        {{-- TAB 1: STOCK OVERVIEW --}}
        <div x-show="activeTab === 'stock'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            {{-- Filter Bar --}}
            <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row gap-4 justify-between">
                <div class="relative w-full md:w-80">
                    <input type="text" x-model="search" placeholder="ค้นหาชื่อสินค้า, SKU..." class="input input-bordered w-full pl-10 focus:ring-emerald-500">
                    <svg class="w-4 h-4 absolute left-3 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm" :class="filterStatus === 'all' ? 'btn-active' : 'btn-ghost'" @click="filterStatus = 'all'">ทั้งหมด</button>
                    <button class="btn btn-sm text-red-600" :class="filterStatus === 'low' ? 'bg-red-50 border-red-200' : 'btn-ghost'" @click="filterStatus = 'low'">ใกล้หมด</button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th>สินค้า</th>
                            <th>SKU</th>
                            <th class="w-1/4">ระดับสต็อก (Stock Level)</th>
                            <th class="text-center">จำนวนคงเหลือ</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="product in filteredProducts" :key="product.id">
                            <tr class="hover:bg-gray-50 group">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-10 h-10 bg-gray-100">
                                                <img :src="product.image" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800" x-text="product.name"></div>
                                            <div class="text-xs text-gray-500" x-text="product.category"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-xs font-mono text-gray-500" x-text="product.sku"></td>
                                
                                {{-- Visual Stock Bar --}}
                                <td>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-200">
                                        <div class="h-2.5 rounded-full transition-all duration-500" 
                                             :class="{
                                                'bg-emerald-500': product.stock > 20,
                                                'bg-yellow-400': product.stock <= 20 && product.stock > 10,
                                                'bg-red-500': product.stock <= 10
                                             }"
                                             :style="'width: ' + Math.min((product.stock / 100) * 100, 100) + '%'"></div>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1 flex justify-between">
                                        <span>0</span>
                                        <span x-text="'Min: ' + product.minStock"></span>
                                        <span>100+</span>
                                    </div>
                                </td>

                                <td class="text-center font-bold text-lg" 
                                    :class="product.stock <= product.minStock ? 'text-red-600' : 'text-gray-800'" 
                                    x-text="product.stock">
                                </td>

                                <td class="text-center">
                                    <span class="badge badge-sm border-none text-white"
                                          :class="{
                                              'bg-emerald-500': product.stock > product.minStock,
                                              'bg-red-500': product.stock <= product.minStock && product.stock > 0,
                                              'bg-gray-500': product.stock === 0
                                          }">
                                        <span x-text="product.stock === 0 ? 'หมด' : (product.stock <= product.minStock ? 'ใกล้หมด' : 'ปกติ')"></span>
                                    </span>
                                </td>

                                <td class="text-right">
                                    <button @click="openAdjustModal(product)" class="btn btn-sm btn-ghost text-emerald-600 hover:bg-emerald-50">
                                        ปรับสต็อก
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 2: HISTORY LOGS --}}
        <div x-show="activeTab === 'history'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">ประวัติการเคลื่อนไหวล่าสุด</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>วันที่/เวลา</th>
                            <th>สินค้า</th>
                            <th>รายการ (Action)</th>
                            <th class="text-center">จำนวน</th>
                            <th>คงเหลือ</th>
                            <th>ผู้ทำรายการ</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="log in logs" :key="log.id">
                            <tr class="hover:bg-gray-50">
                                <td class="text-sm text-gray-500" x-text="log.date"></td>
                                <td class="font-bold text-gray-800" x-text="log.productName"></td>
                                <td>
                                    <span class="badge badge-sm gap-1" 
                                          :class="{
                                              'badge-success badge-outline': log.type === 'in',
                                              'badge-error badge-outline': log.type === 'out',
                                              'badge-warning badge-outline': log.type === 'adjust'
                                          }">
                                        <span x-text="log.type === 'in' ? 'รับเข้า' : (log.type === 'out' ? 'ขายออก' : 'ปรับยอด')"></span>
                                    </span>
                                </td>
                                <td class="text-center font-bold" 
                                    :class="log.type === 'in' ? 'text-emerald-600' : 'text-red-600'" 
                                    x-text="(log.type === 'in' ? '+' : '-') + log.qty">
                                </td>
                                <td class="text-gray-600" x-text="log.balance"></td>
                                <td class="text-xs text-gray-500">Admin</td>
                                <td class="text-xs text-gray-400 truncate max-w-xs" x-text="log.note"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MODAL: ADJUST STOCK ================= --}}
        <dialog class="modal" :class="showModal ? 'modal-open' : ''">
            <div class="modal-box bg-white">
                <h3 class="font-bold text-lg mb-4 text-gray-800">ปรับปรุงสต็อกสินค้า</h3>
                
                <div class="flex items-center gap-4 mb-6 bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <img :src="selectedProduct?.image" class="w-12 h-12 rounded bg-white object-cover">
                    <div>
                        <div class="font-bold text-gray-900" x-text="selectedProduct?.name"></div>
                        <div class="text-sm text-gray-500">คงเหลือปัจจุบัน: <span class="font-bold text-emerald-600" x-text="selectedProduct?.stock"></span> ชิ้น</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <button @click="adjustType = 'add'" 
                            class="btn flex-1" 
                            :class="adjustType === 'add' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'btn-outline text-gray-500'">
                        + รับเข้า (Add)
                    </button>
                    <button @click="adjustType = 'remove'" 
                            class="btn flex-1" 
                            :class="adjustType === 'remove' ? 'bg-red-600 text-white hover:bg-red-700' : 'btn-outline text-gray-500'">
                        - นำออก (Remove)
                    </button>
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">จำนวน</span></label>
                    <input type="number" x-model.number="adjustQty" class="input input-bordered w-full text-lg font-bold" min="1">
                </div>

                <div class="form-control w-full mb-6">
                    <label class="label"><span class="label-text">เหตุผล / หมายเหตุ</span></label>
                    <input type="text" x-model="adjustNote" placeholder="เช่น สินค้าชำรุด, เติมของ, เช็คสต็อก" class="input input-bordered w-full">
                </div>

                <div class="modal-action">
                    <button @click="showModal = false" class="btn btn-ghost text-gray-500">ยกเลิก</button>
                    <button @click="saveAdjustment()" class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-24">บันทึก</button>
                </div>
            </div>
        </dialog>

    </div>

    <script>
        function inventorySystem() {
            return {
                activeTab: 'stock',
                search: '',
                filterStatus: 'all', // all, low
                showModal: false,
                
                // Adjustment Form Data
                selectedProduct: null,
                adjustType: 'add',
                adjustQty: 1,
                adjustNote: '',

                // Mock Data Products
                products: [
                    { id: 1, name: 'เสื้อยืด Oversize', sku: 'HM-001', category: 'เสื้อผ้า', stock: 120, minStock: 20, image: '/images/T-Shirt-1.png' },
                    { id: 2, name: 'กางเกงยีนส์ Slim', sku: 'PT-002', category: 'กางเกง', stock: 8, minStock: 10, image: '/images/T-Shirt-B.png' },
                    { id: 3, name: 'หมวกแก๊ป', sku: 'AC-005', category: 'เครื่องประดับ', stock: 0, minStock: 5, image: '/images/T-Shirt-W.png' },
                    { id: 4, name: 'เสื้อเชิ้ตลายสก็อต', sku: 'SH-003', category: 'เสื้อผ้า', stock: 45, minStock: 15, image: '/images/T-Shirt-1.png' },
                ],

                // Mock Data Logs
                logs: [
                    { id: 1, date: '09/12/2025 10:30', productName: 'เสื้อยืด Oversize', type: 'out', qty: 2, balance: 120, note: 'Order #ORD-2568001' },
                    { id: 2, date: '08/12/2025 15:45', productName: 'กางเกงยีนส์ Slim', type: 'in', qty: 20, balance: 25, note: 'เติมสต็อก' },
                    { id: 3, date: '08/12/2025 09:00', productName: 'หมวกแก๊ป', type: 'out', qty: 5, balance: 0, note: 'สินค้าเสียหาย' },
                ],

                get filteredProducts() {
                    return this.products.filter(p => {
                        const searchMatch = p.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                          p.sku.toLowerCase().includes(this.search.toLowerCase());
                        
                        if (this.filterStatus === 'low') {
                            return searchMatch && (p.stock <= p.minStock);
                        }
                        return searchMatch;
                    });
                },

                get lowStockCount() {
                    return this.products.filter(p => p.stock <= p.minStock && p.stock > 0).length;
                },

                get outOfStockCount() {
                    return this.products.filter(p => p.stock === 0).length;
                },

                openAdjustModal(product) {
                    if (product) {
                        this.selectedProduct = product;
                    } else {
                        // กรณีเปิดจากปุ่ม "ปรับสต็อกด่วน" ด้านบน อาจจะให้เลือกสินค้าก่อน (ในตัวอย่างนี้ข้ามไปก่อน)
                        this.selectedProduct = this.products[0]; 
                    }
                    this.adjustQty = 1;
                    this.adjustNote = '';
                    this.adjustType = 'add';
                    this.showModal = true;
                },

                saveAdjustment() {
                    if (!this.selectedProduct) return;

                    // 1. Update Stock Logic (Mockup)
                    if (this.adjustType === 'add') {
                        this.selectedProduct.stock += this.adjustQty;
                    } else {
                        if (this.selectedProduct.stock < this.adjustQty) {
                            alert('จำนวนสินค้าไม่พอให้ตัดสต็อก');
                            return;
                        }
                        this.selectedProduct.stock -= this.adjustQty;
                    }

                    // 2. Add to Log (Mockup)
                    this.logs.unshift({
                        id: Date.now(),
                        date: new Date().toLocaleString('th-TH'),
                        productName: this.selectedProduct.name,
                        type: this.adjustType === 'add' ? 'in' : 'out',
                        qty: this.adjustQty,
                        balance: this.selectedProduct.stock,
                        note: this.adjustNote || (this.adjustType === 'add' ? 'เติมสต็อก' : 'ปรับยอดออก')
                    });

                    // 3. Close Modal & Alert
                    this.showModal = false;
                    alert('บันทึกการปรับสต็อกเรียบร้อย');
                }
            }
        }
    </script>
@endsection