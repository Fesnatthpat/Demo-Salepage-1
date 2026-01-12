@extends('layouts.admin')

@section('title', 'เพิ่มสินค้าใหม่')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">เพิ่มสินค้าใหม่</h3>
                </div>
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @include('admin.products._form')
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
