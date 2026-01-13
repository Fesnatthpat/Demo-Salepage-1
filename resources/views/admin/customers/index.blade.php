@extends('layouts.admin')

@section('title', 'จัดการลูกค้า')
@section('page-title', 'รายชื่อลูกค้าทั้งหมด')

@section('content')
<div class="card bg-white shadow-md">
    <div class="card-body">
        <!-- Header & Search -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="card-title">ลูกค้าทั้งหมด ({{ $customers->total() }})</h2>
            <form action="{{ route('admin.customers.index') }}" method="GET">
                <div class="form-control">
                    <div class="relative">
                        <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..." class="input input-bordered w-full sm:w-64 pr-10" value="{{ request('search') }}">
                        <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ชื่อ</th>
                        <th>อีเมล</th>
                        <th>เบอร์โทร</th>
                        <th>สถานะ LINE</th>
                        <th>วันที่ลงทะเบียน</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr class="hover">
                            <td>{{ $customer->id }}</td>
                            <td>
                                <div class="font-bold">{{ $customer->name }}</div>
                                <div class="text-sm opacity-50">{{ $customer->line_id ? 'LINE Linked' : 'No LINE' }}</div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                            <td>
                                @if ($customer->line_id)
                                    <span class="badge badge-success">เชื่อมต่อแล้ว</span>
                                @else
                                    <span class="badge badge-warning">ไม่ได้เชื่อมต่อ</span>
                                @endif
                            </td>
                            <td>{{ $customer->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-ghost btn-sm">
                                    <i class="fas fa-eye mr-2"></i>
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                @if(request('search'))
                                    ไม่พบลูกค้าที่ตรงกับคำค้นหา "{{ request('search') }}"
                                @else
                                    ยังไม่มีข้อมูลลูกค้าในระบบ
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
