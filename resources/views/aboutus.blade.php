@extends('layout')

@section('content')
    <div class="bg-white min-h-screen">

        {{-- ================= 1. HERO SECTION ================= --}}
        <div class="relative bg-gray-900 py-20 lg:py-32 overflow-hidden">
            {{-- Background Image Overlay (สมมติว่าเป็นรูปหน้าร้านหรือเสื้อผ้าสวยๆ) --}}
            <div class="absolute inset-0 opacity-40">
                <img src="/images/store-bg.jpg" class="w-full h-full object-cover" alt="Background"
                    onerror="this.src='https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop'">
            </div>

            <div class="container mx-auto px-4 relative z-10 text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 tracking-tight">
                    สไตล์ที่ใช่ ในแบบที่เป็นคุณ
                </h1>
                <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    H&M-R Store มุ่งมั่นที่จะนำเสนอแฟชั่นคุณภาพสูง ดีไซน์ทันสมัย
                    ในราคาที่จับต้องได้ เพื่อให้ทุกคนสนุกกับการแต่งตัวได้ในทุกวัน
                </p>
            </div>
        </div>

        {{-- ================= 2. OUR STORY (Split Layout) ================= --}}
        <div class="container mx-auto px-4 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                {{-- Image Grid --}}
                <div class="relative">
                    <div class="grid grid-cols-2 gap-4">
                        <img src="https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&w=600&q=80"
                            class="rounded-2xl shadow-lg w-full h-64 object-cover transform translate-y-8">
                        <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=600&q=80"
                            class="rounded-2xl shadow-lg w-full h-64 object-cover">
                    </div>
                    {{-- Decorative Dot Pattern --}}
                    <div class="absolute -z-10 top-0 right-0 -mr-8 -mt-8 text-emerald-100">
                        <svg width="100" height="100" fill="currentColor" viewBox="0 0 100 100">
                            <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="2"></circle>
                            </pattern>
                            <rect width="100" height="100" fill="url(#dots)"></rect>
                        </svg>
                    </div>
                </div>

                {{-- Text Content --}}
                <div>
                    <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm">เรื่องราวของเรา</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-6">จากร้านเล็กๆ สู่ผู้นำแฟชั่นออนไลน์
                    </h2>
                    <div class="space-y-4 text-gray-600 leading-relaxed text-lg">
                        <p>
                            H&M-R Store ก่อตั้งขึ้นในปี 2020 ด้วยความหลงใหลในเสื้อผ้าสไตล์ Oversize และ Streetwear
                            เราเริ่มต้นจากห้องเล็กๆ เพียงห้องเดียว ด้วยความเชื่อที่ว่า "เสื้อผ้าที่ดี ไม่จำเป็นต้องแพง"
                        </p>
                        <p>
                            ตลอด 5 ปีที่ผ่านมา เราคัดสรรเนื้อผ้า Cotton 100% เกรดพรีเมียม และใส่ใจในทุกขั้นตอนการตัดเย็บ
                            เพื่อให้ลูกค้าได้รับสินค้าที่มีคุณภาพที่สุด
                            เราภูมิใจที่ได้เป็นส่วนหนึ่งในการสร้างความมั่นใจให้กับลูกค้ากว่า 10,000 คนทั่วประเทศ
                        </p>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-8 mt-10 border-t border-gray-100 pt-8">
                        <div>
                            <span class="block text-3xl font-bold text-emerald-600">5+</span>
                            <span class="text-sm text-gray-500">ปีแห่งประสบการณ์</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-bold text-emerald-600">10k+</span>
                            <span class="text-sm text-gray-500">ลูกค้าที่วางใจ</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-bold text-emerald-600">99%</span>
                            <span class="text-sm text-gray-500">ความพึงพอใจ</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= 3. OUR VALUES (Cards) ================= --}}
        <div class="bg-gray-50 py-16 lg:py-24">
            <div class="container mx-auto px-4">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">ทำไมต้องเลือก H&M-R?</h2>
                    <p class="text-gray-600">เรายึดมั่นในมาตรฐานการบริการและคุณภาพสินค้า เพื่อประสบการณ์ที่ดีที่สุดของคุณ
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Value 1 --}}
                    <div
                        class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition text-center group">
                        <div
                            class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">คุณภาพพรีเมียม</h3>
                        <p class="text-gray-500 leading-relaxed">เราใช้ผ้า Cotton 100% เกรดดีที่สุด นุ่ม ใส่สบาย
                            ระบายอากาศได้ดี ไม่ย้วยง่าย</p>
                    </div>

                    {{-- Value 2 --}}
                    <div
                        class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition text-center group">
                        <div
                            class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">จัดส่งรวดเร็ว</h3>
                        <p class="text-gray-500 leading-relaxed">สินค้าพร้อมส่งทุกชิ้น จัดส่งภายใน 24 ชม. ถึงมือคุณภายใน 1-3
                            วันทำการ</p>
                    </div>

                    {{-- Value 3 --}}
                    <div
                        class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition text-center group">
                        <div
                            class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">บริการด้วยใจ</h3>
                        <p class="text-gray-500 leading-relaxed">ทีมงานพร้อมตอบคำถามและช่วยเหลือตลอดเวลา
                            รับประกันความพึงพอใจ 100%</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 4. OUR TEAM ================= --}}
        <div class="container mx-auto px-4 py-16 lg:py-24">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">ทีมงานของเรา</h2>
                <p class="text-gray-600">ผู้อยู่เบื้องหลังความสำเร็จและการคัดสรรสินค้าคุณภาพ</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Team Member 1 --}}
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name=Jeff+C&background=0D9488&color=fff&size=200"
                        class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900">คุณเจฟ</h3>
                    <p class="text-emerald-600 text-sm font-medium">ผู้ก่อตั้ง & CEO</p>
                </div>

                {{-- Team Member 2 --}}
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name=Sara+M&background=random&color=fff&size=200"
                        class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900">คุณซาร่า</h3>
                    <p class="text-emerald-600 text-sm font-medium">หัวหน้าดีไซน์เนอร์</p>
                </div>

                {{-- Team Member 3 --}}
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name=Mike+T&background=random&color=fff&size=200"
                        class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900">คุณไมค์</h3>
                    <p class="text-emerald-600 text-sm font-medium">ผู้จัดการฝ่ายการตลาด</p>
                </div>

                {{-- Team Member 4 --}}
                <div class="text-center">
                    <img src="https://ui-avatars.com/api/?name=Ann+P&background=random&color=fff&size=200"
                        class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-white shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900">คุณแอน</h3>
                    <p class="text-emerald-600 text-sm font-medium">บริการลูกค้า</p>
                </div>
            </div>
        </div>

        {{-- ================= 5. CTA SECTION ================= --}}
        <div class="bg-emerald-600 py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">พร้อมที่จะอัปเกรดสไตล์ของคุณรึยัง?</h2>
                <p class="text-emerald-100 text-lg mb-8 max-w-xl mx-auto">
                    เลือกชมสินค้าคอลเลกชันใหม่ล่าสุดของเราได้แล้ววันนี้ พร้อมโปรโมชั่นพิเศษมากมาย</p>
                <a href="/"
                    class="btn bg-white text-emerald-600 hover:bg-gray-100 border-none px-8 text-lg rounded-full shadow-lg">
                    ช้อปเลย
                </a>
            </div>
        </div>

    </div>
@endsection
