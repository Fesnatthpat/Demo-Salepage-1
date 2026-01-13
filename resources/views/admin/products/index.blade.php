@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('page-title', 'รายการสินค้าทั้งหมด')

@section('content')
    <div class="card bg-white shadow-md">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                <div class="flex-1">
                    <h2 class="card-title">สินค้าทั้งหมด ({{ $products->total() }})</h2>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-end">
                    {{-- Search Form - Updated route to admin.products.index --}}
                    <form action="{{ route('admin.products.index') }}" method="GET" class="w-full sm:w-auto">
                        <div class="flex w-full sm:w-auto">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสสินค้า..."
                                class="input input-bordered w-full sm:w-64 rounded-r-none focus:outline-none"
                                value="{{ request('search') }}">

                            <button type="submit" class="btn btn-square btn-primary rounded-l-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Create Button - Updated route to admin.products.create --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-full sm:w-auto">
                        <i class="fas fa-plus mr-2"></i>
                        เพิ่มสินค้าใหม่
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

            <!-- Status Filter - Updated route to admin.products.index -->
            <div class="mb-4">
                <div class="join">
                    <a href="{{ route('admin.products.index', ['search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ !request()->has('status') ? 'btn-active' : '' }}">ทั้งหมด</a>
                    <a href="{{ route('admin.products.index', ['status' => 1, 'search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ request('status') == '1' ? 'btn-active' : '' }}">ใช้งาน</a>
                    <a href="{{ route('admin.products.index', ['status' => 0, 'search' => request('search')]) }}"
                        class="join-item btn btn-sm {{ request('status') == '0' ? 'btn-active' : '' }}">ไม่ใช้งาน</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-right">ราคา</th>
                            <th class="text-right">ส่วนลด</th>
                            <th>รายละเอียด</th>
                            <th class="text-center">สถานะ</th>
                            <th>Actions</th> {{-- Changed from empty th to Actions --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product) {{-- Changed $salePages to $products, $salePage to $product --}}
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar">
                                            <div class="rounded border w-12 h-12">
                                                <img src="{{ asset('storage/' . (optional($product->images->first())->image_path ?? 'img.png')) }}"
                                                    alt="{{ $product->pd_sp_name ?? 'Product Image' }}">
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold w-64 truncate">
                                                {{ $product->pd_sp_name ?? 'ไม่พบสินค้าหลัก' }}</div>
                                            <div class="text-sm opacity-50">{{ $product->pd_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">฿{{ number_format($product->pd_sp_price, 2) }}</td>
                                <td class="text-right text-red-500">- ฿{{ number_format($product->pd_sp_discount, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="text-sm text-gray-600 line-clamp-2 max-w-xs">{{ $product->pd_sp_details ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($product->pd_sp_active == 1)
                                        <span class="btn bg-green-500 text-white btn-xs">ใช้งาน</span> {{-- Added btn-xs for smaller badge-like button --}}
                                    @else
                                        <span class="btn bg-gray-500 text-white btn-xs">ไม่ใช้งาน</span> {{-- Added btn-xs --}}
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-center items-center gap-2">
                                        {{-- Updated route to admin.products.edit --}}
                                        <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}"
                                            class="btn btn-sm btn-warning text-white">
                                            <i class="fas fa-edit"></i> แก้ไข
                                        </a>

                                        {{-- Updated modal ID and route to admin.products.destroy --}}
                                        <label for="delete-modal-{{ $product->pd_sp_id }}"
                                            class="btn btn-sm btn-error text-white">
                                            <i class="fas fa-trash-alt"></i> ลบ
                                        </label>
                                    </div>

                                    {{-- Updated modal ID and route to admin.products.destroy --}}
                                    <input type="checkbox" id="delete-modal-{{ $product->pd_sp_id }}" class="modal-toggle" />
                                    <div class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">ยืนยันการลบ</h3>
                                            <p class="py-4">คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า <span class="font-bold">"{{ $product->pd_sp_name }}"</span>?<br>การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                                            <div class="modal-action">
                                                <form action="{{ route('admin.products.destroy', $product->pd_sp_id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">ลบสินค้า</button>
                                                </form>
                                                <label for="delete-modal-{{ $product->pd_sp_id }}" class="btn">ยกเลิก</label>
                                            </div>
                                        </div>
                                        <label class="modal-backdrop" for="delete-modal-{{ $product->pd_sp_id }}">Close</label>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500">
                                    ยังไม่มีข้อมูลสินค้าในระบบ
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-8">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection