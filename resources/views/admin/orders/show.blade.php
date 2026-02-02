@extends('layouts.admin')

@section('title', 'รายละเอียดออเดอร์')
@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-emerald-400 transition-colors">
            <i class="fas fa-shopping-bag mr-1"></i> ออเดอร์
        </a>
        <span>/</span>
        <span class="text-gray-100 font-medium">รหัส: {{ $order->ord_code }}</span>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success bg-green-900/50 border-green-800 text-green-200 shadow-sm mb-6">
            <div>
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content (Order Details & Items) --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Order Items Card --}}
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 mb-4 border-b border-gray-700 pb-3">
                        <i class="fas fa-list-ul text-emerald-500 mr-2"></i> รายการสินค้า
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full text-gray-300">
                            <thead>
                                <tr class="bg-gray-900/50 text-gray-400 border-b border-gray-700">
                                    <th>สินค้า</th>
                                    <th class="text-right">ราคาต่อหน่วย</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-right">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $detail)
                                    <tr class="border-b border-gray-700 last:border-0 hover:bg-gray-700/30">
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12 bg-gray-700">
                                                        @php
                                                            $displayImage =
                                                                'https://via.placeholder.com/64?text=No+Image';
                                                            $product = $detail->productSalepage;

                                                            // Logic ดึงรูปภาพ
                                                            if ($product && $product->images->isNotEmpty()) {
                                                                $imgObj =
                                                                    $product->images->sortBy('img_sort')->first() ??
                                                                    $product->images->first();
                                                                $rawPath = $imgObj->img_path ?? $imgObj->image_path;

                                                                if ($rawPath) {
                                                                    $displayImage = filter_var(
                                                                        $rawPath,
                                                                        FILTER_VALIDATE_URL,
                                                                    )
                                                                        ? $rawPath
                                                                        : asset(
                                                                            'storage/' .
                                                                                ltrim(
                                                                                    str_replace(
                                                                                        'storage/',
                                                                                        '',
                                                                                        $rawPath,
                                                                                    ),
                                                                                    '/',
                                                                                ),
                                                                        );
                                                                }
                                                            }
                                                        @endphp
                                                        <img src="{{ $displayImage }}"
                                                            alt="{{ $product->pd_sp_name ?? 'Product Image' }}"
                                                            class="object-cover w-full h-full"
                                                            onerror="this.src='https://via.placeholder.com/64?text=Error'">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-200">
                                                        {{ $product->pd_sp_name ?? 'สินค้าถูกลบไปแล้ว' }}
                                                    </div>

                                                    {{-- ✅ เพิ่มส่วนแสดงตัวเลือกสินค้า (ถ้ามี) --}}
                                                    @if (isset($detail->ordd_option_name) && $detail->ordd_option_name)
                                                        <div class="text-xs text-emerald-400 mt-0.5">
                                                            <i class="fas fa-tag mr-1"></i>ตัวเลือก:
                                                            {{ $detail->ordd_option_name }}
                                                        </div>
                                                    @endif

                                                    <div class="text-xs text-gray-500 mt-0.5">
                                                        SKU: {{ $product->pd_sp_code ?? ($product->pd_code ?? '-') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- ✅ แก้ไข: ใช้ตัวแปร ordd_price แทน pd_price --}}
                                        <td class="text-right">
                                            @if (($detail->ordd_original_price ?? 0) > ($detail->ordd_price ?? 0))
                                                <div class="text-xs text-gray-500 line-through">
                                                    ฿{{ number_format($detail->ordd_original_price, 2) }}
                                                </div>
                                            @endif
                                            <span class="font-medium text-gray-300">
                                                ฿{{ number_format($detail->ordd_price, 2) }}
                                            </span>
                                        </td>

                                        <td class="text-center font-mono text-gray-300">{{ $detail->ordd_count }}</td>

                                        {{-- ✅ แก้ไข: คำนวณราคารวมจาก ordd_price --}}
                                        <td class="text-right font-bold text-emerald-400">
                                            ฿{{ number_format($detail->ordd_price * $detail->ordd_count, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="divider border-gray-700 my-4"></div>
                    <div class="space-y-2 max-w-sm ml-auto bg-gray-900/50 p-4 rounded-lg border border-gray-700">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">ยอดรวมสินค้า</span>
                            <span class="font-semibold text-gray-200">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">ค่าจัดส่ง</span>
                            <span class="font-semibold text-gray-200">฿{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if ($order->total_discount > 0)
                            <div class="flex justify-between text-sm text-red-400">
                                <span>ส่วนลด</span>
                                <span class="font-semibold">-฿{{ number_format($order->total_discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="divider my-1 border-gray-700"></div>
                        <div class="flex justify-between text-lg font-bold text-emerald-400">
                            <span>ยอดสุทธิ</span>
                            <span>฿{{ number_format($order->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (Customer & Status) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Status Update Card --}}
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 text-base">
                        <i class="fas fa-tasks text-emerald-500 mr-2"></i> สถานะออเดอร์
                    </h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="form-control w-full">
                            <div class="join w-full">
                                <select name="status_id"
                                    class="select select-bordered join-item flex-grow bg-gray-700 border-gray-600 text-gray-100 focus:outline-none focus:border-emerald-500">
                                    @foreach ($statuses as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $order->status_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 border-none join-item text-white">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="card bg-gray-800 shadow-lg border border-gray-700">
                <div class="card-body">
                    <h2 class="card-title text-gray-100 text-base">
                        <i class="fas fa-user-circle text-emerald-500 mr-2"></i> ข้อมูลจัดส่ง
                    </h2>
                    <div class="space-y-3 text-sm mt-2 text-gray-300">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user mt-1 text-gray-500 w-4"></i>
                            <div>
                                <p class="font-bold text-gray-100">{{ $order->shipping_name }}</p>
                                <p class="text-xs text-gray-500">User Email: {{ $order->user->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone text-gray-500 w-4"></i>
                            <p>{{ $order->shipping_phone }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-gray-500 w-4"></i>
                            <p class="whitespace-pre-line leading-relaxed text-gray-400">{{ $order->shipping_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($order->slip_path)
                <div class="card bg-gray-800 shadow-lg border border-gray-700">
                    <div class="card-body">
                        <h2 class="card-title text-gray-100 text-base">
                            <i class="fas fa-receipt text-emerald-500 mr-2"></i> หลักฐานการโอน
                        </h2>
                        <div class="mt-4 rounded-lg overflow-hidden border border-gray-600 bg-gray-900">
                            @php
                                $slipUrl = '';
                                if (filter_var($order->slip_path, FILTER_VALIDATE_URL)) {
                                    $slipUrl = $order->slip_path;
                                } else {
                                    $cleanSlipPath = ltrim(str_replace('storage/', '', $order->slip_path), '/');
                                    $slipUrl = asset('storage/' . $cleanSlipPath);
                                }
                            @endphp

                            <a href="{{ $slipUrl }}" target="_blank" class="block hover:opacity-90 transition">
                                <img src="{{ $slipUrl }}" alt="Payment Slip" class="w-full object-contain"
                                    onerror="this.src='https://via.placeholder.com/300?text=Slip+Error'">
                            </a>
                        </div>
                        <p class="text-center text-xs text-gray-500 mt-2">คลิกที่รูปเพื่อดูภาพขยาย</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
