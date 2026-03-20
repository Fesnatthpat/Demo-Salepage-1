@extends('layouts.admin')

@section('title', 'จัดการ Popup หน้าแรก')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center shadow-lg shadow-indigo-500/10">
                    <i class="fas fa-window-restore text-indigo-400 text-2xl"></i>
                </div>
                Homepage Popups
            </h1>
            <p class="text-gray-400 text-sm mt-1">จัดการภาพโฆษณาที่จะแสดงเมื่อลูกค้าเข้าสู่หน้าแรกของเว็บไซต์</p>
        </div>
        <a href="{{ route('admin.popups.create') }}"
            class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 rounded-xl hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-lg shadow-indigo-900/30 overflow-hidden">
            <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
            <i class="fas fa-plus mr-2 transition-transform group-hover:rotate-90"></i> สร้าง Popup ใหม่
        </a>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
            class="flex items-center p-4 mb-4 text-emerald-400 rounded-xl bg-emerald-900/20 border border-emerald-500/20 shadow-lg backdrop-blur-sm">
            <div class="p-2 bg-emerald-500/20 rounded-lg mr-3"><i class="fas fa-check-circle flex-shrink-0 w-5 h-5"></i></div>
            <div class="text-sm font-medium">{{ session('success') }}</div>
            <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-emerald-400 rounded-lg p-1.5 hover:bg-emerald-900/40 inline-flex h-8 w-8 items-center justify-center transition-colors"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="bg-gray-800 rounded-2xl shadow-xl border border-gray-700 overflow-hidden animate-fade-in-up">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900/80 border-b border-gray-700 text-xs uppercase text-gray-400 font-bold tracking-wider">
                        <th class="px-6 py-5 text-center">Sort</th>
                        <th class="px-6 py-5">Popup Info</th>
                        <th class="px-6 py-5">Display Logic</th>
                        <th class="px-6 py-5">Schedule</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse ($popups as $popup)
                        <tr class="hover:bg-gray-700/30 transition-colors duration-150 group">
                            <td class="px-6 py-5 text-center align-middle">
                                <span class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-gray-900 border border-gray-700 text-indigo-400 font-bold text-xs shadow-inner shadow-black/20">
                                    {{ $popup->sort_order }}
                                </span>
                            </td>
                            <td class="px-6 py-5 align-top">
                                <div class="flex items-center gap-4">
                                    <div class="w-24 h-16 rounded-lg bg-gray-900 overflow-hidden border border-gray-700 flex-shrink-0 relative group-hover:border-indigo-500/50 transition-colors">
                                        <img src="{{ asset('storage/' . $popup->image_path) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-bold text-white group-hover:text-indigo-400 transition-colors mb-1">{{ $popup->name }}</span>
                                        <div class="flex flex-wrap gap-1 mt-0.5">
                                            @if(empty($popup->display_pages))
                                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-gray-700 text-gray-400 border border-gray-600">แสดงทุกหน้า</span>
                                            @else
                                                @foreach($popup->display_pages as $page)
                                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                                        @if($page == 'home') หน้าหลัก 
                                                        @elseif($page == 'allproducts') หน้าสินค้าทั้งหมด 
                                                        @elseif($page == 'product.show') หน้าสินค้าเฉพาะอัน 
                                                        @else {{ $page }} @endif
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 align-middle text-xs">
                                <div class="flex flex-col gap-1.5">
                                    @if($popup->display_type === 'once_per_session')
                                        <span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 inline-block w-fit font-bold">
                                            <i class="fas fa-user-clock mr-1"></i> แสดงครั้งเดียวต่อเซสชัน
                                        </span>
                                    @elseif($popup->display_type === 'once_per_day')
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 inline-block w-fit font-bold">
                                            <i class="fas fa-calendar-day mr-1"></i> แสดงวันละครั้ง
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20 inline-block w-fit font-bold">
                                            <i class="fas fa-sync-alt mr-1"></i> แสดงทุกครั้งที่โหลด
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-5 align-middle">
                                @if ($popup->start_date || $popup->end_date)
                                    <div class="inline-flex flex-col gap-1">
                                        @php
                                            $now = now();
                                            $isUpcoming = $popup->start_date && $popup->start_date > $now;
                                            $isExpired = $popup->end_date && $popup->end_date < $now;
                                        @endphp

                                        @if($isUpcoming)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 w-fit">
                                                <i class="fas fa-clock mr-1"></i> เตรียมเริ่มใช้งาน
                                            </span>
                                        @elseif($isExpired)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20 w-fit">
                                                <i class="fas fa-calendar-times mr-1"></i> สิ้นสุดแล้ว
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 w-fit">
                                                <i class="fas fa-check mr-1"></i> กำลังออนไลน์
                                            </span>
                                        @endif

                                        <span class="text-[10px] text-gray-400 font-mono">
                                            S: {{ $popup->start_date ? $popup->start_date->format('d/m/y H:i') : 'Immediate' }}<br>
                                            E: {{ $popup->end_date ? $popup->end_date->format('d/m/y H:i') : 'Unlimited' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="px-3 py-1 rounded-md text-[11px] font-bold bg-gray-700 text-gray-400">
                                        <i class="fas fa-globe mr-1"></i> แสดงผลตลอดเวลา
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-5 text-center align-middle" x-data="{ isActive: {{ $popup->is_active ? 'true' : 'false' }}, isToggling: false }">
                                <button type="button"
                                    @click="async () => {
                                        if(isToggling) return; isToggling = true;
                                        try {
                                            const res = await fetch(`/admin/popups/{{ $popup->id }}/toggle-status`, {
                                                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                            });
                                            if(res.ok) { const data = await res.json(); isActive = data.is_active; } else throw new Error();
                                        } catch(e) { alert('ไม่สามารถอัปเดตสถานะได้'); } finally { isToggling = false; }
                                    }"
                                    :disabled="isToggling"
                                    class="relative inline-flex items-center px-4 py-2 rounded-full text-xs font-bold transition-all duration-300 cursor-pointer"
                                    :class="isActive ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-gray-700 text-gray-400 border border-gray-600 hover:bg-gray-600'">
                                    <i x-show="!isToggling" class="fas" :class="isActive ? 'fa-check-circle mr-1.5' : 'fa-circle mr-1.5'"></i>
                                    <i x-show="isToggling" class="fas fa-circle-notch fa-spin mr-1.5"></i>
                                    <span x-text="isActive ? 'เปิดใช้งาน' : 'ปิดใช้งาน'"></span>
                                </button>
                            </td>

                            <td class="px-6 py-5 text-right align-middle">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.popups.edit', $popup->id) }}"
                                        class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-indigo-600 rounded-xl transition-all"
                                        title="แก้ไข"><i class="fas fa-pen text-sm"></i></a>
                                    
                                    <form action="{{ route('admin.popups.destroy', $popup->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบ Popup นี้?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center text-gray-400 hover:text-white hover:bg-red-600 rounded-xl transition-all"
                                            title="ลบ"><i class="fas fa-trash text-sm"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-900 rounded-3xl flex items-center justify-center mb-4 border border-gray-700">
                                        <i class="fas fa-window-restore text-4xl opacity-20 text-indigo-500"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-300">ยังไม่มี Popup โฆษณา</p>
                                    <p class="text-sm mt-1 text-gray-500">เริ่มต้นสร้าง Popup แรกของคุณได้เลยเพื่อส่งเสริมการขาย</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($popups->hasPages())
        <div class="mt-6 flex justify-end">{{ $popups->links('pagination::tailwind') }}</div>
    @endif
</div>
@endsection
