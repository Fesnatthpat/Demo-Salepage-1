@extends('layout')

@section('title', 'ติดต่อเรา')

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-center mb-8">ติดต่อเรา</h1>

    <div class="max-w-4xl mx-auto space-y-8">
        @forelse ($contacts as $contact)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold mb-3">{{ $contact->title }}</h2>
                
                @if($contact->content)
                    <p class="text-gray-700 mb-4">{!! nl2br(e($contact->content)) !!}</p>
                @endif

                <div class="space-y-3 text-gray-800">
                    @if($contact->address)
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt w-5 text-center mr-3 mt-1 text-gray-500"></i>
                            <span>{!! nl2br(e($contact->address)) !!}</span>
                        </div>
                    @endif
                    @if($contact->phone)
                        <div class="flex items-center">
                            <i class="fas fa-phone w-5 text-center mr-3 text-gray-500"></i>
                            <span>{{ $contact->phone }}</span>
                        </div>
                    @endif
                    @if($contact->email)
                        <div class="flex items-center">
                            <i class="fas fa-envelope w-5 text-center mr-3 text-gray-500"></i>
                            <a href="mailto:{{ $contact->email }}" class="hover:text-blue-600">{{ $contact->email }}</a>
                        </div>
                    @endif
                    @if($contact->map_url)
                        <div class="flex items-center">
                            <i class="fas fa-map w-5 text-center mr-3 text-gray-500"></i>
                            <a href="{{ $contact->map_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-blue-600">ดูแผนที่</a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <p class="text-gray-500">ไม่มีข้อมูลการติดต่อ</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
