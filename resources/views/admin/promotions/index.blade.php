@extends('layouts.admin')

@section('title', 'จัดการโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="{
        activeTab: '{{ $type }}',
        copyCode(code) {
            navigator.clipboard.writeText(code);
        },
        showDeleteModal: false,
        deleteFormAction: '',
        promotionNameToDelete: '',
        openDeleteModal(actionUrl, promoName) {
            this.deleteFormAction = actionUrl;
            this.promotionNameToDelete = promoName;
            this.showDeleteModal = true;
        }
    }">
        {{-- Header & Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <div class="p-2.5 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl border border-emerald-500/30">
                        <i class="fas fa-bullhorn text-emerald-400 text-xl"></i>
                    </div>
                    Campaign Manager
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-medium">จัดการแคมเปญส่วนลด คูปอง และโปรโมชั่นของแถม</p>
            </div>
            <a href="{{ route('admin.promotions.create') }}"
                class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-300 bg-emerald-600 rounded-xl hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_25px_rgba(16,185,129,0.5)] overflow-hidden active:scale-95">
                <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                <i class="fas fa-plus mr-2 transition-transform group-hover:rotate-90"></i> สร้างแคมเปญใหม่
            </a>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up">
            <div class="bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-emerald-500/20 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 shadow-inner">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">ใช้งานอยู่ (Active)</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $promotions->where('is_active', true)->count() }}</p>
                </div>
            </div>

            <div class="bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-all duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-blue-500/20 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20 shadow-inner">
                    <i class="fas fa-ticket-alt text-2xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">รหัสโค้ด (Manual Code)</p>
                    <p class="text-3xl font-black text-white mt-1">{{ $promotions->whereNotNull('code')->count() }}</p>
                </div>
            </div>

            <div class="bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 flex items-center gap-5 shadow-lg relative overflow-hidden group hover:border-pink-500/50 transition-all duration-300">
                <div class="absolute right-0 top-0 w-32 h-32 bg-pink-500/10 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-pink-500/20 transition-colors"></div>
                <div class="w-14 h-14 rounded-2xl bg-pink-500/10 flex items-center justify-center text-pink-400 border border-pink-500/20 shadow-inner">
                    <i class="fas fa-gifts text-2xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">โปรฯ ของแถม (BxGy)</p>
                    <p class="text-3xl font-black text-white mt-1">
                        {{ $promotions->filter(fn($p) => $p->rules->count() > 0)->count() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Category Tabs & Filters --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-gray-800/50 p-3 rounded-2xl border border-gray-700/50 backdrop-blur-sm">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.promotions.index', ['type' => 'all', 'status' => $status]) }}" 
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all {{ $type === 'all' ? 'bg-gray-700 text-white shadow-md' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">ทั้งหมด</a>
                <a href="{{ route('admin.promotions.index', ['type' => 'coupon', 'status' => $status]) }}" 
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $type === 'coupon' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-900/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                    <i class="fas fa-bolt text-xs opacity-70"></i> คูปองอัตโนมัติ
                </a>
                <a href="{{ route('admin.promotions.index', ['type' => 'code', 'status' => $status]) }}" 
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $type === 'code' ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                    <i class="fas fa-ticket-alt text-xs opacity-70"></i> รหัสโค้ด
                </a>
                <a href="{{ route('admin.promotions.index', ['type' => 'bxgy', 'status' => $status]) }}" 
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $type === 'bxgy' ? 'bg-pink-600 text-white shadow-md shadow-pink-900/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                    <i class="fas fa-gifts text-xs opacity-70"></i> ซื้อ X แถม Y
                </a>
                <a href="{{ route('admin.promotions.index', ['type' => 'birthday', 'status' => $status]) }}" 
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $type === 'birthday' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'text-gray-400 hover:bg-gray-700/50 hover:text-white' }}">
                    <i class="fas fa-birthday-cake text-xs opacity-70"></i> โปรฯ วันเกิด
                </a>
            </div>

            <div class="flex items-center gap-2 bg-gray-900/50 p-1.5 rounded-xl border border-gray-700">
                <span class="text-[10px] font-black text-gray-500 uppercase px-2">สถานะ</span>
                <a href="{{ route('admin.promotions.index', ['type' => $type, 'status' => 'all']) }}"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'all' ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700/50' }}">ทั้งหมด</a>
                <a href="{{ route('admin.promotions.index', ['type' => $type, 'status' => 'active']) }}"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'text-gray-400 hover:bg-gray-700/50' }}">
                    <div class="w-2 h-2 rounded-full {{ $status === 'active' ? 'bg-emerald-400 shadow-[0_0_5px_#34d399]' : 'bg-gray-500' }}"></div> เปิดใช้งาน
                </a>
                <a href="{{ route('admin.promotions.index', ['type' => $type, 'status' => 'inactive']) }}"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $status === 'inactive' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'text-gray-400 hover:bg-gray-700/50' }}">
                    <div class="w-2 h-2 rounded-full {{ $status === 'inactive' ? 'bg-red-400' : 'bg-gray-500' }}"></div> ปิดใช้งาน
                </a>
            </div>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="flex items-center p-4 mb-4 text-emerald-400 rounded-xl bg-emerald-900/20 border border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.15)] backdrop-blur-sm" role="alert">
                <div class="p-2 bg-emerald-500/20 rounded-lg mr-3"><i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i></div>
                <div class="text-sm font-bold">{{ session('success') }}</div>
                <button @click="show = false" type="button" class="ml-auto bg-transparent text-emerald-400 rounded-lg p-1.5 hover:bg-emerald-900/40 inline-flex h-8 w-8 items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-gray-800/90 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-700/50 overflow-hidden animate-fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900/60 border-b border-gray-700/50 text-[10px] uppercase text-gray-400 font-black tracking-widest">
                            <th class="px-6 py-5">แคมเปญ</th>
                            <th class="px-6 py-5 text-center">ประเภท</th>
                            <th class="px-6 py-5 text-center">มูลค่า/เงื่อนไข</th>
                            <th class="px-6 py-5 text-center">การใช้งาน</th>
                            <th class="px-6 py-5 text-center">สถานะ</th>
                            <th class="px-6 py-5 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse ($promotions as $promo)
                            <tr class="hover:bg-gray-700/30 transition-colors duration-200 group">
                                {{-- Name --}}
                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors">{{ $promo->name }}</span>
                                            @if($promo->birthdayPromotion)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 tracking-wider">
                                                    <i class="fas fa-birthday-cake mr-1"></i> BIRTHDAY
                                                </span>
                                            @endif
                                        </div>
                                        @if ($promo->description)
                                            <span class="text-xs text-gray-400 line-clamp-1 mb-2">{{ $promo->description }}</span>
                                        @endif
                                        <div class="flex items-center gap-2 mt-auto text-[10px] text-gray-400 font-mono bg-gray-900/60 w-fit px-2.5 py-1 rounded-md border border-gray-700/50">
                                            <i class="far fa-clock text-gray-500"></i>
                                            <span>
                                                {{ $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('d M y') : 'Now' }}
                                                <i class="fas fa-arrow-right mx-1.5 text-gray-600"></i>
                                                {{ $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('d M y') : '∞' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type Badge --}}
                                <td class="px-6 py-5 text-center align-middle">
                                    @if ($promo->is_free_shipping)
                                        @if ($promo->code)
                                            <button @click="copyCode('{{ $promo->code }}')" class="group/btn inline-flex flex-col items-center gap-1.5 cursor-pointer transition-transform active:scale-95" title="คลิกเพื่อคัดลอก">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-400 border border-purple-500/20 group-hover/btn:bg-purple-500/20 transition-all"><i class="fas fa-shipping-fast"></i> รหัสส่งฟรี</span>
                                                <span class="font-mono text-sm font-bold text-gray-200 group-hover/btn:text-white bg-gray-900 px-2 py-0.5 rounded border border-gray-700/50">{{ $promo->code }}</span>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-500/10 text-purple-400 border border-purple-500/20"><i class="fas fa-shipping-fast"></i> ส่งฟรี (Auto)</span>
                                        @endif
                                    @elseif ($promo->code)
                                        <button @click="copyCode('{{ $promo->code }}')" class="group/btn inline-flex flex-col items-center gap-1.5 cursor-pointer transition-transform active:scale-95" title="คลิกเพื่อคัดลอก">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 group-hover/btn:bg-blue-500/20 transition-all"><i class="fas fa-ticket-alt"></i> รหัสส่วนลด</span>
                                            <span class="font-mono text-sm font-bold text-gray-200 group-hover/btn:text-white bg-gray-900 px-2 py-0.5 rounded border border-gray-700/50">{{ $promo->code }}</span>
                                        </button>
                                    @elseif($promo->rules->count() > 0)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-pink-500/10 text-pink-400 border border-pink-500/20"><i class="fas fa-gift"></i> Buy X Get Y</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><i class="fas fa-bolt"></i> คูปองอัตโนมัติ</span>
                                    @endif
                                </td>

                                {{-- Value --}}
                                <td class="px-6 py-5 text-center align-middle">
                                    @if (isset($promo->discount_value))
                                        <div class="flex flex-col items-center">
                                            <span class="text-white font-mono text-xl font-bold tracking-tight">
                                                {{ number_format($promo->discount_value, 0) }}<span class="text-xs text-gray-400 ml-0.5">{{ $promo->discount_type === 'percentage' ? '%' : '฿' }}</span>
                                            </span>
                                            @if ($promo->min_order_value > 0)
                                                <span class="text-[10px] text-gray-400 bg-gray-900/80 border border-gray-700 px-2 py-0.5 rounded-md mt-1 font-medium">ขั้นต่ำ ฿{{ number_format($promo->min_order_value) }}</span>
                                            @endif
                                        </div>
                                    @elseif($promo->rules->count() > 0)
                                        <div class="flex items-center justify-center gap-2 text-xs">
                                            <div class="flex flex-col items-center bg-gray-900 px-2.5 py-1 rounded-lg border border-gray-700">
                                                <span class="text-gray-500 text-[9px] font-black uppercase">Buy</span>
                                                <span class="text-white font-bold">{{ $promo->rules->sum('quantity') }}</span>
                                            </div>
                                            <i class="fas fa-plus text-gray-600 text-[10px]"></i>
                                            <div class="flex flex-col items-center bg-pink-500/10 px-2.5 py-1 rounded-lg border border-pink-500/20">
                                                <span class="text-pink-500 text-[9px] font-black uppercase">Get</span>
                                                <span class="text-pink-400 font-bold">{{ $promo->actions->sum('quantity') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-600">-</span>
                                    @endif
                                </td>

                                {{-- Usage --}}
                                <td class="px-6 py-5 align-middle">
                                    <div class="flex flex-col gap-3">
                                        <div class="flex justify-center gap-4">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] text-gray-500 uppercase font-bold tracking-widest mb-0.5">ใช้ไปแล้ว</span>
                                                <span class="text-emerald-400 font-bold text-sm">{{ number_format($promo->usage_count ?? 0) }}</span>
                                            </div>
                                            <div class="w-px h-8 bg-gray-700/50"></div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] text-gray-500 uppercase font-bold tracking-widest mb-0.5">ผู้ใช้</span>
                                                <span class="text-blue-400 font-bold text-sm">{{ number_format($promo->unique_users_count ?? 0) }}</span>
                                            </div>
                                        </div>

                                        @if ($promo->usage_limit)
                                            <div class="w-full max-w-[140px] mx-auto">
                                                <div class="w-full bg-gray-700/50 rounded-full h-1.5 overflow-hidden">
                                                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-1.5 rounded-full transition-all duration-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"
                                                        style="width: {{ min(100, (($promo->usage_count ?? 0) / $promo->usage_limit) * 100) }}%">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-[9px] font-bold text-gray-500 mt-1.5 uppercase">
                                                    <span>Limit</span>
                                                    <span>{{ number_format($promo->usage_limit) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <a href="{{ route('admin.promotions.logs', ['promotion' => $promo->name]) }}" class="text-[10px] text-emerald-500/70 hover:text-emerald-400 hover:underline transition-colors font-medium">ดูประวัติการใช้ <i class="fas fa-angle-right ml-0.5"></i></a>
                                        </div>
                                    </div>
                                </td>

                                {{-- Status Toggle --}}
                                <td class="px-6 py-5 text-center align-middle" x-data="{ isActive: {{ $promo->is_active ? 'true' : 'false' }}, isToggling: false }">
                                    <button type="button"
                                        @click="async () => {
                                            if(isToggling) return;
                                            isToggling = true;
                                            try {
                                                const res = await fetch(`/admin/promotions/{{ $promo->id }}/toggle-status`, {
                                                    method: 'POST',
                                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                });
                                                if(res.ok) {
                                                    const data = await res.json();
                                                    isActive = data.is_active;
                                                } else throw new Error('Error');
                                            } catch(e) { alert('ไม่สามารถอัปเดตสถานะได้'); } finally { isToggling = false; }
                                        }"
                                        :disabled="isToggling"
                                        class="relative inline-flex items-center w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                                        :class="isActive ? 'bg-emerald-500' : 'bg-gray-600'" title="เปิด/ปิด แคมเปญ">
                                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform duration-300 shadow-md" :class="isActive ? 'translate-x-7' : 'translate-x-1'"></span>
                                        <div x-show="isToggling" class="absolute inset-0 flex items-center justify-center bg-gray-900/50 rounded-full backdrop-blur-sm"><i class="fas fa-circle-notch fa-spin text-white text-[10px]"></i></div>
                                    </button>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-5 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.promotions.edit', $promo->id) }}" class="w-9 h-9 flex items-center justify-center text-gray-400 bg-gray-700/50 hover:text-white hover:bg-indigo-600 rounded-xl transition-all shadow-sm" title="แก้ไข"><i class="fas fa-pen text-sm"></i></a>
                                        <button type="button" @click="openDeleteModal('{{ route('admin.promotions.destroy', $promo->id) }}', '{{ addslashes($promo->name) }}')" class="w-9 h-9 flex items-center justify-center text-gray-400 bg-gray-700/50 hover:text-white hover:bg-red-600 rounded-xl transition-all shadow-sm" title="ลบ"><i class="fas fa-trash text-sm"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-inner border border-gray-700">
                                            <i class="fas fa-folder-open text-4xl opacity-30"></i>
                                        </div>
                                        <p class="text-lg font-bold text-gray-300">ยังไม่มีข้อมูลโปรโมชั่น</p>
                                        <p class="text-sm mt-1 text-gray-500">สร้างแคมเปญใหม่เพื่อกระตุ้นยอดขายกันเลย</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (method_exists($promotions, 'hasPages') && $promotions->hasPages())
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Delete --}}
        <div x-show="showDeleteModal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showDeleteModal" @click.away="showDeleteModal = false"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-700">
                        <form :action="deleteFormAction" method="POST">
                            @csrf @method('DELETE')
                            <div class="px-6 pt-8 pb-6">
                                <div class="flex flex-col items-center text-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10 border border-red-500/20 mb-4">
                                        <i class="fas fa-trash-alt text-2xl text-red-500"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-2">ยืนยันการลบแคมเปญ</h3>
                                    <p class="text-sm text-gray-400">คุณแน่ใจหรือไม่ว่าต้องการลบแคมเปญ <br><span class="font-bold text-white text-base mt-1 block" x-text="promotionNameToDelete"></span></p>
                                    <p class="text-[10px] text-red-400 mt-4 bg-red-500/10 px-3 py-2 rounded-lg border border-red-500/20 w-full"><i class="fas fa-info-circle mr-1"></i> ข้อมูลและประวัติการใช้ทั้งหมดจะหายไปและไม่สามารถกู้คืนได้</p>
                                </div>
                            </div>
                            <div class="bg-gray-900/50 px-6 py-4 flex gap-3 border-t border-gray-700">
                                <button type="button" @click="showDeleteModal = false" class="flex-1 rounded-xl bg-gray-700 px-4 py-3 text-sm font-bold text-white hover:bg-gray-600 transition-colors">ยกเลิก</button>
                                <button type="submit" class="flex-1 rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-500 shadow-[0_0_15px_rgba(220,38,38,0.3)] transition-colors">ยืนยันการลบ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection