@extends('layouts.admin')

@section('title', 'จัดการออเดอร์')
@section('page-title', 'รายการออเดอร์ทั้งหมด')

@section('styles')
    <style>
        .slip-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 4px;
            transition: transform 0.2s;
        }

        .slip-thumbnail:hover {
            transform: scale(1.1);
        }
    </style>
@endsection

@section('content')
    <div class="card bg-white shadow-md">
        <div class="card-body">
            {{-- Header & Search --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title">ออเดอร์ทั้งหมด ({{ $orders->total() }})</h2>
                <form action="{{ route('admin.orders.index') }}" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหา รหัสออเดอร์, ชื่อลูกค้า..."
                                class="input input-bordered w-full sm:w-64 pr-10" value="{{ request('search') }}">
                            <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Filter Tabs --}}
            <div class="mb-4">
                <div class="join">
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                        class="join-item btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-active' : '' }}">ทั้งหมด</a>
                    @php $statusOptions = [1=>'รอชำระเงิน', 2=>'แจ้งชำระเงินแล้ว', 3=>'กำลังเตรียมจัดส่ง', 4=>'จัดส่งแล้ว', 5=>'ยกเลิก']; @endphp
                    @foreach ($statusOptions as $id => $text)
                        <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => $id])) }}"
                            class="join-item btn btn-sm {{ request('status') == $id ? 'btn-active' : '' }}">{{ $text }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>รหัสออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th class="text-right">ยอดรวม</th>
                            <th class="text-right">ส่วนลด</th>
                            <th class="text-right">ยอดสุทธิ</th>
                            <th class="text-center">สลิป</th>
                            <th class="text-center">สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $statusMap = [1=>'รอชำระเงิน', 2=>'แจ้งชำระเงินแล้ว', 3=>'กำลังเตรียมจัดส่ง', 4=>'จัดส่งแล้ว', 5=>'ยกเลิก']; @endphp

                        @forelse ($orders as $order)
                            {{-- ★★★ คำนวณราคา Real-time (ใช้ชื่อฟิลด์ ordd_price) ★★★ --}}
                            @php
                                $calTotal = $order->details->sum(function ($item) {
                                    // ใช้ ordd_price ถ้ามีค่า > 0, ถ้าเป็น 0 ให้ดึง pd_sp_price จากตารางสินค้า
                                    $price =
                                        $item->ordd_price > 0
                                            ? $item->ordd_price
                                            : $item->productSalepage->pd_sp_price ?? 0;

                                    // คูณด้วย ordd_count
                                    return $price * $item->ordd_count;
                                });

                                // คำนวณยอดสุทธิ (Total + Shipping - Discount)
                                $calNet = $calTotal + $order->shipping_cost - $order->total_discount;
                                if ($calNet < 0) {
                                    $calNet = 0;
                                } // ป้องกันติดลบ
                            @endphp

                            <tr class="hover group">
                                <td class="align-middle"><span
                                        class="font-mono font-semibold text-gray-700">{{ $order->ord_code }}</span></td>
                                <td class="align-middle">
                                    <div class="font-bold">{{ $order->shipping_name }}</div>
                                    <div class="text-sm opacity-50">{{ $order->user->email ?? '-' }}</div>
                                </td>
                                <td class="align-middle text-right">฿{{ number_format($calTotal, 2) }}</td>
                                <td class="align-middle text-right text-red-500">
                                    -฿{{ number_format($order->total_discount, 2) }}</td>
                                <td class="align-middle text-right font-bold text-primary">฿{{ number_format($calNet, 2) }}
                                </td>
                                <td class="align-middle text-center">
                                    @if ($order->slip_path)
                                        <img src="{{ asset('storage/' . $order->slip_path) }}" class="slip-thumbnail"
                                            data-slip-src="{{ asset('storage/' . $order->slip_path) }}">
                                    @else
                                        <span class="text-gray-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span
                                        class="badge @if ($order->status_id == 1) badge-warning @elseif($order->status_id == 2) badge-info @elseif($order->status_id == 3) badge-accent @elseif($order->status_id == 4) badge-success @elseif($order->status_id == 5) badge-error @else badge-ghost @endif">
                                        {{ $statusMap[$order->status_id] ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="align-middle text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="btn btn-xs btn-ghost text-blue-600">รายละเอียด</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-400">ไม่พบข้อมูลออเดอร์</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">{{ $orders->appends(request()->query())->links() }}</div>
        </div>
    </div>
    <div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; pointer-events: none;">
        <img src=""
            style="max-width: 300px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); background: white;">
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('slip-preview-modal');
            const modalImg = modal.querySelector('img');
            document.querySelectorAll('.slip-thumbnail').forEach(thumb => {
                thumb.addEventListener('mouseenter', (e) => {
                    modalImg.src = e.target.dataset.slipSrc;
                    modal.style.display = 'block';
                    const rect = e.target.getBoundingClientRect();
                    modal.style.top = (rect.top + 20) + 'px';
                    modal.style.left = (rect.left + 20) + 'px';
                });
                thumb.addEventListener('mouseleave', () => {
                    modal.style.display = 'none';
                });
            });
        });
    </script>
@endpush
