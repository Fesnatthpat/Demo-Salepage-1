@extends('layouts.admin')

@section('title', 'แก้ไขสินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">แก้ไขสินค้า: {{ $productSalepage->pd_sp_name }}</h3>
                </div>
                <form action="{{ route('admin.products.update', $productSalepage->pd_sp_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @include('admin.products._form')
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
