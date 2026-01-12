@extends('layouts.admin')

@section('title', 'จัดการสินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">รายการสินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> เพิ่มสินค้าใหม่
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>รหัสสินค้า</th>
                                <th>ชื่อสินค้า</th>
                                <th>รายละเอียด</th>
                                <th>รูปภาพหลัก</th>
                                <th style="width: 150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->pd_sp_id }}</td>
                                <td>{{ $product->pd_code }}</td>
                                <td>{{ $product->pd_sp_name }}</td>
                                <td>{{ Str::limit($product->pd_sp_details, 50) }}</td>
                                <td>
                                    @if ($product->images->first())
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->images->first()->image_alt }}" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        ไม่มีรูปภาพ
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->pd_sp_id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->pd_sp_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> ลบ</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">ไม่พบสินค้า</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
