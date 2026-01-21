@extends('layouts.admin')

@section('title', 'จัดการโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">รายการโปรโมชั่น</h1>
                <p class="text-gray-500 mt-1">จัดการแคมเปญและเงื่อนไขการส่งเสริมการขายทั้งหมด</p>
            </div>
            <a href="{{ route('admin.promotions.create') }}"
                class="btn btn-primary btn-md shadow-lg shadow-primary/20 gap-2 font-medium transition-transform hover:scale-105">
                <i class="fas fa-plus"></i> เพิ่มโปรโมชั่นใหม่
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div role="alert"
                class="alert alert-success bg-green-50 border border-green-200 text-green-800 shadow-sm mb-6 flex items-center">
                <i class="fas fa-check-circle text-xl text-green-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-4 pl-6 w-[25%]">รายละเอียดแคมเปญ</th>
                            <th class="py-4 text-center w-[40%]">เงื่อนไข (ซื้อ <i
                                    class="fas fa-arrow-right text-xs mx-1"></i> แถม)</th>
                            <th class="py-4 text-center">ระยะเวลา</th>
                            <th class="py-4 text-center">สถานะ</th>
                            <th class="py-4 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($promotions as $promo)
                            <tr class="hover:bg-gray-50/50 transition-colors duration-200 group">
                                {{-- Name & Desc --}}
                                <td class="pl-6 align-top py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-800 text-base group-hover:text-primary transition-colors">
                                            {{ $promo->name }}
                                        </span>
                                        @if ($promo->description)
                                            <span class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                                {{ $promo->description }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic mt-1">- ไม่มีรายละเอียด -</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Logic Visualizer --}}
                                <td class="align-middle py-4">
                                    <div
                                        class="flex items-center justify-center gap-3 bg-gray-50/80 rounded-xl p-3 border border-dashed border-gray-200">
                                        <div class="flex flex-col gap-1 items-end min-w-[40%] text-right">
                                            @foreach ($promo->rules as $rule)
                                                <div class="text-xs text-gray-600 flex items-center justify-end gap-2">
                                                    <span class="truncate max-w-[120px]"
                                                        title="{{ $products[$rule->product_id] ?? 'Unknown' }}">
                                                        {{ $products[$rule->product_id] ?? 'สินค้าถูกลบ' }}
                                                    </span>
                                                    <span
                                                        class="badge badge-sm badge-primary badge-outline font-mono">x{{ $rule->quantity }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="text-gray-300">
                                            <i class="fas fa-arrow-circle-right text-lg"></i>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start min-w-[40%] text-left">
                                            @foreach ($promo->actions as $action)
                                                @php
                                                    if ($action->product_id) {
                                                        $getName = $products[$action->product_id] ?? 'สินค้าถูกลบ';
                                                    } else {
                                                        $count = $action->giftableProducts->count();
                                                        $getName = "เลือกได้ ($count รายการ)";
                                                    }
                                                @endphp
                                                <div class="text-xs text-gray-600 flex items-center gap-2">
                                                    <span class="badge badge-sm badge-secondary text-white font-mono">ฟรี
                                                        {{ $action->quantity }}</span>
                                                    <span class="truncate max-w-[120px]" title="{{ $getName }}">
                                                        {{ $getName }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="text-center align-middle py-4">
                                    @if ($promo->start_date)
                                        <div
                                            class="flex flex-col text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg px-2 py-1 inline-block text-left w-fit mx-auto shadow-sm">
                                            <div class="flex items-center gap-2 border-b border-gray-100 pb-1 mb-1">
                                                <span class="text-green-600 w-4 text-center"><i
                                                        class="fas fa-play text-[10px]"></i></span>
                                                <span
                                                    class="font-mono">{{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-red-500 w-4 text-center"><i
                                                        class="fas fa-stop text-[10px]"></i></span>
                                                <span
                                                    class="font-mono">{{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/y H:i') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="badge badge-ghost text-xs">ตลอดไป</div>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center align-middle py-4">
                                    @if ($promo->is_active)
                                        <span class="badge badge-success gap-1 text-white shadow-sm px-3 py-2">
                                            <i class="fas fa-check"></i> ใช้งาน
                                        </span>
                                    @else
                                        <span class="badge badge-ghost gap-1 text-gray-400 bg-gray-200 px-3 py-2">
                                            <i class="fas fa-times"></i> ปิด
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-center align-middle py-4">
                                    <div class="join shadow-sm border border-gray-200 rounded-lg">
                                        <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                            class="btn btn-sm btn-ghost join-item text-yellow-600 hover:bg-yellow-50 tooltip"
                                            data-tip="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST"
                                            class="join-item"
                                            onsubmit="return confirm('ยืนยันลบโปรโมชั่นนี้? ข้อมูลจะไม่สามารถกู้คืนได้');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-ghost text-red-500 hover:bg-red-50 tooltip"
                                                data-tip="ลบ">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-60">
                                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                                            <i class="fas fa-tag text-4xl text-gray-400"></i>
                                        </div>
                                        <h3 class="font-bold text-lg text-gray-600">ยังไม่มีโปรโมชั่น</h3>
                                        <p class="text-sm text-gray-500 mb-4">เริ่มต้นสร้างแคมเปญแรกของคุณได้เลย</p>
                                        <a href="{{ route('admin.promotions.create') }}"
                                            class="btn btn-sm btn-primary">สร้างโปรโมชั่น</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($promotions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $promotions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
