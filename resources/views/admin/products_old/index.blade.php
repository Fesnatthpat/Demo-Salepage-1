@extends('layouts.admin')

@section('title', 'จัดการสินค้า')
@section('page-title', 'รายการสินค้าทั้งหมด')

@section('content')
<div class="card bg-white shadow-md">
    <div class="card-body">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="card-title">สินค้าทั้งหมด ({{ $products->total() }})</h2>
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                 <form action="{{ route('admin.products.index') }}" method="GET" class="w-full sm:w-auto">
                    <div class="form-control">
                        <div class="relative w-full">
                            <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสสินค้า..." class="input input-bordered w-full sm:w-56 pr-10" value="{{ request('search') }}">
                            <button type="submit" class="absolute top-0 right-0 rounded-l-none btn btn-square btn-sm btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-block sm:btn-wide mt-2 sm:mt-0">
                    <i class="fas fa-plus mr-2"></i>
                    เพิ่มสินค้าใหม่
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>รูปภาพ</th>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th class="text-right">ราคา</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center" style="width: 120px;">จัดการ</th> </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="hover">
                            <td>
                                <div class="avatar">
                                    <div class="mask mask-squircle w-12 h-12">
                                        <img src="https://crm.kawinbrothers.com/product_images/{{ $product->pd_img ?? 'default.png' }}" alt="{{ $product->pd_name }}">
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono">{{ $product->pd_code }}</td>
                            <td>
                                <div class="font-bold w-64 truncate">{{ $product->pd_name }}</div>
                            </td>
                            <td class="text-right">฿{{ number_format($product->pd_price, 2) }}</td>
                            <td class="text-center">
                                @if($product->pd_status == 1)
                                    <span class="badge badge-success">เผยแพร่</span>
                                @else
                                    <span class="badge badge-ghost">ฉบับร่าง</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-center items-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning text-white">
                                        แก้ไข
                                    </a>
                                    
                                    <label for="delete-modal-{{ $product->pd_id }}" class="btn btn-sm btn-error text-white">
                                        ลบ
                                    </label>
                                </div>
                                <input type="checkbox" id="delete-modal-{{ $product->pd_id }}" class="modal-toggle" />
                                <div class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg text-error">
                                            ยืนยันการลบ
                                        </h3>
                                        <p class="py-4">คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า <span class="font-bold">"{{ $product->pd_name }}"</span>?<br>การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                                        <div class="modal-action">
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">ลบสินค้า</button>
                                            </form>
                                            <label for="delete-modal-{{ $product->pd_id }}" class="btn">ยกเลิก</label>
                                        </div>
                                    </div>
                                    <label class="modal-backdrop" for="delete-modal-{{ $product->pd_id }}">Close</label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                @if(request('search'))
                                    ไม่พบสินค้าที่ตรงกับคำค้นหา "{{ request('search') }}"
                                @else
                                    ยังไม่มีข้อมูลสินค้าในระบบ
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $products->appends(request()->query())->links() }}
        </div>

        @if(session('success'))
            <div class="toast toast-end">
                <div class="alert alert-success">
                    <div>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection