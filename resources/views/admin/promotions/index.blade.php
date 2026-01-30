@extends('layouts.admin')

@section('title', 'จัดการโปรโมชั่น')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-100 tracking-tight">รายการโปรโมชั่น</h1>
                <p class="text-gray-400 mt-1">จัดการแคมเปญและเงื่อนไขการส่งเสริมการขายทั้งหมด</p>
            </div>
            <a href="{{ route('admin.promotions.create') }}"
                class="btn btn-primary btn-md bg-emerald-600 hover:bg-emerald-700 border-none text-white shadow-lg shadow-emerald-900/20 gap-2 font-medium transition-transform hover:scale-105">
                <i class="fas fa-plus"></i> เพิ่มโปรโมชั่นใหม่
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div role="alert"
                class="alert alert-success bg-green-900/50 border border-green-800 text-green-200 shadow-sm mb-6 flex items-center">
                <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table Card --}}
        <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead
                        class="bg-gray-900/50 text-gray-400 text-xs uppercase font-bold tracking-wider border-b border-gray-700">
                        <tr>
                            <th class="py-4 pl-6 w-[25%]">รายละเอียดแคมเปญ</th>
                            <th class="py-4 text-center w-[40%]">เงื่อนไข (ซื้อ <i
                                    class="fas fa-arrow-right text-xs mx-1"></i> แถม)</th>
                            <th class="py-4 text-center">ระยะเวลา</th>
                            <th class="py-4 text-center">สถานะ</th>
                            <th class="py-4 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse ($promotions as $promo)
                            <tr
                                class="hover:bg-gray-700/50 transition-colors duration-200 group border-b border-gray-700 last:border-0">
                                {{-- Name & Desc --}}
                                <td class="pl-6 align-top py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-gray-200 text-base group-hover:text-emerald-400 transition-colors">
                                            {{ $promo->name }}
                                        </span>
                                        @if ($promo->description)
                                            <span class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                                                {{ $promo->description }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-600 italic mt-1">- ไม่มีรายละเอียด -</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Logic Visualizer --}}
                                <td class="align-middle py-4">
                                    <div
                                        class="flex items-center justify-center gap-3 bg-gray-900/50 rounded-xl p-3 border border-dashed border-gray-600">
                                        <div class="flex flex-col gap-1 items-end min-w-[40%] text-right">
                                            @foreach ($promo->rules as $rule)
                                                <div class="text-xs text-gray-400 flex items-center justify-end gap-2">
                                                    <span class="truncate max-w-[120px]"
                                                        title="{{ $products[$rule->product_id]->pd_sp_name ?? 'Unknown' }}">
                                                        {{ $products[$rule->product_id]->pd_sp_name ?? 'สินค้าถูกลบ' }}
                                                    </span>
                                                    <span
                                                        class="badge badge-sm badge-outline border-emerald-500 text-emerald-400 font-mono">x{{ $rule->quantity }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="text-gray-500">
                                            <i class="fas fa-arrow-circle-right text-lg"></i>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start min-w-[40%] text-left">
                                            @foreach ($promo->actions as $action)
                                                @php
                                                    if ($action->product_id) {
                                                        $getName =
                                                            $products[$action->product_id]->pd_sp_name ?? 'สินค้าถูกลบ';
                                                    } else {
                                                        $count = $action->giftableProducts->count();
                                                        $getName = "เลือกได้ ($count รายการ)";
                                                    }
                                                @endphp
                                                <div class="text-xs text-gray-400 flex items-center gap-2">
                                                    <span
                                                        class="badge badge-sm border-none bg-pink-600 text-white font-mono">ฟรี
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
                                            class="flex flex-col text-xs font-medium text-gray-400 bg-gray-900 border border-gray-600 rounded-lg px-2 py-1 inline-block text-left w-fit mx-auto shadow-sm">
                                            <div class="flex items-center gap-2 border-b border-gray-700 pb-1 mb-1">
                                                <span class="text-emerald-500 w-4 text-center"><i
                                                        class="fas fa-play text-[10px]"></i></span>
                                                <span
                                                    class="font-mono">{{ \Carbon\Carbon::parse($promo->start_date)->format('d/m/y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-red-400 w-4 text-center"><i
                                                        class="fas fa-stop text-[10px]"></i></span>
                                                <span
                                                    class="font-mono">{{ \Carbon\Carbon::parse($promo->end_date)->format('d/m/y H:i') }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="badge badge-ghost text-xs text-gray-400 bg-gray-700 border-gray-600">
                                            ตลอดไป</div>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="text-center align-middle py-4">
                                    @if ($promo->is_active)
                                        <span
                                            class="badge badge-success gap-1 text-white shadow-sm px-3 py-2 border-none bg-emerald-600">
                                            <i class="fas fa-check"></i> ใช้งาน
                                        </span>
                                    @else
                                        <span
                                            class="badge badge-ghost gap-1 text-gray-400 bg-gray-700 px-3 py-2 border-gray-600">
                                            <i class="fas fa-times"></i> ปิด
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="text-center align-middle py-4">
                                    <div class="join shadow-sm border border-gray-600 rounded-lg bg-gray-700">
                                        <a href="{{ route('admin.promotions.edit', $promo->id) }}"
                                            class="btn btn-sm btn-ghost join-item text-yellow-500 hover:bg-yellow-900/30 hover:text-yellow-400 tooltip tooltip-bottom"
                                            data-tip="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.promotions.destroy', $promo->id) }}" method="POST"
                                            class="join-item"
                                            onsubmit="return confirm('ยืนยันลบโปรโมชั่นนี้? ข้อมูลจะไม่สามารถกู้คืนได้');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-ghost text-red-400 hover:bg-red-900/30 hover:text-red-300 tooltip tooltip-bottom"
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
                                        <div class="bg-gray-700 rounded-full p-4 mb-4">
                                            <i class="fas fa-tag text-4xl text-gray-500"></i>
                                        </div>
                                        <h3 class="font-bold text-lg text-gray-400">ยังไม่มีโปรโมชั่น</h3>
                                        <p class="text-sm text-gray-500 mb-4">เริ่มต้นสร้างแคมเปญแรกของคุณได้เลย</p>
                                        <a href="{{ route('admin.promotions.create') }}"
                                            class="btn btn-sm btn-primary bg-emerald-600 border-none text-white">สร้างโปรโมชั่น</a>
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
