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
            <!-- Header & Search -->
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

            <!-- Status Filter -->
            <div class="mb-4">
                <div class="join">
                    {{-- All Statuses --}}
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                        class="join-item btn btn-sm {{ !request('status') || request('status') == 'all' ? 'btn-active' : '' }}">
                        ทั้งหมด
                    </a>
                    {{-- Specific Statuses --}}
                    @php
                        $statusOptions = [
                            1 => 'รอชำระเงิน',
                            2 => 'กำลังดำเนินการ',
                            3 => 'จัดส่งแล้ว',
                            4 => 'สำเร็จ',
                            5 => 'ยกเลิก',
                        ];
                    @endphp
                    @foreach ($statusOptions as $id => $text)
                        <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => $id])) }}"
                            class="join-item btn btn-sm {{ request('status') == $id ? 'btn-active' : '' }}">
                            {{ $text }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Orders Table -->
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
                        @php
                            $statusMap = [
                                1 => 'รอชำระเงิน',
                                2 => 'แจ้งชำระเงินแล้ว',
                                3 => 'กำลังเตรียมจัดส่ง',
                                4 => 'จัดส่งแล้ว',
                                5 => 'ยกเลิก',
                            ];
                        @endphp
                        @forelse ($orders as $order)
                            <tr class="hover">
                                <td class="align-middle font-mono font-semibold">{{ $order->ord_code }}</td>
                                <td class="align-middle">
                                    <div class="font-bold">{{ $order->shipping_name }}</div>
                                    <div class="text-sm opacity-50">{{ $order->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="align-middle text-right">฿{{ number_format($order->total_price, 2) }}</td>
                                <td class="align-middle text-right text-red-500">
                                    -฿{{ number_format($order->total_discount, 2) }}</td>
                                <td class="align-middle text-right font-bold">฿{{ number_format($order->net_amount, 2) }}
                                </td>
                                <td class="align-middle text-center">
                                    @if ($order->slip_path)
                                        <img src="{{ asset('storage/' . $order->slip_path) }}" alt="Slip"
                                            class="slip-thumbnail"
                                            data-slip-src="{{ asset('storage/' . $order->slip_path) }}">
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span
                                        class="badge
                                    @switch($order->status_id)
                                        @case(1) badge-warning @break
                                        @case(2) badge-info @break
                                        @case(3) badge-success @break
                                        @case(4) badge-primary @break
                                        @case(5) badge-error @break
                                        @default badge-ghost
                                    @endswitch
                                ">
                                        {{ $statusMap[$order->status_id] ?? 'ไม่ทราบสถานะ' }}
                                    </span>
                                </td>
                                <td class="align-middle">{{ $order->ord_date->format('d M Y, H:i') }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="text-blue-600 font-semibold hover:underline">
                                        <i class="fas fa-eye"></i>
                                        ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-gray-500">
                                    @if (request('search'))
                                        ไม่พบออเดอร์ที่ตรงกับคำค้นหา "{{ request('search') }}"
                                    @else
                                        ยังไม่มีข้อมูลออเดอร์ในระบบ
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Slip Preview Modal -->
    <div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; transition: opacity 0.2s;">
        <img src="" alt="Slip Preview"
            style="max-width: 350px; height: auto; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); background-color: white;">
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('slip-preview-modal');
            if (!modal) return;

            const modalImage = modal.querySelector('img');
            const thumbnails = document.querySelectorAll('.slip-thumbnail');
            let hideTimeout;

            thumbnails.forEach(thumb => {
                thumb.addEventListener('mouseenter', (e) => {
                    clearTimeout(hideTimeout);
                    const rect = e.target.getBoundingClientRect();
                    modalImage.src = e.target.dataset.slipSrc;
                    modal.style.opacity = 0;
                    modal.style.display = 'block';

                    // Use a short timeout to allow the image to load and get its dimensions
                    setTimeout(() => {
                        const modalRect = modal.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        const margin = 15; // Margin from viewport edges and cursor

                        let top = rect.top;
                        let left = rect.right + margin;

                        // If it goes off-screen right, position it to the left
                        if (left + modalRect.width > viewportWidth - margin) {
                            left = rect.left - modalRect.width - margin;
                        }

                        // If it goes off-screen bottom, align bottom of modal with viewport bottom
                        if (top + modalRect.height > viewportHeight - margin) {
                            top = viewportHeight - modalRect.height - margin;
                        }

                        // Ensure it doesn't go off-screen top
                        if (top < margin) {
                            top = margin;
                        }

                        // Ensure it doesn't go off-screen left
                        if (left < margin) {
                            left = margin;
                        }

                        modal.style.top = `${top}px`;
                        modal.style.left = `${left}px`;
                        modal.style.opacity = 1;
                    }, 50);
                });

                thumb.addEventListener('mouseleave', () => {
                    hideTimeout = setTimeout(() => {
                        modal.style.opacity = 0;
                        setTimeout(() => modal.style.display = 'none',
                        200); // Hide after transition
                    }, 100);
                });
            });

            // Also hide the modal if the mouse enters the modal itself and then leaves
            modal.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
            });
            modal.addEventListener('mouseleave', () => {
                hideTimeout = setTimeout(() => {
                    modal.style.opacity = 0;
                    setTimeout(() => modal.style.display = 'none', 200);
                }, 100);
            });

        });
    </script>
@endpush
