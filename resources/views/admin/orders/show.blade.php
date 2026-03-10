@extends('layouts.admin')

@section('title', 'รายละเอียดออเดอร์ ' . $order->ord_code)
@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-shopping-bag mr-1"></i> ออเดอร์ทั้งหมด
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">รายละเอียดออเดอร์</span>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success bg-green-900/50 border-green-800 text-green-200 shadow-sm mb-6 animate-fade-in-down">
            <div>
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- 🌟 Top Header Section 🌟 --}}
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-800 p-6 rounded-2xl shadow-lg border border-gray-700 mb-8 gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                {{ $order->ord_code }}
                <button onclick="navigator.clipboard.writeText('{{ $order->ord_code }}'); alert('คัดลอกรหัสแล้ว');"
                    class="text-gray-500 hover:text-emerald-400 text-lg transition-colors" title="คัดลอกรหัสออเดอร์">
                    <i class="far fa-copy"></i>
                </button>
            </h1>
            <p class="text-gray-400 text-sm mt-2 flex items-center gap-2">
                <i class="far fa-calendar-alt"></i> สั่งซื้อเมื่อ:
                {{ \Carbon\Carbon::parse($order->ord_date)->format('d M Y, เวลา H:i น.') }}
            </p>
        </div>

        @php
            $statusConfig = [
                1 => [
                    'label' => 'รอชำระเงิน',
                    'color' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                    'icon' => 'fa-clock',
                ],
                2 => [
                    'label' => 'แจ้งชำระเงินแล้ว',
                    'color' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                    'icon' => 'fa-file-invoice-dollar',
                ],
                3 => [
                    'label' => 'กำลังเตรียมจัดส่ง',
                    'color' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                    'icon' => 'fa-box-open',
                ],
                4 => [
                    'label' => 'จัดส่งแล้ว',
                    'color' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                    'icon' => 'fa-truck',
                ],
                5 => [
                    'label' => 'ยกเลิก',
                    'color' => 'bg-red-500/10 text-red-400 border-red-500/20',
                    'icon' => 'fa-times-circle',
                ],
            ];
            $currentStatus = $statusConfig[$order->status_id] ?? [
                'label' => 'ไม่ทราบสถานะ',
                'color' => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                'icon' => 'fa-question',
            ];
        @endphp
        <div class="px-4 py-2 rounded-full border shadow-sm {{ $currentStatus['color'] }} flex items-center gap-2">
            <i class="fas {{ $currentStatus['icon'] }}"></i>
            <span class="font-bold text-sm tracking-wide">{{ $currentStatus['label'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- 📦 Main Content (Order Details & Items) 📦 --}}
        <div class="xl:col-span-2 space-y-8">

            {{-- Order Items Card --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-100 flex items-center gap-2">
                        <i class="fas fa-box text-emerald-500"></i> รายการสินค้า ({{ $order->details->sum('ordd_count') }}
                        ชิ้น)
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-gray-300">
                        <thead>
                            <tr
                                class="bg-gray-900/50 text-gray-400 border-b border-gray-700 text-sm uppercase tracking-wider">
                                <th class="py-4 pl-6">สินค้า</th>
                                <th class="text-right py-4">ราคาต่อหน่วย</th>
                                <th class="text-center py-4">จำนวน</th>
                                <th class="text-right py-4 pr-6">ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700/50">
                            @foreach ($order->details as $detail)
                                <tr class="hover:bg-gray-700/30 transition-colors group">
                                    <td class="py-4 pl-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-16 h-16 rounded-xl overflow-hidden bg-gray-700 flex-shrink-0 border border-gray-600 shadow-sm">
                                                @php
                                                    $displayImage = 'https://via.placeholder.com/64?text=No+Image';
                                                    $product = $detail->productSalepage;

                                                    if ($product && $product->images->isNotEmpty()) {
                                                        $imgObj =
                                                            $product->images->sortBy('img_sort')->first() ??
                                                            $product->images->first();
                                                        $rawPath = $imgObj->img_path ?? $imgObj->image_path;

                                                        if ($rawPath) {
                                                            $displayImage = filter_var($rawPath, FILTER_VALIDATE_URL)
                                                                ? $rawPath
                                                                : asset(
                                                                    'storage/' .
                                                                        ltrim(
                                                                            str_replace('storage/', '', $rawPath),
                                                                            '/',
                                                                        ),
                                                                );
                                                        }
                                                    }
                                                @endphp
                                                <img src="{{ $displayImage }}"
                                                    alt="{{ $product->pd_sp_name ?? 'Product Image' }}"
                                                    class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-500"
                                                    onerror="this.src='https://via.placeholder.com/64?text=Error'">
                                            </div>
                                            <div class="flex flex-col justify-center">
                                                <div class="font-bold text-gray-100 text-base line-clamp-2 leading-tight">
                                                    {{ $product->pd_sp_name ?? 'สินค้าถูกลบไปแล้ว' }}
                                                </div>

                                                @if ($detail->productOption || $detail->option_name)
                                                    <div
                                                        class="inline-flex items-center w-fit mt-1.5 px-2 py-0.5 rounded bg-gray-900 border border-gray-600 text-xs text-emerald-400">
                                                        <i class="fas fa-tag mr-1.5"></i>
                                                        {{ $detail->productOption->option_name ?? $detail->option_name }}
                                                    </div>
                                                @endif

                                                <div class="text-xs text-gray-500 mt-1.5 font-mono">
                                                    SKU: {{ $product->pd_sp_code ?? ($product->pd_code ?? '-') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-right py-4 align-middle">
                                        @if (($detail->ordd_original_price ?? 0) > ($detail->ordd_price ?? 0))
                                            <div class="text-xs text-gray-500 line-through mb-0.5">
                                                ฿{{ number_format($detail->ordd_original_price, 2) }}
                                            </div>
                                        @endif
                                        <span class="font-bold text-gray-200">
                                            @if ($detail->ordd_price == 0)
                                                <span class="text-pink-500">แถมฟรี</span>
                                            @else
                                                ฿{{ number_format($detail->ordd_price, 2) }}
                                            @endif
                                        </span>
                                    </td>

                                    <td class="text-center align-middle py-4">
                                        <span
                                            class="px-3 py-1 bg-gray-900 rounded-lg text-gray-300 font-bold border border-gray-700 shadow-inner">
                                            x{{ $detail->ordd_count }}
                                        </span>
                                    </td>

                                    <td class="text-right font-bold text-emerald-400 py-4 pr-6 align-middle text-lg">
                                        @if ($detail->ordd_price == 0)
                                            <span class="text-pink-500">฿0.00</span>
                                        @else
                                            ฿{{ number_format($detail->ordd_price * $detail->ordd_count, 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Order Summary --}}
                <div class="p-6 bg-gray-900/30 border-t border-gray-700">
                    <div class="max-w-sm ml-auto space-y-3">
                        <div class="flex justify-between text-sm items-center">
                            <span class="text-gray-400 font-medium">ยอดรวมสินค้า</span>
                            <span class="font-bold text-gray-200">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm items-center">
                            <span class="text-gray-400 font-medium">ค่าจัดส่ง</span>
                            <span class="font-bold text-gray-200">฿{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if ($order->total_discount > 0)
                            <div
                                class="flex justify-between text-sm items-center text-red-400 bg-red-500/10 px-3 py-2 rounded-lg border border-red-500/20">
                                <span class="font-bold"><i class="fas fa-ticket-alt mr-1"></i> ส่วนลดโปรโมชั่น</span>
                                <span class="font-bold">-฿{{ number_format($order->total_discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-700 my-2 border-dashed"></div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-lg font-bold text-white">ยอดชำระสุทธิ</span>
                            <span
                                class="text-3xl font-black text-emerald-400 drop-shadow-md">฿{{ number_format($order->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🛠️ Sidebar (Customer, Status, Slip) 🛠️ --}}
        <div class="xl:col-span-1 space-y-6">

            {{-- Update Status Card --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700">
                <div class="p-5 border-b border-gray-700 flex items-center gap-2">
                    <i class="fas fa-tasks text-emerald-500"></i>
                    <h2 class="font-bold text-gray-100">อัปเดตสถานะ</h2>
                </div>
                <div class="card-body p-5">
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        <div class="form-control w-full">
                            <label
                                class="label text-xs font-bold text-gray-400 uppercase tracking-wider pb-1">สถานะใหม่</label>
                            <div class="flex flex-col gap-3">
                                <select name="status_id"
                                    class="select select-bordered w-full bg-gray-900 border-gray-600 text-gray-100 focus:outline-none focus:border-emerald-500 shadow-inner">
                                    @foreach ($statuses as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $order->status_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="btn w-full bg-emerald-600 hover:bg-emerald-700 border-none text-white font-bold shadow-lg shadow-emerald-900/30">
                                    <i class="fas fa-save mr-1"></i> บันทึกสถานะ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="card bg-gray-800 shadow-xl border border-gray-700">
                <div class="p-5 border-b border-gray-700 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-address-card text-emerald-500"></i>
                        <h2 class="font-bold text-gray-100">ข้อมูลการจัดส่ง</h2>
                    </div>
                </div>
                <div class="card-body p-5 space-y-4 text-sm text-gray-300">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 flex-shrink-0">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-100 text-base">{{ $order->shipping_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Email: {{ $order->user->email ?? 'ไม่มีอีเมล' }}</p>
                        </div>
                    </div>

                    <div class="h-px bg-gray-700 w-full"></div>

                    <div class="flex items-center gap-4 group">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 flex-shrink-0 group-hover:text-emerald-400 group-hover:bg-emerald-900/30 transition-colors">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <p class="font-medium font-mono text-base tracking-wide">{{ $order->shipping_phone }}</p>
                    </div>

                    <div class="flex items-start gap-4 group">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-gray-400 flex-shrink-0 mt-1 group-hover:text-emerald-400 group-hover:bg-emerald-900/30 transition-colors">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <p class="leading-relaxed text-gray-400">{{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Slip Card --}}
            @if ($order->slip_path)
                <div class="card bg-gray-800 shadow-xl border border-gray-700">
                    <div class="p-5 border-b border-gray-700 flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-500"></i>
                        <h2 class="font-bold text-gray-100">หลักฐานการชำระเงิน</h2>
                    </div>
                    <div class="card-body p-5 flex flex-col items-center">
                        <div
                            class="relative w-full max-w-[250px] rounded-xl overflow-hidden border-2 border-gray-600 bg-gray-900 group cursor-pointer shadow-lg">
                            @php
                                $slipUrl = filter_var($order->slip_path, FILTER_VALIDATE_URL)
                                    ? $order->slip_path
                                    : asset('storage/' . ltrim(str_replace('storage/', '', $order->slip_path), '/'));
                            @endphp

                            <img src="{{ $slipUrl }}" alt="Payment Slip"
                                class="w-full object-contain transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='https://via.placeholder.com/300?text=Slip+Error'">

                            {{-- Hover Overlay --}}
                            <a href="{{ $slipUrl }}" target="_blank"
                                class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white">
                                <i class="fas fa-search-plus text-3xl mb-2"></i>
                                <span class="font-bold text-sm tracking-wider">ดูรูปขนาดเต็ม</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
