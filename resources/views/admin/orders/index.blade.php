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
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title">ออเดอร์ทั้งหมด ({{ $orders->total() }})</h2>
                <form action="{{ route('admin.orders.index') }}" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหา รหัสออเดอร์, ชื่อลูกค้า..."
                                class="input input-bordered w-full sm:w-64 pr-10" value="{{ request('search') }}">
                            <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

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
                            <tr class="hover group">
                                <td class="align-middle">
                                    {{-- ★★★ ส่วนแก้ไข: ใช้ SVG แทน FontAwesome เพื่อให้เห็นไอคอนแน่นอน ★★★ --}}
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-semibold text-gray-700">{{ $order->ord_code }}</span>
                                        <button onclick="copyToClipboard('{{ $order->ord_code }}')"
                                            class="btn btn-ghost btn-xs btn-square text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all"
                                            title="คลิกเพื่อคัดลอก">
                                            {{-- SVG Icon รูปกระดาษซ้อนกัน (Copy) --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
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
                                        <span class="text-gray-400">ไม่มีรูป</span>
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
                                        class="text-blue-600 font-semibold hover:underline flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
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

            <div class="mt-8">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; transition: opacity 0.2s;">
        <img src="" alt="Slip Preview"
            style="max-width: 350px; height: auto; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); background-color: white;">
    </div>
@endsection

@push('scripts')
    {{-- ตรวจสอบว่ามี SweetAlert2 หรือยัง ถ้าไม่มีให้เพิ่มบรรทัดนี้ --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ฟังก์ชันสำหรับคัดลอก
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: false,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'คัดลอกรหัสออเดอร์แล้ว'
                });
            }).catch(err => {
                console.error('ไม่สามารถคัดลอกได้: ', err);
            });
        }

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

                    setTimeout(() => {
                        const modalRect = modal.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        const margin = 15;

                        let top = rect.top;
                        let left = rect.right + margin;

                        if (left + modalRect.width > viewportWidth - margin) {
                            left = rect.left - modalRect.width - margin;
                        }

                        if (top + modalRect.height > viewportHeight - margin) {
                            top = viewportHeight - modalRect.height - margin;
                        }

                        if (top < margin) {
                            top = margin;
                        }

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
                        setTimeout(() => modal.style.display = 'none', 200);
                    }, 100);
                });
            });

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