@extends('layouts.admin')

@section('title', 'จัดการลูกค้า')
@section('page-title', 'รายชื่อลูกค้าทั้งหมด')

@section('content')
    <div class="bg-gray-800 shadow-xl rounded-2xl border border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-100 flex items-center">
                        <i class="fas fa-users text-emerald-400 mr-3"></i>
                        ลูกค้าทั้งหมด 
                        <span class="ml-3 px-3 py-1 bg-gray-700 text-gray-400 text-xs font-bold rounded-full border border-gray-600">
                            {{ number_format($customers->total()) }} ราย
                        </span>
                    </h2>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    {{-- ปุ่ม Export Excel --}}
                    <a href="{{ route('admin.customers.export', request()->query()) }}"
                        class="flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-500 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-green-900/20 order-2 sm:order-1">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>

                    {{-- ฟอร์มค้นหา --}}
                    <form action="{{ route('admin.customers.index') }}" method="GET" class="w-full lg:w-72 order-1 sm:order-2">
                        <div class="relative group">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล, เบอร์โทร..."
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-gray-100 placeholder-gray-500 focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 outline-none transition-all"
                                value="{{ request('search') }}">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-500 text-xs"></i>
                            </div>
                            @if(request('search'))
                                <a href="{{ route('admin.customers.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-300">
                                    <i class="fas fa-times-circle text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @php
                // แปลงค่าเพศเป็นภาษาไทย
                $genderMap = ['male' => 'ชาย', 'female' => 'หญิง', 'm' => 'ชาย', 'f' => 'หญิง', 'other' => 'อื่นๆ'];
            @endphp

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs text-gray-400 border-b border-gray-700 bg-gray-900/30 uppercase tracking-wider">
                            <th class="px-4 py-4 font-bold">#</th>
                            <th class="px-4 py-4 font-bold">ชื่อ / LINE</th>
                            <th class="px-4 py-4 font-bold">อีเมล / เบอร์โทร</th>
                            <th class="px-4 py-4 font-bold">เพศ / อายุ</th>
                            <th class="px-4 py-4 font-bold">สถานะ LINE</th>
                            <th class="px-4 py-4 font-bold">วันที่ลงทะเบียน</th>
                            <th class="px-4 py-4 font-bold text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-700/30 transition-colors border-b border-gray-700 last:border-0">
                                <td class="px-4 py-5 text-gray-500">{{ $customer->id }}</td>
                                <td class="px-4 py-5">
                                    <div class="font-bold text-gray-100">{{ $customer->name }}</div>
                                    <div class="text-[10px] text-emerald-500 font-bold uppercase mt-0.5">{{ $customer->line_id ? 'LINE Connected' : '' }}</div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-gray-300">{{ $customer->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $customer->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="text-gray-300">{{ $customer->gender ? $genderMap[strtolower($customer->gender)] ?? $customer->gender : 'N/A' }}</span>
                                    <span class="mx-1 text-gray-600">/</span>
                                    <span class="text-gray-300">{{ $customer->age ?? 'N/A' }} ปี</span>
                                </td>
                                <td class="px-4 py-5">
                                    @if ($customer->line_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-900/50 text-emerald-400 border border-emerald-800/50 uppercase">
                                            Linked
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-700 text-gray-400 border border-gray-600 uppercase">
                                            Not Linked
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-gray-400 text-xs">
                                        {{ $customer->created_at ? $customer->created_at->locale('th')->translatedFormat('d M ') . ($customer->created_at->year + 543) : 'N/A' }}
                                    </div>
                                    <div class="text-[10px] text-gray-600 mt-0.5">{{ $customer->created_at ? $customer->created_at->format('H:i') : '' }}</div>
                                </td>
                                <td class="px-4 py-5 text-right">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-bold rounded-lg transition-all border border-gray-600">
                                        <i class="fas fa-eye mr-2"></i>
                                        ดูข้อมูล
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-20 text-gray-500">
                                    <i class="fas fa-user-slash text-4xl mb-4 block opacity-20"></i>
                                    ไม่พบข้อมูลลูกค้าในระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-4">
                @forelse ($customers as $customer)
                    <div class="bg-gray-900/50 border border-gray-700 rounded-2xl p-4 space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 font-bold border border-emerald-500/20">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-100 text-base">{{ $customer->name }}</div>
                                    <div class="text-[10px] text-gray-500">ID: #{{ $customer->id }}</div>
                                </div>
                            </div>
                            @if ($customer->line_id)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-500 text-white uppercase tracking-tighter">LINE</span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <div class="text-gray-500 mb-1 uppercase tracking-widest font-black text-[9px]">Email</div>
                                <div class="text-gray-300 truncate">{{ $customer->email }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 mb-1 uppercase tracking-widest font-black text-[9px]">Phone</div>
                                <div class="text-gray-300">{{ $customer->phone ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500 mb-1 uppercase tracking-widest font-black text-[9px]">Gender/Age</div>
                                <div class="text-gray-300">
                                    {{ $customer->gender ? $genderMap[strtolower($customer->gender)] ?? $customer->gender : 'N/A' }} ({{ $customer->age ?? 'N/A' }})
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500 mb-1 uppercase tracking-widest font-black text-[9px]">Registered</div>
                                <div class="text-gray-300">{{ $customer->created_at ? $customer->created_at->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-700">
                            <a href="{{ route('admin.customers.show', $customer) }}"
                                class="flex items-center justify-center w-full py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-bold rounded-xl transition-all border border-gray-600">
                                <i class="fas fa-info-circle mr-2"></i> รายละเอียดลูกค้า
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">ไม่มีข้อมูลลูกค้า</div>
                @endforelse
            </div>

            <div class="mt-8 px-4">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
