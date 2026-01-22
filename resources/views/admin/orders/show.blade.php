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
    {{-- ★★★ คำนวณราคา Real-time หน้า Show ★★★ --}}
    @php
        $calculatedSubtotal = 0;
        foreach ($order->details as $detail) {
            // ใช้ ordd_price ถ้ามีค่า > 0, ถ้าเป็น 0 ให้ดึง pd_sp_price
            $itemPrice = $detail->ordd_price > 0 ? $detail->ordd_price : $detail->productSalepage->pd_sp_price ?? 0;

            $calculatedSubtotal += $itemPrice * $detail->ordd_count;
        }

        $calculatedNet = $calculatedSubtotal + $order->shipping_cost - $order->total_discount;
        if ($calculatedNet < 0) {
            $calculatedNet = 0;
        }
    @endphp

    @if (session('success'))
        <div class="alert alert-success shadow-sm mb-6"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- รายการสินค้า --}}
        <div class="lg:col-span-2 space-y-8">
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
                                    @php
                                        $product = $detail->productSalepage;
                                        // หารูปภาพ
                                        $imgUrl = 'https://via.placeholder.com/64?text=No+Image';
                                        if ($product && $product->images->isNotEmpty()) {
                                            $imgObj =
                                                $product->images->sortBy('img_sort')->first() ??
                                                $product->images->first();
                                            if ($imgObj) {
                                                $path = $imgObj->img_path ?? $imgObj->image_path;
                                                $imgUrl = filter_var($path, FILTER_VALIDATE_URL)
                                                    ? $path
                                                    : asset(
                                                        'storage/' . ltrim(str_replace('storage/', '', $path), '/'),
                                                    );
                                            }
                                        }

                                        // ★★★ หาราคาที่จะแสดง (Fix 0.00) ★★★
                                        $displayPrice =
                                            $detail->ordd_price > 0 ? $detail->ordd_price : $product->pd_sp_price ?? 0;
                                    @endphp
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12 bg-gray-100">
                                                        <img src="{{ $imgUrl }}" class="object-cover w-full h-full">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-800">
                                                        {{ $product->pd_sp_name ?? 'สินค้าถูกลบ' }}</div>
                                                    <div class="text-xs text-gray-400">SKU:
                                                        {{ $product->pd_sp_code ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <span class="font-medium">฿{{ number_format($displayPrice, 2) }}</span>
                                        </td>
                                        <td class="text-center font-mono">{{ $detail->ordd_count }}</td>
                                        <td class="text-right font-bold text-primary">
                                            ฿{{ number_format($displayPrice * $detail->ordd_count, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ยอดรวม --}}
                    <div class="divider my-4"></div>
                    <div class="space-y-2 max-w-sm ml-auto bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">ยอดรวมสินค้า</span>
                            <span class="font-semibold">฿{{ number_format($calculatedSubtotal, 2) }}</span>
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
                            <span>฿{{ number_format($calculatedNet, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body">
                    <h2 class="card-title text-gray-800 text-base"><i class="fas fa-tasks text-primary mr-2"></i>
                        สถานะออเดอร์</h2>
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="form-control w-full">
                            <div class="join w-full">
                                <select name="status_id" class="select select-bordered join-item flex-grow">
                                    @foreach ($statuses as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ $order->status_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary join-item">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-white shadow-sm border border-gray-200">
                <div class="card-body">
                    <h2 class="card-title text-gray-800 text-base"><i class="fas fa-user-circle text-primary mr-2"></i>
                        ข้อมูลจัดส่ง</h2>
                    <div class="space-y-3 text-sm mt-2 text-gray-600">
                        <p><strong>ชื่อ:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>โทร:</strong> {{ $order->shipping_phone }}</p>
                        <p><strong>ที่อยู่:</strong> {{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>

            @if ($order->slip_path)
                <div class="card bg-white shadow-sm border border-gray-200">
                    <div class="card-body">
                        <h2 class="card-title text-gray-800 text-base"><i class="fas fa-receipt text-primary mr-2"></i>
                            หลักฐานการโอน</h2>
                        <div class="mt-4 rounded-lg overflow-hidden border border-gray-200">
                            @php
                                $slipUrl = filter_var($order->slip_path, FILTER_VALIDATE_URL)
                                    ? $order->slip_path
                                    : asset('storage/' . ltrim(str_replace('storage/', '', $order->slip_path), '/'));
                            @endphp
                            <a href="{{ $slipUrl }}" target="_blank"><img src="{{ $slipUrl }}"
                                    class="w-full object-contain"></a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
