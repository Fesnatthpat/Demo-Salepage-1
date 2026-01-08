@extends('layouts.admin')

@section('title', 'จัดการราคาสินค้า SalePage')
@section('page-title', 'รายการราคาสินค้า SalePage')

@section('content')
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                <div class="flex-1">
                    <h2 class="card-title">ราคาทั้งหมด ({{ $salePages->total() }})</h2>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                    <form action="{{ route('admin.salepages.index') }}" method="GET" class="w-full sm:w-auto">
                        <div class="flex w-full sm:w-auto">
                            <input type="text" name="search" 
                                   placeholder="ค้นหาชื่อ หรือรหัสสินค้า..."
                                   class="input input-bordered w-full sm:w-64 rounded-r-none focus:outline-none" 
                                   value="{{ request('search') }}">
                            
                            <button type="submit" class="btn btn-square btn-primary rounded-l-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <a href="{{ route('admin.salepages.create') }}"
                        class="btn btn-primary w-full sm:w-auto">
                        <i class="fas fa-plus mr-2"></i>
                        เพิ่มราคาใหม่
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success shadow-sm mb-4">
                    <div>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Status Filter -->
            <div class="mb-4">
                <div class="join">
                    <a href="{{ route('admin.salepages.index', ['search' => request('search')]) }}" 
                       class="join-item btn btn-sm {{ !request()->has('status') ? 'btn-active' : '' }}">ทั้งหมด</a>
                    <a href="{{ route('admin.salepages.index', ['status' => 1, 'search' => request('search')]) }}" 
                       class="join-item btn btn-sm {{ request('status') == '1' ? 'btn-active' : '' }}">ใช้งาน</a>
                    <a href="{{ route('admin.salepages.index', ['status' => 0, 'search' => request('search')]) }}" 
                       class="join-item btn btn-sm {{ request('status') == '0' ? 'btn-active' : '' }}">ไม่ใช้งาน</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-right">ราคา SalePage</th>
                            <th class="text-right">ส่วนลด</th>
                            <th>รายละเอียด</th>
                            <th class="text-center">สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salePages as $salePage)
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-12 h-12">
                                                <img src="https://crm.kawinbrothers.com/product_images/{{ $salePage->product->pd_img ?? 'default.png' }}"
                                                    alt="{{ $salePage->product->pd_name ?? '' }}">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold w-64 truncate">
                                                {{ $salePage->product->pd_name ?? 'ไม่พบสินค้าหลัก' }}</div>
                                            <div class="text-sm opacity-50">{{ $salePage->pd_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">฿{{ number_format($salePage->pd_sp_price, 2) }}</td>
                                <td class="text-right text-red-500">- ฿{{ number_format($salePage->pd_sp_discount, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="text-sm text-gray-600 line-clamp-2 max-w-xs">{{ $salePage->pd_sp_details ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($salePage->pd_sp_active == 1)
                                        <span class="btn bg-green-500 text-white">ใช้งาน</span>
                                    @else
                                        <span class="btn bg-gray-500 text-white">ไม่ใช้งาน</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('admin.salepages.edit', $salePage) }}"
                                            class="btn btn-sm btn-warning text-white">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>

                                        <label for="delete-modal-{{ $salePage->id }}"
                                            class="btn btn-sm btn-error text-white">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </label>
                                    </div>

                                    <input type="checkbox" id="delete-modal-{{ $salePage->id }}" class="modal-toggle" />
                                    <div class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">ยืนยันการลบ</h3>
                                            <p class="py-4">คุณแน่ใจหรือไม่ว่าต้องการลบราคานี้?</p>
                                            <div class="modal-action">
                                                <form action="{{ route('admin.salepages.destroy', $salePage) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">ลบ</button>
                                                </form>
                                                <label for="delete-modal-{{ $salePage->id }}" class="btn">ยกเลิก</label>
                                            </div>
                                        </div>
                                        <label class="modal-backdrop" for="delete-modal-{{ $salePage->id }}">Close</label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500">
                                    ยังไม่มีข้อมูลราคาสำหรับ SalePage
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $salePages->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection