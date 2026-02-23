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
            border: 1px solid #4b5563;
            /* gray-600 */
        }

        .slip-thumbnail:hover {
            transform: scale(1.1);
            border-color: #10b981;
            /* emerald-500 */
        }
    </style>
@endsection

@section('content')
    <div class="card bg-gray-800 shadow-lg border border-gray-700">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title text-gray-100">ออเดอร์ทั้งหมด <span
                        class="text-gray-500 text-sm font-normal">({{ $orders->total() }})</span></h2>
                <form action="{{ route('admin.orders.index') }}" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหา รหัส, ชื่อลูกค้า..."
                                class="input input-bordered w-full sm:w-64 pr-10 bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mb-4 overflow-x-auto">
                <div class="join bg-gray-700 p-1 rounded-lg border border-gray-600">
                    {{-- All Statuses --}}
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'all'])) }}"
                        class="join-item btn btn-sm border-none {{ !request('status') || request('status') == 'all' ? 'bg-emerald-600 text-white' : 'bg-transparent text-gray-400 hover:text-white hover:bg-gray-600' }}">
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
                            class="join-item btn btn-sm border-none {{ request('status') == $id ? 'bg-emerald-600 text-white' : 'bg-transparent text-gray-400 hover:text-white hover:bg-gray-600' }}">
                            {{ $text }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-900/50 text-gray-400">
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
                            <tr class="border-b border-gray-700 hover:bg-gray-700/50 transition-colors group">
                                <td class="align-middle">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-semibold text-emerald-400">{{ $order->ord_code }}</span>
                                        <button onclick="copyToClipboard('{{ $order->ord_code }}')"
                                            class="btn btn-ghost btn-xs btn-square text-gray-500 hover:text-emerald-400 hover:bg-gray-700"
                                            title="คลิกเพื่อคัดลอก">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="font-bold text-gray-200">{{ $order->shipping_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                </td>
                                <td class="align-middle text-right text-gray-400">
                                    ฿{{ number_format($order->total_price, 2) }}</td>
                                <td class="align-middle text-right text-red-400">
                                    -฿{{ number_format($order->total_discount, 2) }}</td>
                                <td class="align-middle text-right font-bold text-emerald-400">
                                    ฿{{ number_format($order->net_amount, 2) }}
                                </td>
                                <td class="align-middle text-center">
                                    @if ($order->slip_path)
                                        <img src="{{ asset('storage/' . $order->slip_path) }}" alt="Slip"
                                            class="slip-thumbnail bg-gray-700"
                                            data-slip-src="{{ asset('storage/' . $order->slip_path) }}">
                                    @else
                                        <span class="text-xs text-gray-600">ไม่มีรูป</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <span
                                        class="badge border-none text-xs font-medium px-3 py-3
                                    @switch($order->status_id)
                                        @case(1) bg-yellow-900/50 text-yellow-300 @break
                                        @case(2) bg-blue-900/50 text-blue-300 @break
                                        @case(3) bg-indigo-900/50 text-indigo-300 @break
                                        @case(4) bg-emerald-900/50 text-emerald-300 @break
                                        @case(5) bg-red-900/50 text-red-300 @break
                                        @default bg-gray-700 text-gray-400
                                    @endswitch">
                                        {{ $statusMap[$order->status_id] ?? 'ไม่ทราบสถานะ' }}
                                    </span>
                                </td>
                                <td class="align-middle text-gray-400">{{ $order->ord_date->format('d M Y, H:i') }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="text-blue-400 font-semibold hover:text-blue-300 hover:underline flex items-center gap-1 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        ดู
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-12 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-700 mb-3"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        @if (request('search'))
                                            ไม่พบออเดอร์ที่ตรงกับ "{{ request('search') }}"
                                        @else
                                            ยังไม่มีข้อมูลออเดอร์ในระบบ
                                        @endif
                                    </div>
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

    {{-- Modal Preview รูปสลิป --}}
    <div id="slip-preview-modal" style="display: none; position: fixed; z-index: 1000; pointer-events: none;">
        <img src="" alt="Slip Preview"
            style="max-width: 350px; height: auto; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); background-color: #1f2937; border: 2px solid #374151;">
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#1f2937',
                    color: '#fff',
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
                    modal.style.transition = 'opacity 0.2s ease-in-out';

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
                        if (top < margin) top = margin;
                        if (left < margin) left = margin;

                        modal.style.top = `${top}px`;
                        modal.style.left = `${left}px`;
                        modal.style.opacity = 1;
                    }, 10);
                });

                thumb.addEventListener('mouseleave', () => {
                    hideTimeout = setTimeout(() => {
                        modal.style.opacity = 0;
                        setTimeout(() => modal.style.display = 'none', 200);
                    }, 100);
                });
            });
        });
    </script>
@endpush
