@extends('layouts.admin')

@section('title', 'จัดการลูกค้า')
@section('page-title', 'รายชื่อลูกค้าทั้งหมด')

@section('content')
    <div class="card bg-gray-800 shadow-lg border border-gray-700">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                <h2 class="card-title text-gray-100">ลูกค้าทั้งหมด <span
                        class="text-gray-500 text-sm font-normal">({{ $customers->total() }})</span></h2>
                <form action="{{ route('admin.customers.index') }}" method="GET">
                    <div class="form-control">
                        <div class="relative">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..."
                                class="input input-bordered w-full sm:w-64 pr-10 bg-gray-700 border-gray-600 text-gray-200 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="absolute top-0 right-0 rounded-l-none btn btn-square btn-primary bg-emerald-600 hover:bg-emerald-700 border-none text-white">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <th>#</th>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>อายุ</th>
                            <th>เพศ</th>
                            <th>วันเกิด</th>
                            <th>สถานะ LINE</th>
                            <th>วันที่ลงทะเบียน</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-700/50 transition-colors border-b border-gray-700 last:border-0">
                                <td class="text-gray-500">{{ $customer->id }}</td>
                                <td>
                                    <div class="font-bold text-gray-200">{{ $customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->line_id ? 'LINE Linked' : 'No LINE' }}
                                    </div>
                                </td>
                                <td class="text-gray-400">{{ $customer->email }}</td>
                                <td class="text-gray-400">{{ $customer->phone ?? 'N/A' }}</td>
                                <td class="text-gray-400">{{ $customer->age ?? 'N/A' }}</td>
                                <td class="text-gray-400">{{ $customer->gender ?? 'N/A' }}</td>
                                <td class="text-gray-400">
                                    {{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    @if ($customer->line_id)
                                        <span class="badge badge-success text-white">เชื่อมต่อแล้ว</span>
                                    @else
                                        <span
                                            class="badge badge-warning bg-yellow-600 border-none text-white">ไม่ได้เชื่อมต่อ</span>
                                    @endif
                                </td>
                                <td class="text-gray-500">{{ $customer->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                        class="btn btn-ghost btn-sm text-gray-400 hover:text-emerald-400">
                                        <i class="fas fa-eye mr-2"></i>
                                        รายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-12 text-gray-500">
                                    @if (request('search'))
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

            <div class="mt-8">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
