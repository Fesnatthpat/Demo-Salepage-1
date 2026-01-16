@extends('layouts.admin')

@section('title', 'รายละเอียดออเดอร์')
@section('page-title')
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.orders.index') }}" class="hover:text-primary transition-colors">
            <i class="fas fa-shopping-bag mr-1"></i> ออเดอร์
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">รหัส: {{ $order->ord_code }}</span>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success shadow-sm mb-6">
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
            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body">
                    <h2 class="card-title text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-list-ul text-primary mr-2"></i> รายการสินค้า
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600">
                                    <th>สินค้า</th>
                                    <th class="text-right">ราคาต่อหน่วย</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-right">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $detail)
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12 bg-gray-100">
                                                        {{-- ★★★ PHP Logic สำหรับหารูปภาพแบบละเอียด (เหมือนหน้า Dashboard) ★★★ --}}
                                                        @php
                                                            $displayImage =
                                                                'https://via.placeholder.com/64?text=No+Image';
                                                            $product = $detail->productSalepage;

                                                            if ($product && $product->images->isNotEmpty()) {
                                                                // พยายามหารูปแรก หรือรูปที่มี sort น้อยที่สุด
                                                                $imgObj =
                                                                    $product->images->sortBy('img_sort')->first() ??
                                                                    $product->images->first();

                                                                $rawPath = $imgObj->img_path ?? $imgObj->image_path;

                                                                if ($rawPath) {
                                                                    if (filter_var($rawPath, FILTER_VALIDATE_URL)) {
                                                                        $displayImage = $rawPath;
                                                                    } else {
                                                                        $cleanPath = ltrim(
                                                                            str_replace('storage/', '', $rawPath),
                                                                            '/',
                                                                        );
                                                                        $displayImage = asset('storage/' . $cleanPath);
                                                                    }
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
                                                    <div class="font-bold text-gray-800">
                                                        {{ $product->pd_sp_name ?? 'สินค้าถูกลบไปแล้ว' }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        SKU: {{ $product->pd_code ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            @if ($detail->pd_original_price > $detail->pd_price)
                                                <div class="text-xs text-gray-400 line-through">
                                                    ฿{{ number_format($detail->pd_original_price, 2) }}</div>
                                            @endif
                                            <span class="font-medium">฿{{ number_format($detail->pd_price, 2) }}</span>
                                        </td>
                                        <td class="text-center font-mono">{{ $detail->ordd_count }}</td>
                                        <td class="text-right font-bold text-primary">
                                            ฿{{ number_format($detail->pd_price * $detail->ordd_count, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="divider my-4"></div>
                    <div class="space-y-2 max-w-sm ml-auto bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">ยอดรวมสินค้า</span>
                            <span class="font-semibold">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">ค่าจัดส่ง</span>
                            <span class="font-semibold">฿{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if ($order->total_discount > 0)
                            <div class="flex justify-between text-sm text-red-500">
                                <span>ส่วนลด</span>
                                <span class="font-semibold">-฿{{ number_format($order->total_discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="divider my-1"></div>
                        <div class="flex justify-between text-lg font-bold text-primary">
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
            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body">
                    <h2 class="card-title text-gray-800 text-base">
                        <i class="fas fa-tasks text-primary mr-2"></i> สถานะออเดอร์
                    </h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="form-control w-full">
                            <div class="join w-full">
                                <select name="status_id"
                                    class="select select-bordered join-item flex-grow focus:outline-none">
                                    @foreach ($statuses as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $order->status_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary join-item">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body">
                    <h2 class="card-title text-gray-800 text-base">
                        <i class="fas fa-user-circle text-primary mr-2"></i> ข้อมูลจัดส่ง
                    </h2>
                    <div class="space-y-3 text-sm mt-2 text-gray-600">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-user mt-1 text-gray-400 w-4"></i>
                            <div>
                                <p class="font-bold text-gray-800">{{ $order->shipping_name }}</p>
                                <p class="text-xs text-gray-400">User Email: {{ $order->user->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-phone text-gray-400 w-4"></i>
                            <p>{{ $order->shipping_phone }}</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-gray-400 w-4"></i>
                            <p class="whitespace-pre-line leading-relaxed">{{ $order->shipping_address }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($order->slip_path)
                <div class="card bg-white shadow-sm border border-gray-200">
                    <div class="card-body">
                        <h2 class="card-title text-gray-800 text-base">
                            <i class="fas fa-receipt text-primary mr-2"></i> หลักฐานการโอน
                        </h2>
                        <div class="mt-4 rounded-lg overflow-hidden border border-gray-200">
                            {{-- ★★★ PHP Logic สำหรับรูปสลิป ★★★ --}}
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
                                <img src="{{ $slipUrl }}" alt="Payment Slip" class="w-full object-contain bg-gray-50"
                                    onerror="this.src='https://via.placeholder.com/300?text=Slip+Error'">
                            </a>
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-2">คลิกที่รูปเพื่อดูภาพขยาย</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection