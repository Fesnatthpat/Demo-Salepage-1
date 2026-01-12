@extends('layouts.admin')

@section('title', 'รายละเอียดออเดอร์')
@section('page-title')
    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-900">ออเดอร์</a> /
    <span class="text-gray-900">รายละเอียดออเดอร์ {{ $order->ord_code }}</span>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success shadow-lg mb-6">
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
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title mb-4">รายการสินค้าในออเดอร์</h2>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th class="text-right">ราคาต่อหน่วย</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-right">ราคารวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="flex items-center space-x-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12">
                                                        <img src="{{ asset('storage/' . ($detail->productSalepage->images->first()->image_path ?? '')) }}" alt="{{ $detail->productSalepage->pd_sp_name ?? '' }}">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $detail->productSalepage->pd_sp_name ?? 'N/A' }}</div>
                                                    <div class="text-sm opacity-50">SKU: {{ $detail->productSalepage->pd_code ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            @if($detail->pd_original_price > $detail->pd_price)
                                                <s class="text-gray-400">฿{{ number_format($detail->pd_original_price, 2) }}</s>
                                            @endif
                                            ฿{{ number_format($detail->pd_price, 2) }}
                                        </td>
                                        <td class="text-center">{{ $detail->ordd_count }}</td>
                                        <td class="text-right font-semibold">฿{{ number_format($detail->pd_price * $detail->ordd_count, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Totals --}}
                    <div class="divider"></div>
                    <div class="space-y-2 max-w-sm ml-auto">
                         <div class="flex justify-between text-sm">
                            <span class="text-gray-500">ยอดรวมสินค้า</span>
                            <span class="font-semibold">฿{{ number_format($order->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">ค่าจัดส่ง</span>
                            <span class="font-semibold">฿{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-red-500">
                            <span class="text-gray-500">ส่วนลด</span>
                            <span class="font-semibold">-฿{{ number_format($order->total_discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>ยอดสุทธิ</span>
                            <span>฿{{ number_format($order->net_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (Customer & Status) --}}
        <div class="lg:col-span-1 space-y-8">
            {{-- Status Update Card --}}
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title">สถานะออเดอร์</h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">อัปเดตสถานะ</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="status_id" class="select select-bordered flex-grow">
                                    @foreach($statuses as $id => $name)
                                        <option value="{{ $id }}" {{ $order->status_id == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Customer Info Card --}}
            <div class="card bg-white shadow-md">
                <div class="card-body">
                    <h2 class="card-title">ข้อมูลลูกค้าและที่อยู่จัดส่ง</h2>
                    <div class="space-y-2 text-sm mt-4">
                        <p><strong>ชื่อ:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>อีเมล:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        <p><strong>เบอร์โทรศัพท์:</strong> {{ $order->shipping_phone }}</p>
                        <div class="divider my-2"></div>
                        <p><strong>ที่อยู่:</strong></p>
                        <p class="whitespace-pre-line overflow-y-auto h-[300px]">{{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>

             @if($order->slip_path)
            <div class="card bg-white shadow-md">
                <div class="card-body">
                     <h2 class="card-title">สลิปการโอนเงิน</h2>
                     <a href="{{ asset('storage/' . $order->slip_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $order->slip_path) }}" alt="Payment Slip" class="rounded-lg mt-4 w-full cursor-pointer">
                     </a>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
