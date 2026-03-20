@extends('layouts.admin')

@section('title', 'จัดการโปรโมชั่นวันเกิด')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="{
        showDeleteModal: false,
        deleteFormAction: '',
        promotionTitleToDelete: '',
        openDeleteModal(actionUrl, title) {
            this.deleteFormAction = actionUrl;
            this.promotionTitleToDelete = title;
            this.showDeleteModal = true;
        }
    }">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
                    <div
                        class="w-12 h-12 rounded-xl bg-pink-500/20 flex items-center justify-center shadow-lg shadow-pink-500/10">
                        <i class="fas fa-birthday-cake text-pink-400 text-2xl"></i>
                    </div>
                    Birthday Campaigns
                </h1>
                <p class="text-gray-400 text-sm mt-1">จัดการข้อความและโปรโมชั่นที่จะส่งให้ลูกค้าในวันเกิดอัตโนมัติ</p>
            </div>
            <a href="{{ route('admin.birthday-promotion.create') }}"
                class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-pink-600 rounded-xl hover:bg-pink-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-600 shadow-lg shadow-pink-900/30 overflow-hidden">
                <span
                    class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                <i class="fas fa-plus mr-2 transition-transform group-hover:rotate-90"></i> สร้างแคมเปญวันเกิดใหม่
            </a>
        </div>

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                class="flex items-center p-4 mb-4 text-emerald-400 rounded-xl bg-emerald-900/20 border border-emerald-500/20 shadow-lg backdrop-blur-sm"
                role="alert">
                <div class="p-2 bg-emerald-500/20 rounded-lg mr-3"><i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i>
                </div>
                <div class="text-sm font-medium">{{ session('success') }}</div>
                <button @click="show = false" type="button"
                    class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-emerald-400 rounded-lg p-1.5 hover:bg-emerald-900/40 inline-flex h-8 w-8 items-center justify-center transition-colors"><i
                        class="fas fa-times"></i></button>
            </div>
        @endif

        <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden animate-fade-in-up"
            style="animation-delay: 100ms;">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-900/80 border-b border-gray-700 text-xs uppercase text-gray-400 font-bold tracking-wider">
                            <th class="px-6 py-5">แคมเปญ</th>
                            <th class="px-6 py-5">สิทธิพิเศษ (Privileges)</th>
                            <th class="px-6 py-5">ระยะเวลา (Schedule)</th>
                            <th class="px-6 py-5 text-center">สถานะใช้งาน</th>
                            <th class="px-6 py-5 text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse ($birthdayPromotions as $bp)
                            <tr class="hover:bg-gray-700/30 transition-colors duration-150 group">
                                <td class="px-6 py-5 align-top">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-20 h-14 rounded-lg bg-gray-900 overflow-hidden border border-gray-700 flex-shrink-0">
                                            @if ($bp->image_path)
                                                <img src="{{ asset('storage/' . $bp->image_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-800"><i
                                                        class="fas fa-image text-gray-600"></i></div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-base font-bold text-white group-hover:text-pink-400 transition-colors mb-1">{{ $bp->title }}</span>
                                            <p class="text-[11px] text-gray-400 line-clamp-1 max-w-xs"
                                                title="{{ $bp->message }}">{{ $bp->message }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-middle text-xs">
                                    <div class="flex flex-col gap-1.5">
                                        @if($bp->discount_code)
                                            <div class="flex items-center gap-2 text-amber-400">
                                                <i class="fas fa-ticket-alt w-4"></i>
                                                <span class="font-mono bg-amber-400/10 px-1.5 py-0.5 rounded border border-amber-400/20">{{ $bp->discount_code }}</span>
                                                @if($bp->discount_value > 0)
                                                    <span class="text-gray-500">(ลด {{ number_format($bp->discount_value) }}.-)</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($bp->gift_product_id)
                                            <div class="flex items-center gap-2 text-emerald-400">
                                                <i class="fas fa-gift w-4"></i>
                                                <span class="truncate max-w-[150px]" title="{{ $bp->giftProduct->pd_sp_name ?? 'สินค้าของแถม' }}">
                                                    {{ $bp->giftProduct->pd_sp_name ?? 'สินค้าของแถม' }}
                                                </span>
                                            </div>
                                        @endif
                                        @if($bp->card_image_path)
                                            <div class="flex items-center gap-2 text-blue-400">
                                                <i class="fas fa-id-card w-4"></i>
                                                <span>มีการ์ดอวยพร</span>
                                            </div>
                                        @endif
                                        @if(!$bp->discount_code && !$bp->gift_product_id && !$bp->promotion_id)
                                            <span class="text-gray-600 italic">ไม่มีสิทธิพิเศษ</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 align-middle">
                                    @if ($bp->start_date || $bp->end_date)
                                        <div class="inline-flex flex-col">
                                            @php
                                                $now = now();
                                                $isUpcoming = $bp->start_date && $bp->start_date > $now;
                                                $isExpired = $bp->end_date && $bp->end_date < $now;
                                            @endphp

                                            @if($isUpcoming)
                                                <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1">
                                                    <i class="fas fa-clock mr-1"></i> เตรียมเริ่มใช้งาน
                                                </span>
                                            @elseif($isExpired)
                                                <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-red-500/10 text-red-400 border border-red-500/20 mb-1">
                                                    <i class="fas fa-calendar-times mr-1"></i> สิ้นสุดแล้ว
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-1">
                                                    <i class="fas fa-calendar-alt mr-1"></i> พิเศษเฉพาะช่วง
                                                </span>
                                            @endif

                                            <span class="text-xs text-gray-400">
                                                {{ $bp->start_date ? $bp->start_date->format('d M Y H:i:s') : 'ตลอดมา' }} -
                                                {{ $bp->end_date ? $bp->end_date->format('d M Y H:i:s') : 'ไม่มีกำหนด' }}
                                            </span>

                                            {{-- Countdown Timer if active and has end date --}}
                                            @if(!$isUpcoming && !$isExpired && $bp->end_date)
                                                <div class="mt-2 flex items-center gap-1.5 text-[10px] font-bold px-2 py-1 rounded-lg border transition-all duration-500"
                                                     :class="isUrgent ? 'text-red-400 animate-pulse bg-red-500/10 border-red-500/20' : 'text-pink-400/80 bg-pink-500/5 border-pink-500/10'"
                                                     x-data="{
                                                        remaining: '',
                                                        isUrgent: false,
                                                        target: '{{ $bp->end_date->format('Y-m-d H:i:s') }}',
                                                        updateTimer() {
                                                            const diff = new Date(this.target) - new Date();
                                                            if (diff <= 0) {
                                                                this.remaining = 'แคมเปญสิ้นสุดลงแล้ว';
                                                                this.isUrgent = false;
                                                                return;
                                                            }
                                                            
                                                            // เช็คว่าเหลือน้อยกว่า 1 ชั่วโมง (3600000 ms) หรือไม่
                                                            this.isUrgent = diff < 3600000;

                                                            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                                            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                            const secs = Math.floor((diff % (1000 * 60)) / 1000);
                                                            
                                                            let str = '';
                                                            if (days > 0) str += `${days} วัน `;
                                                            str += `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')} ชม.`;
                                                            this.remaining = `เหลืออีก ${str}`;
                                                        }
                                                     }"
                                                     x-init="updateTimer(); setInterval(() => updateTimer(), 1000)">
                                                    <i class="fas" :class="isUrgent ? 'fa-exclamation-circle' : 'fa-hourglass-half'"></i>
                                                    <span x-text="remaining"></span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-gray-700 text-gray-400">
                                            <i class="fas fa-globe mr-1"></i> แคมเปญพื้นฐาน (ตลอดปี)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center align-middle" x-data="{ isActive: {{ $bp->is_active ? 'true' : 'false' }}, isToggling: false }">
                                    <button type="button"
                                        @click="async () => {
                                            if(isToggling) return; isToggling = true;
                                            try {
                                                const res = await fetch(`/admin/birthday-promotion/{{ $bp->id }}/toggle-status`, {
                                                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                                });
                                                if(res.ok) { const data = await res.json(); isActive = data.is_active; } else throw new Error();
                                            } catch(e) { alert('ไม่สามารถอัปเดตสถานะได้'); } finally { isToggling = false; }
                                        }"
                                        :disabled="isToggling"
                                        class="relative inline-flex items-center px-4 py-2 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer"
                                        :class="isActive ? 'bg-pink-500 text-white shadow-lg shadow-pink-500/30' :
                                            'bg-gray-700 text-gray-400 border border-gray-600 hover:bg-gray-600'">
                                        <i x-show="!isToggling" class="fas"
                                            :class="isActive ? 'fa-check-circle mr-1.5' : 'fa-circle mr-1.5'"></i>
                                        <i x-show="isToggling" class="fas fa-circle-notch fa-spin mr-1.5"></i>
                                        <span x-text="isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน'"></span>
                                    </button>
                                </td>

                                <td class="px-6 py-5 text-right align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.birthday-promotion.edit', $bp->id) }}"
                                            class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-indigo-600 rounded-xl transition-all"
                                            title="แก้ไข"><i class="fas fa-pen text-sm"></i></a>
                                        <button type="button"
                                            @click="openDeleteModal('{{ route('admin.birthday-promotion.destroy', $bp->id) }}', '{{ addslashes($bp->title) }}')"
                                            class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-red-600 rounded-xl transition-all"
                                            title="ลบ"><i class="fas fa-trash text-sm"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 bg-gray-900 rounded-3xl flex items-center justify-center mb-4 border border-gray-700">
                                            <i class="fas fa-birthday-cake text-4xl opacity-20 text-pink-500"></i></div>
                                        <p class="text-lg font-medium text-gray-300">ยังไม่มีแคมเปญวันเกิด</p>
                                        <p class="text-sm mt-1 text-gray-500">เริ่มต้นสร้างแคมเปญแรกของคุณได้เลย</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($birthdayPromotions->hasPages())
            <div class="mt-6 flex justify-end">{{ $birthdayPromotions->links('pagination::tailwind') }}</div>
        @endif

        <div class="mt-12 bg-blue-500/5 border border-blue-500/20 rounded-3xl p-8 flex gap-6 items-start">
            <div
                class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/10">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i></div>
            <div>
                <h4 class="font-bold text-blue-300 mb-2">ระบบความสำคัญของแคมเปญ (Priority System)</h4>
                <ul class="text-sm text-gray-400 space-y-2 list-disc list-inside">
                    <li>คุณสามารถตั้งค่าเปิดใช้งานหลายแคมเปญพร้อมกันได้</li>
                    <li>ระบบจะให้ความสำคัญกับ <strong>"แคมเปญพิเศษ (กำหนดวันที่)"</strong> เป็นอันดับแรก
                        หากตรงกับวันที่ลูกค้าเกิด</li>
                    <li>หากวันนี้ไม่มีแคมเปญพิเศษใดๆ ระบบจะดึง <strong>"แคมเปญพื้นฐาน (ไม่ระบุวันที่)"</strong>
                        ไปส่งแทนอัตโนมัติ</li>
                </ul>
            </div>
        </div>

        <div x-show="showDeleteModal" style="display: none;" class="relative z-50">
            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative transform overflow-hidden rounded-3xl bg-gray-800 border border-gray-700 p-8 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg"
                        @click.away="showDeleteModal = false">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-red-500/20 flex items-center justify-center"><i
                                    class="fas fa-exclamation-triangle text-red-500 text-xl"></i></div>
                            <h3 class="text-xl font-bold text-white">ยืนยันการลบแคมเปญ</h3>
                        </div>
                        <p class="text-gray-400 mb-8">คุณแน่ใจหรือไม่ว่าต้องการลบแคมเปญวันเกิด <span
                                class="text-white font-bold" x-text="promotionTitleToDelete"></span>?
                            ข้อมูลนี้ไม่สามารถกู้คืนได้</p>
                        <form :action="deleteFormAction" method="POST" class="flex justify-end gap-3">@csrf
                            @method('DELETE')<button type="button" @click="showDeleteModal = false"
                                class="px-6 py-3 rounded-xl bg-gray-700 text-gray-300 font-bold hover:bg-gray-600 transition-colors">ยกเลิก</button><button
                                type="submit"
                                class="px-6 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-500 shadow-lg shadow-red-900/30 transition-colors">ยืนยันการลบ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
