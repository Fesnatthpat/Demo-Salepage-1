@extends('layout')

@section('title', 'ติดต่อติดใจ')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="container mx-auto px-4 py-12 min-h-screen">
        <div class="max-w-4xl mx-auto">

            @forelse ($contacts as $contact)
                {{-- Main Header --}}
                <div class="text-center mb-10">
                    <h1 class="text-4xl font-bold text-[#1e3a5f] mb-4">{{ $contact->title ?? 'ติดต่อติดใจ' }}</h1>
                    <p class="text-gray-500 text-base">
                        เรารูปยินดีให้บริการและพร้อมตอบทุกคำถามของคุณ กรุณาติดต่อเราผ่านช่องทางด้านล่าง
                    </p>
                </div>

                {{-- Contact Card --}}
                <div class="bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.08)] overflow-hidden border border-gray-100">
                    <div class="p-8 md:p-12 bg-white">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                            {{-- Left Column: ข้อมูลการติดต่อ --}}
                            <div>
                                <h2 class="text-xl font-bold text-[#1e3a5f] mb-6">ข้อมูลการติดต่อ</h2>
                                <div class="space-y-5">
                                    {{-- ที่อยู่ --}}
                                    @if ($contact->address)
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-6 mt-1">
                                                <i class="fas fa-map-marker-alt text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600 leading-relaxed">
                                                    <strong>บริษัท ติดใจ จำกัด</strong><br>
                                                    {!! nl2br(e($contact->address)) !!}
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- โทรศัพท์ --}}
                                    @if ($contact->phone)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-6">
                                                <i class="fas fa-phone-alt text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600">โทรศัพท์: {{ $contact->phone }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- อีเมล --}}
                                    @if ($contact->email)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-6">
                                                <i class="fas fa-envelope text-red-500 text-lg"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-gray-600">อีเมล: {{ $contact->email }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Right Column: เวลาทำการ --}}
                            <div>
                                <h2 class="text-xl font-bold text-[#1e3a5f] mb-6">เวลาทำการ</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันจันทร์ - วันศุกร์:</span>
                                        <span>9:00 น. - 18:00 น.</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>วันเสาร์ - วันอาทิตย์:</span>
                                        <span class="text-red-500">ปิดทำการ</span>
                                    </div>
                                    <div class="mt-6">
                                        <p class="text-sm text-gray-400 leading-relaxed italic">
                                            หากคุณมีข้อสงสัยหรือต้องการความช่วยเหลือเร่งด่วน
                                            กรุณาโทรศัพท์หาเราในช่วงเวลาทำการ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Map Section --}}
                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-[#1e3a5f] text-center mb-6">แผนที่</h2>
                            <div class="rounded-xl overflow-hidden border border-gray-200 h-[400px] w-full relative">
                                @if ($contact->map_url)
                                    @if (str_contains($contact->map_url, '<iframe'))
                                        {!! str_replace('<iframe', '<iframe class="absolute inset-0 w-full h-full border-0"', $contact->map_url) !!}
                                    @else
                                        <iframe
                                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d124043.88894239043!2d100.25641349726563!3d13.695933799999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e297398da35fa3%3A0xb8c32c9766ac1ed4!2z4Lia4Lij4Li04Lip4Lix4LiXIOC4geC4p-C4tOC4mSDguJrguKPguLLguYDguJjguK3guKPguYzguKog4LiI4Liz4LiB4Lix4LiU!5e0!3m2!1sth!2sth!4v1773804143774!5m2!1sth!2sth"
                                            width="799" height="399" style="border:0;" allowfullscreen=""
                                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    @endif
                                @else
                                    <div class="flex flex-col items-center justify-center h-full bg-gray-50 text-gray-400">
                                        <i class="fas fa-map-marked-alt text-5xl mb-3"></i>
                                        <p>ยังไม่ได้ระบุตำแหน่งบนแผนที่</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <i class="far fa-address-book text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-400">ไม่พบข้อมูลการติดต่อ</p>
                </div>
            @endforelse

        </div>
    </div>
@endsection
