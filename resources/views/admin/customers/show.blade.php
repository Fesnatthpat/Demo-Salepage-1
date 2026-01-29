@extends('layouts.admin')

@section('title', 'รายละเอียดลูกค้า')
@section('page-title')
    <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-900">ลูกค้า</a> /
    <span class="text-gray-900">รายละเอียดลูกค้า: {{ $customer->name }}</span>
@endsection

@section('content')
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6">
                <h2 class="card-title">ข้อมูลลูกค้า</h2>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-ghost">
                    <i class="fas fa-arrow-left mr-2"></i>
                    กลับ
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- ชื่อ --}}
                <div>
                    <p class="text-sm text-gray-500">ชื่อ</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customer->name }}</p>
                </div>
                
                {{-- อีเมล (แก้ไข: จัดให้ปุ่มอยู่ติดข้อความ) --}}
                <div>
                    <p class="text-sm text-gray-500">อีเมล</p>
                    <div class="flex items-center justify-start gap-2">
                        <span class="text-lg font-bold text-gray-800">{{ $customer->email ?? '-' }}</span>
                        @if($customer->email)
                            <button onclick="copyToClipboard('{{ $customer->email }}', this)" 
                                    class="btn btn-xs btn-circle btn-ghost text-gray-400 hover:text-gray-800"
                                    title="คัดลอกอีเมล">
                                <i class="fas fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- เบอร์โทรศัพท์ (แก้ไข: จัดให้ปุ่มอยู่ติดข้อความ) --}}
                <div>
                    <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                    <div class="flex items-center justify-start gap-2">
                        <span class="text-lg font-bold text-gray-800">{{ $customer->phone ?? '-' }}</span>
                        @if($customer->phone)
                            <button onclick="copyToClipboard('{{ $customer->phone }}', this)" 
                                    class="btn btn-xs btn-circle btn-ghost text-gray-400 hover:text-gray-800"
                                    title="คัดลอกเบอร์โทรศัพท์">
                                <i class="fas fa-copy"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">อายุ</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customer->age ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">เพศ</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customer->gender ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">วันเกิด</p>
                    <p class="text-lg font-bold text-gray-800">
                        {{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : '-' }}
                    </p>
                </div>
                
                {{-- สถานะ LINE (Tooltip + Copy) --}}
                <div>
                    <p class="text-sm text-gray-500">สถานะ LINE</p>
                    <div class="text-lg font-bold text-gray-800 flex items-center justify-start gap-2">
                        @if ($customer->line_id)
                            <span class="badge badge-success">เชื่อมต่อแล้ว</span>
                            
                            {{-- Tooltip แสดง Line ID เมื่อเอาเมาส์ชี้ --}}
                            <div class="tooltip" data-tip="{{ $customer->line_id }}">
                                <button onclick="copyToClipboard('{{ $customer->line_id }}', this)" 
                                        class="btn btn-xs btn-circle btn-ghost text-gray-500 hover:text-gray-800">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="badge badge-warning">ไม่ได้เชื่อมต่อ</span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">วันที่ลงทะเบียน</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customer->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">อัปเดตล่าสุด</p>
                    <p class="text-lg font-bold text-gray-800">{{ $customer->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="divider"></div>

            <h3 class="card-title mb-4">ออเดอร์ล่าสุดของลูกค้า</h3>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>รหัสออเดอร์</th>
                            <th>ยอดสุทธิ</th>
                            <th>สถานะ</th>
                            <th>วันที่สั่งซื้อ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->orders as $order)
                            <tr class="hover">
                                <td class="font-mono">{{ $order->ord_code }}</td>
                                <td class="text-right">฿{{ number_format($order->net_amount, 2) }}</td>
                                <td class="text-center">
                                    @php
                                        // Assuming you have a status map like in AdminOrderController
                                        $statusMap = [
                                            1 => 'รอชำระเงิน',
                                            2 => 'แจ้งชำระเงินแล้ว',
                                            3 => 'กำลังเตรียมจัดส่ง',
                                            4 => 'จัดส่งแล้ว',
                                            5 => 'ยกเลิก',
                                        ];
                                        $statusText = $statusMap[$order->status_id] ?? 'ไม่ทราบสถานะ';
                                    @endphp
                                    <span
                                        class="badge 
                                    @switch($order->status_id)
                                        @case(1) badge-warning @break
                                        @case(2) badge-info @break
                                        @case(3) badge-success @break
                                        @case(4) badge-primary @break
                                        @default badge-ghost
                                    @endswitch
                                ">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>{{ $order->ord_date->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    ลูกค้ารายนี้ยังไม่มีออเดอร์
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- Script สำหรับฟังก์ชัน Copy --}}
    <script>
        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(function() {
                // เก็บ HTML เดิมของปุ่มไว้
                let originalContent = btn.innerHTML;
                
                // เปลี่ยนไอคอนให้เป็นสีเขียวและเป็นเครื่องหมายถูก
                btn.classList.remove('text-gray-400', 'text-gray-500');
                btn.classList.add('text-green-600');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                
                // คืนค่าไอคอนเดิมหลังจาก 2 วินาที
                setTimeout(function() {
                    btn.classList.remove('text-green-600');
                    btn.classList.add('text-gray-400'); // หรือ class สีเดิมที่คุณต้องการ
                    btn.innerHTML = originalContent;
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
@endsection