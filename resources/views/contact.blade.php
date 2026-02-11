@extends('layout')

@section('title', 'ติดต่อเรา | Salepage Demo')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 max-w-3xl mx-auto mt-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-6">ติดต่อเรา</h1>

        <p class="text-gray-600 text-lg text-center mb-8">
            เรายินดีให้บริการและพร้อมตอบทุกคำถามของคุณ กรุณาติดต่อเราผ่านช่องทางด้านล่าง
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-3">ข้อมูลการติดต่อ</h2>
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-gray-700">บริษัท ติดใจ จำกัด<br>123 ถนนสุขุมวิท แขวงคลองเตย<br>เขตคลองเตย กรุงเทพฯ 10110</p>
                </div>
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.158l-2.293 1.147a1 1 0 00-.447.894v0a1 1 0 00.998.998h.001v0a1 1 0 00.998-.998v0a1 1 0 00-.447-.894l-2.293-1.147a1 1 0 01-.502-1.158l1.498-4.493a1 1 0 01.948-.684H5a2 2 0 00-2 2v0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 5a2 2 0 00-2-2h-3.28a1 1 0 01-.948-.684l-1.498 4.493a1 1 0 01.502 1.158l2.293 1.147a1 1 0 00.447.894v0a1 1 0 00-.998.998h-.001v0a1 1 0 00-.998-.998v0a1 1 0 00.447-.894l2.293-1.147a1 1 0 01.502-1.158l-1.498-4.493a1 1 0 01-.948-.684H18a2 2 0 012 2v0z" />
                    </svg>
                    <p class="text-gray-700">โทรศัพท์: 02-123-4567</p>
                </div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-700">อีเมล: contact@tidjai.com</p>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-700 mb-3">เวลาทำการ</h2>
                <p class="text-gray-700 mb-2">วันจันทร์ - วันศุกร์: 9:00 น. - 18:00 น.</p>
                <p class="text-gray-700 mb-2">วันเสาร์ - วันอาทิตย์: ปิดทำการ</p>
                <p class="text-gray-600 text-sm mt-4">
                    หากคุณมีข้อสงสัยหรือต้องการความช่วยเหลือเร่งด่วน กรุณาโทรศัพท์หาเราในช่วงเวลาทำการ
                </p>
            </div>
        </div>

        <div class="text-center mt-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-3">แผนที่</h2>
            <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-md">
                <!-- Replace with an actual Google Maps embed iframe if available -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.5471460592395!2d100.56230491483018!3d13.746132790350742!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29e0000000001%3A0x2000000000000000!2sSukhumvit%20Road!5e0!3m2!1sen!2sth!4v1678888888888!5m2!1sen!2sth" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
