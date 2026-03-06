@extends('layouts.admin')

@section('title', 'จัดการโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-100 tracking-tight">แคมเปญโปรโมชั่น</h1>
                <p class="text-gray-400 mt-1">จัดการส่วนลดและเงื่อนไขการส่งเสริมการขาย</p>
            </div>
            <a href="{{ route('admin.promotions.create') }}"
                class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white shadow-lg shadow-emerald-900/20 gap-2 font-medium transition-all hover:scale-105">
                <i class="fas fa-plus"></i> สร้างโปรโมชั่น
            </a>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div
                class="alert alert-success bg-emerald-900/20 border border-emerald-500/30 text-emerald-200 shadow-lg mb-6 flex items-center rounded-xl backdrop-blur-sm">
                <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Promotions Table --}}
        <div class="bg-gray-800/60 backdrop-blur rounded-2xl shadow-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead
                        class="bg-gray-900/80 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-700">
                        <tr>
                            <th class="py-5 pl-6">ชื่อแคมเปญ</th>
                            <th class="py-5 text-center">ประเภท</th>
                            <th class="py-5 text-center">เงื่อนไข/ส่วนลด</th>
                            <th class="py-5 text-center">การใช้งาน</th>
                            <th class="py-5 text-center">สถานะ</th>
                            <th class="py-5 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/50">
                        @forelse ($promotions as $promo)
                            <tr class="hover:bg-gray-700/30 transition-colors duration-200 group">
                                {{-- Campaign Info --}}
                                <td class="pl-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-100 text-base group-hover:text-emerald-400 transition-colors">
                                            {{ $promo->name }}
                                        </span>
                                        @if ($promo->description)
                                            <span
                                                class="text-xs text-gray-500 mt-1 line-clamp-1 max-w-xs">{{ $promo->description }}</span>
                                        @endif
                                        <div class="flex items-center gap-2 mt-2 text-[10px] text-gray-400">
                                            <i class="far fa-calendar-alt"></i>
                                            <span>
                                                {{ $promo->start_date ? \Carbon\Carbon::parse($promo->start_date)->format('d/m/y') : 'Now' }}
                                                -
                                                {{ $promo->end_date ? \Carbon\Carbon::parse($promo->end_date)->format('d/m/y') : '∞' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="text-center py-4">
                                    @if ($promo->code)
                                        <div
                                            class="badge badge-lg bg-blue-900/30 text-blue-400 border border-blue-500/30 gap-1 font-mono">
                                            <i class="fas fa-ticket-alt text-xs"></i> {{ $promo->code }}
                                        </div>
                                    @elseif($promo->rules->count() > 0)
                                        <div
                                            class="badge badge-lg bg-pink-900/30 text-pink-400 border border-pink-500/30 gap-1">
                                            <i class="fas fa-gifts text-xs"></i> Buy X Get Y
                                        </div>
                                    @else
                                        <div
                                            class="badge badge-lg bg-emerald-900/30 text-emerald-400 border border-emerald-500/30 gap-1">
                                            <i class="fas fa-bolt text-xs"></i> Auto
                                        </div>
                                    @endif
                                </td>

                                {{-- Value / Logic --}}
                                <td class="text-center py-4">
                                    @if (isset($promo->discount_value))
                                        <div class="flex flex-col items-center">
                                            <span class="text-xl font-bold text-white">
                                                {{ number_format($promo->discount_value, 0) }}{{ $promo->discount_type === 'percentage' ? '%' : '฿' }}
                                            </span>
                                            @if ($promo->min_order_value > 0)
                                                <span
                                                    class="text-[10px] text-gray-400 bg-gray-700 px-1.5 py-0.5 rounded mt-1">
                                                    ขั้นต่ำ ฿{{ number_format($promo->min_order_value) }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($promo->rules->count() > 0)
                                        <div class="flex items-center justify-center text-xs gap-2">
                                            <span class="bg-gray-700 px-2 py-1 rounded text-gray-300">ซื้อ
                                                {{ $promo->rules->sum('quantity') }}</span>
                                            <i class="fas fa-arrow-right text-gray-500"></i>
                                            <span
                                                class="bg-pink-900/50 text-pink-300 border border-pink-500/30 px-2 py-1 rounded">
                                                แถม {{ $promo->actions->sum('quantity') }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>

                                {{-- Usage --}}
                                <td class="text-center py-4">
                                    @if ($promo->usage_limit)
                                        <div class="flex flex-col items-center gap-1 w-24 mx-auto">
                                            <div class="w-full bg-gray-700 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-emerald-500 h-full rounded-full"
                                                    style="width: {{ min(100, ($promo->used_count / $promo->usage_limit) * 100) }}%">
                                                </div>
                                            </div>
                                            <span class="text-[10px] text-gray-400">
                                                {{ number_format($promo->used_count) }} /
                                                {{ number_format($promo->usage_limit) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">ไม่จำกัด</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center py-4">
                                    @if ($promo->is_active)
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                            <span class="text-xs text-emerald-400 font-medium">Active</span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                            <span class="text-xs text-gray-500">Inactive</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-center py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                            class="btn btn-sm btn-square btn-ghost text-gray-400 hover:text-white hover:bg-gray-700">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST"
                                            onsubmit="return confirm('ยืนยันลบโปรโมชั่นนี้?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-square btn-ghost text-gray-400 hover:text-red-400 hover:bg-red-900/20">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-50">
                                        <div
                                            class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-tag text-3xl text-gray-400"></i>
                                        </div>
                                        <h3 class="font-bold text-gray-300">ไม่พบข้อมูลโปรโมชั่น</h3>
                                        <p class="text-sm text-gray-500 mt-1">กดปุ่ม "สร้างโปรโมชั่น"
                                            เพื่อเริ่มต้นแคมเปญใหม่</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promotions->hasPages())
                <div class="px-6 py-4 border-t border-gray-700 bg-gray-900/30">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
