@extends('layouts.admin')

@section('title', 'ตั้งค่าหน้าเว็บไซต์ & Live Preview')

@section('content')
    {{-- ขยาย Container ให้กว้างขึ้นเพื่อรองรับจอคอมพิวเตอร์ได้ดีขึ้น --}}
    <div class="container mx-auto px-4 pb-32 max-w-7xl" x-data="siteSettings()">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 mt-6">
            <h1 class="text-3xl font-extrabold text-gray-100 flex items-center tracking-tight">
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center mr-4 shadow-lg shadow-emerald-500/10 border border-emerald-500/30">
                    <i class="fas fa-magic text-emerald-400 text-xl"></i>
                </div>
                ตกแต่งหน้าเว็บไซต์
            </h1>

            <div class="flex flex-col gap-3 w-full md:w-auto">
                @if (session('success'))
                    <div
                        class="px-6 py-3 bg-emerald-500/20 border border-emerald-500/50 text-emerald-300 rounded-xl flex items-center gap-3 animate-fade-in shadow-lg shadow-emerald-500/10 w-full">
                        <i class="fas fa-check-circle text-lg"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="px-6 py-4 bg-red-500/20 border border-red-500/50 text-red-300 rounded-xl flex flex-col gap-2 animate-fade-in shadow-lg shadow-red-500/10 w-full">
                        <div class="flex items-center gap-2 font-bold text-lg">
                            <i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด:
                        </div>
                        <ul class="text-sm list-disc list-inside space-y-1 ml-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tab Navigation (Scrollable on mobile) --}}
        <div class="mb-8 overflow-x-auto hide-scroll-bar pb-2 -mx-4 px-4 md:mx-0 md:px-0">
            <div
                class="flex items-center gap-2 bg-gray-800/50 p-1.5 rounded-2xl border border-gray-700 w-max shadow-lg backdrop-blur-md">
                <button type="button" @click="activeTab = 'homepage'"
                    class="px-5 md:px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2.5 text-sm md:text-base whitespace-nowrap"
                    :class="activeTab === 'homepage' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' :
                        'text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10'">
                    <i class="fas fa-home"></i> หน้าหลัก (Homepage)
                </button>
                <button type="button" @click="activeTab = 'all_products'"
                    class="px-5 md:px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2.5 text-sm md:text-base whitespace-nowrap"
                    :class="activeTab === 'all_products' ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/50' :
                        'text-gray-400 hover:text-purple-400 hover:bg-purple-500/10'">
                    <i class="fas fa-store"></i> หน้าสินค้าทั้งหมด (All Products)
                </button>
                <button type="button" @click="activeTab = 'site_settings'"
                    class="px-5 md:px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2.5 text-sm md:text-base whitespace-nowrap"
                    :class="activeTab === 'site_settings' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' :
                        'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'">
                    <i class="fas fa-globe"></i> ตั้งค่าร้านค้า (Site Settings)
                </button>
                <button type="button" @click="activeTab = 'footer'"
                    class="px-5 md:px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2.5 text-sm md:text-base whitespace-nowrap"
                    :class="activeTab === 'footer' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50' :
                        'text-gray-400 hover:text-indigo-400 hover:bg-indigo-500/10'">
                    <i class="fas fa-shoe-prints"></i> ส่วนท้ายเว็บ (Footer)
                </button>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="relative">
            @csrf

            {{-- 🟣 TAB: FOOTER --}}
            <div class="space-y-8" x-show="activeTab === 'footer'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                style="display: none;">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- 1. Footer Info & Logo --}}
                    <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2.5 bg-indigo-500/10 rounded-xl"><i
                                    class="fas fa-info-circle text-indigo-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">ข้อมูลทั่วไปส่วนท้าย (Footer Info)</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-6">
                            {{-- Logo Upload for Footer (Same as Site Logo) --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center gap-6 bg-gray-900/50 p-4 rounded-2xl border border-gray-700 mb-6">
                                <div
                                    class="w-20 h-20 bg-gray-900 rounded-xl border-2 border-dashed border-gray-600 overflow-hidden relative group shrink-0 hover:border-indigo-500/50 transition-colors mx-auto sm:mx-0">
                                    @php
                                        $currentLogo = \App\Models\SiteSetting::get('site_logo');
                                        $logoUrl = $currentLogo
                                            ? asset('storage/' . $currentLogo)
                                            : asset('images/logo/logo1.png');
                                    @endphp
                                    <img src="{{ $logoUrl }}" class="w-full h-full object-contain p-2"
                                        id="footer-logo-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-xl"></i>
                                    </div>
                                    <input type="file" name="site_logo" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="document.getElementById('footer-logo-preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('logo-preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                                <div class="space-y-1 text-center sm:text-left">
                                    <p class="text-sm text-gray-300 font-bold">โลโก้ร้านค้า</p>
                                    <p class="text-xs text-gray-500">จะแสดงที่ส่วนบนและส่วนท้ายของเว็บไซต์</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        ชื่อร้าน (Site Name)
                                    </label>
                                    <input type="text" name="settings[site_name]"
                                        value="{{ $settings['site_name'] ?? 'ติดใจ' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        ข้อความ Copyright
                                    </label>
                                    <input type="text" name="settings[footer_copyright]"
                                        value="{{ $settings['footer_copyright'] ?? 'All right reserved by Tidjai Co., Ltd.' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    คำโปรย (Footer Slogan)
                                </label>
                                <textarea name="settings[footer_slogan]" rows="3"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">{{ $settings['footer_slogan'] ?? "ของกินเล่นสูตรเด็ด ต้นตำรับความอร่อย\nคัดสรรวัตถุดิบคุณภาพเพื่อคุณ" }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Contact Information --}}
                    <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2.5 bg-green-500/10 rounded-xl"><i
                                    class="fas fa-address-book text-green-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">ข้อมูลติดต่อ (Contact Info)</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        เบอร์โทรศัพท์
                                    </label>
                                    <input type="text" name="settings[site_phone]"
                                        value="{{ $settings['site_phone'] ?? '02-123-4567' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-green-500 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                        อีเมล
                                    </label>
                                    <input type="email" name="settings[site_email]"
                                        value="{{ $settings['site_email'] ?? 'contact@tidjai.com' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-green-500 outline-none transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    ที่อยู่
                                </label>
                                <textarea name="settings[site_address]" rows="3"
                                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-green-500 outline-none transition-all">{{ $settings['site_address'] ?? "บริษัท ติดใจ จำกัด\n123 ถนนสุขุมวิท แขวงคลองเตย\nเขตคลองเตย กรุงเทพฯ 10110" }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Social Media Links --}}
                    <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2.5 bg-blue-500/10 rounded-xl"><i
                                    class="fas fa-share-alt text-blue-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">โซเชียลมีเดีย (Social Media)</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-blue-600 rounded-lg flex items-center justify-center text-white text-lg">
                                        <i class="fab fa-facebook-f"></i>
                                    </div>
                                    <input type="text" name="settings[social_facebook]"
                                        value="{{ $settings['social_facebook'] ?? '#' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-sky-400 rounded-lg flex items-center justify-center text-white text-lg">
                                        <i class="fab fa-twitter"></i>
                                    </div>
                                    <input type="text" name="settings[social_twitter]"
                                        value="{{ $settings['social_twitter'] ?? '#' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-sky-400 outline-none transition-all">
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-lg">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                    <input type="text" name="settings[social_instagram]"
                                        value="{{ $settings['social_instagram'] ?? '#' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-pink-500 outline-none transition-all">
                                </div>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 flex-shrink-0 bg-green-500 rounded-lg flex items-center justify-center text-white text-lg">
                                        <i class="fab fa-line"></i>
                                    </div>
                                    <input type="text" name="settings[social_line]"
                                        value="{{ $settings['social_line'] ?? '#' }}"
                                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white focus:ring-2 focus:ring-green-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Footer Columns Links --}}
                    <div class="lg:col-span-2 bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2.5 bg-amber-500/10 rounded-xl"><i
                                    class="fas fa-link text-amber-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">จัดการลิงก์เมนูส่วนท้าย (Footer Menu Links)</h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                                {{-- Help Center Column --}}
                                <div class="space-y-6">
                                    <div class="flex items-center gap-2 pb-2 border-b border-gray-700">
                                        <i class="fas fa-question-circle text-amber-400"></i>
                                        <input type="text" name="settings[faq_badge]"
                                            value="{{ $settings['faq_badge'] ?? 'ศูนย์ช่วยเหลือ' }}"
                                            class="bg-transparent border-none text-gray-100 font-bold text-lg focus:ring-0 w-full p-0">
                                    </div>
                                    <div class="space-y-4">
                                        @for ($i = 1; $i <= 4; $i++)
                                            <div class="grid grid-cols-2 gap-3">
                                                <input type="text"
                                                    name="settings[footer_col2_link{{ $i }}_label]"
                                                    value="{{ $settings['footer_col2_link' . $i . '_label'] ?? ($i == 1 ? 'ติดตามสถานะคำสั่งซื้อ' : ($i == 2 ? 'การรับประกันสินค้า' : ($i == 3 ? 'การคืนสินค้าและการคืนเงิน' : 'วิธีการสั่งซื้อ'))) }}"
                                                    placeholder="ชื่อลิงก์"
                                                    class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-xs text-white">
                                                <input type="text"
                                                    name="settings[footer_col2_link{{ $i }}_url]"
                                                    value="{{ $settings['footer_col2_link' . $i . '_url'] ?? ($i == 1 ? route('order.tracking.form') : '#') }}"
                                                    placeholder="URL (เช่น /about หรือ http://...)"
                                                    class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-xs text-white">
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                {{-- About Column --}}
                                <div class="space-y-6">
                                    <div class="flex items-center gap-2 pb-2 border-b border-gray-700">
                                        <i class="fas fa-info-circle text-indigo-400"></i>
                                        <input type="text" name="settings[footer_about_title]"
                                            value="{{ $settings['footer_about_title'] ?? 'เกี่ยวกับติดใจ' }}"
                                            class="bg-transparent border-none text-gray-100 font-bold text-lg focus:ring-0 w-full p-0">
                                    </div>
                                    <div class="space-y-4">
                                        @for ($i = 1; $i <= 4; $i++)
                                            <div class="grid grid-cols-2 gap-3">
                                                <input type="text"
                                                    name="settings[footer_col3_link{{ $i }}_label]"
                                                    value="{{ $settings['footer_col3_link' . $i . '_label'] ?? ($i == 1 ? 'เรื่องราวของเรา' : ($i == 2 ? 'บทความน่ารู้' : ($i == 3 ? 'นโยบายความเป็นส่วนตัว' : 'ข้อกำหนดและเงื่อนไข'))) }}"
                                                    placeholder="ชื่อลิงก์"
                                                    class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-xs text-white">
                                                <input type="text"
                                                    name="settings[footer_col3_link{{ $i }}_url]"
                                                    value="{{ $settings['footer_col3_link' . $i . '_url'] ?? ($i == 1 ? '/about' : '#') }}"
                                                    placeholder="URL"
                                                    class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-xs text-white">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔵 TAB: SITE SETTINGS --}}
            <div class="space-y-8" x-show="activeTab === 'site_settings'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- 1. Logo & Background --}}
                    <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2.5 bg-blue-500/10 rounded-xl"><i
                                    class="fas fa-image text-blue-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">รูปภาพเว็บไซต์ Background</h3>
                        </div>
                        <div class="p-6 md:p-8 space-y-8">
                            {{-- Site Cover Image (Background) --}}
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider">
                                    ภาพพื้นหลังเว็บไซต์ (Site Background Image)
                                </label>
                                <div
                                    class="relative w-full aspect-[16/9] md:aspect-[2/1] bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden group hover:border-blue-500/50 transition-colors">
                                    @php
                                        $currentCover = \App\Models\SiteSetting::get('site_cover_image');
                                        $coverUrl = $currentCover
                                            ? asset('storage/' . $currentCover)
                                            : asset('images/BG/fruit2.png');
                                    @endphp
                                    <img src="{{ $coverUrl }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        id="cover-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-4xl mb-2"></i>
                                        <span class="text-white font-medium text-sm">คลิกเพื่อเปลี่ยนรูปภาพ</span>
                                    </div>
                                    <input type="file" name="site_cover_image" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="document.getElementById('cover-preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                                <div
                                    class="flex items-start gap-2 text-blue-400 bg-blue-500/10 p-3 rounded-lg border border-blue-500/20">
                                    <i class="fas fa-info-circle mt-0.5"></i>
                                    <p class="text-xs md:text-sm">ภาพพื้นหลังที่จะแสดงทั่วทั้งเว็บไซต์ แนะนำขนาด:
                                        <strong>1920x1080 px</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🟢 TAB: HOMEPAGE --}}
            <div class="space-y-8" x-show="activeTab === 'homepage'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0">

                {{-- 0. PAGE CONTENT (Titles) --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2.5 bg-amber-500/10 rounded-xl"><i
                                class="fas fa-heading text-amber-400 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">หัวข้อหน้าหลัก (Homepage Titles)</h3>
                    </div>
                    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                Badge เมนูแนะนำ (เช่น Recommended)
                            </label>
                            <input type="text" name="settings[home_recommended_badge]"
                                value="{{ $settings['home_recommended_badge'] ?? 'Recommended' }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                หัวข้อเมนูแนะนำ (รองรับ HTML)
                            </label>
                            <input type="text" name="settings[home_recommended_title]"
                                value="{{ $settings['home_recommended_title'] ?? 'เมนูแนะนำ <span class=\"text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-red-500\">ต้องลอง!</span>' }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">
                                หัวข้อส่วน "6 เหตุผล"
                            </label>
                            <input type="text" name="settings[home_reasons_title]"
                                value="{{ $settings['home_reasons_title'] ?? '6 เหตุผลทำไมต้องเลือกเรา' }}"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                {{-- 0. GENERAL SETTINGS (Logo) --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2.5 bg-emerald-500/10 rounded-xl"><i
                                class="fas fa-cog text-emerald-400 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">ตั้งค่าทั่วไป (General Settings)</h3>
                    </div>
                    <div class="p-6 md:p-8">
                        {{-- Site Logo --}}
                        <div class="space-y-4 max-w-lg">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider">
                                โลโก้เว็บไซต์ (Site Logo)
                            </label>
                            <div
                                class="flex flex-col sm:flex-row sm:items-center gap-6 bg-gray-900/50 p-4 rounded-2xl border border-gray-700">
                                <div
                                    class="w-28 h-28 bg-gray-900 rounded-2xl border-2 border-dashed border-gray-600 overflow-hidden relative group shrink-0 hover:border-emerald-500/50 transition-colors mx-auto sm:mx-0">
                                    @php
                                        $currentLogo = \App\Models\SiteSetting::get('site_logo');
                                        $logoUrl = $currentLogo
                                            ? asset('storage/' . $currentLogo)
                                            : asset('images/logo/logo1.png');
                                    @endphp
                                    <img src="{{ $logoUrl }}" class="w-full h-full object-contain p-3"
                                        id="logo-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-2xl"></i>
                                    </div>
                                    <input type="file" name="site_logo" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="document.getElementById('logo-preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                                <div class="space-y-2 text-center sm:text-left">
                                    <p class="text-sm text-gray-300 font-medium">อัปโหลดโลโก้ร้านค้าของคุณ</p>
                                    <p class="text-xs text-gray-500"><i class="fas fa-check text-emerald-500 mr-1"></i>
                                        แนะนำเป็นไฟล์ PNG พื้นหลังโปร่งใส</p>
                                    <p class="text-xs text-gray-500"><i class="fas fa-compress text-emerald-500 mr-1"></i>
                                        ขนาดที่แนะนำ: 512x512 px</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 1. HERO SLIDER (Homepage) --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div
                        class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-red-500/10 rounded-xl"><i class="fas fa-images text-red-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">Hero Slides (แบนเนอร์หน้าหลัก)</h3>
                        </div>
                        <button type="button" @click="addHeroSlide()"
                            class="w-full sm:w-auto btn bg-red-600 hover:bg-red-500 text-white border-none rounded-xl px-5 py-2.5 font-medium transition-all shadow-lg shadow-red-900/20 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> เพิ่มแบนเนอร์ใหม่
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- 💡 กล่องคำอธิบาย --}}
                        <div
                            class="bg-blue-900/20 border border-blue-700/30 rounded-2xl p-4 md:p-5 flex flex-col sm:flex-row gap-4 items-start text-sm text-blue-300">
                            <div class="p-2 bg-blue-500/20 rounded-lg shrink-0">
                                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                            </div>
                            <div class="space-y-1">
                                <strong class="text-blue-200 text-base">คำแนะนำขนาดรูปภาพแบนเนอร์</strong>
                                <p>เพื่อความสวยงามและแสดงผลได้ดีในทุกอุปกรณ์ แนะนำให้ใช้รูปภาพขนาด <strong>1600 x 600
                                        พิกเซล</strong></p>
                                <p class="text-xs text-blue-400/70 pt-1">
                                    * รูปภาพจะถูกปรับสัดส่วนอัตโนมัติตามขนาดหน้าจอ (มือถือ 16:10, แท็บเล็ต 2.5:1,
                                    คอมพิวเตอร์ 3:1)
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <template x-for="(slide, index) in hero_slides" :key="index">
                                <div
                                    class="bg-gray-900/50 rounded-2xl border border-gray-700 overflow-hidden group relative hover:border-gray-500 transition-colors">
                                    <button type="button" @click="removeHeroSlide(index)"
                                        class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 transition-all z-20 shadow-lg">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>

                                    <input type="hidden" :name="`hero_banners[${index}][id]`" :value="slide.id">
                                    <input type="hidden" :name="`hero_banners[${index}][existing_path]`"
                                        :value="slide.existing_path">

                                    <div class="p-4 space-y-4">
                                        <div
                                            class="w-full aspect-[21/9] bg-gray-800 rounded-xl border border-gray-600 overflow-hidden relative group/img">
                                            <img :src="slide.image" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-camera text-white text-2xl"></i>
                                            </div>
                                            <input type="file" :name="`hero_banners[${index}][image]`"
                                                accept="image/*" @change="previewImage($event, 'hero_slides', index)"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        </div>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-link text-gray-500"></i>
                                            </div>
                                            <input type="text" :name="`hero_banners[${index}][link_url]`"
                                                x-model="slide.link_url" placeholder="ลิงก์เมื่อคลิกแบนเนอร์ (ถ้ามี)..."
                                                class="w-full bg-gray-800 border-gray-600 rounded-xl text-sm text-gray-200 pl-10 px-4 py-3 focus:border-red-500 focus:ring-red-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ส่วนเนื้อหารอง แบ่ง Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    {{-- คอลัมน์ซ้าย --}}
                    <div class="space-y-8">
                        {{-- Allergy Banner --}}
                        <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-3">
                                    <div class="p-2 bg-yellow-500/10 rounded-lg"><i
                                            class="fas fa-exclamation-triangle text-yellow-400"></i></div>
                                    ข้อมูลแพ้อาหาร
                                </h3>
                                <button type="button" x-show="allergy_img"
                                    @click="allergy_img = ''; document.getElementById('remove_allergy_image').value = '1'"
                                    class="text-sm text-red-400 hover:text-red-300 underline font-medium px-2 py-1">
                                    ลบรูปภาพ
                                </button>
                            </div>
                            <div class="p-6">
                                <div
                                    class="relative w-full aspect-[4/1] bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden group hover:border-yellow-500/50 transition-colors">
                                    <img :src="allergy_img || 'https://via.placeholder.com/800x200?text=Upload+Allergy+Banner'"
                                        class="w-full h-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center">
                                        <i class="fas fa-upload text-white text-3xl mb-2"></i>
                                    </div>
                                    <input type="file" name="allergy_image" accept="image/*"
                                        @change="previewImage($event, 'allergy_img'); document.getElementById('remove_allergy_image').value = '0'"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <input type="hidden" name="remove_allergy_image" id="remove_allergy_image"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        {{-- Service Bar --}}
                        <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-3">
                                    <div class="p-2 bg-purple-500/10 rounded-lg"><i
                                            class="fas fa-concierge-bell text-purple-400"></i></div>
                                    บริการ (Service Bar)
                                </h3>
                                <button type="button" @click="addService()"
                                    class="text-sm bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-xl transition-colors font-medium">
                                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="(svc, index) in services" :key="index">
                                        <div
                                            class="p-3 bg-gray-900/50 rounded-xl border border-gray-700 flex gap-3 items-center relative group hover:border-purple-500/30 transition-colors">
                                            <button type="button" @click="removeService(index)"
                                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 text-xs shadow-md transition-all z-10">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <div class="flex-shrink-0 w-12 h-12 bg-gray-800 rounded-lg flex items-center justify-center text-purple-400 border border-gray-600 cursor-pointer hover:bg-purple-500/10 transition-colors"
                                                @click="openIconPicker('service', index)" title="คลิกเพื่อเปลี่ยนไอคอน">
                                                <i :class="svc.icon" class="text-xl"></i>
                                            </div>

                                            <div class="flex-grow">
                                                <input type="text" :name="`services[${index}][title]`"
                                                    x-model="svc.title" placeholder="กรอกข้อความบริการ..."
                                                    class="w-full bg-transparent border-b border-gray-600 text-sm text-gray-200 py-2 focus:ring-0 focus:border-purple-500 transition-colors px-1">
                                                <input type="hidden" :name="`services[${index}][icon]`"
                                                    x-model="svc.icon">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- คอลัมน์ขวา --}}
                    <div class="space-y-8">
                        {{-- Secondary Sliders --}}
                        <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-3">
                                    <div class="p-2 bg-blue-500/10 rounded-lg"><i class="fas fa-tags text-blue-400"></i>
                                    </div>
                                    แบนเนอร์รอง (Secondary Sliders)
                                </h3>
                                <button type="button" @click="addSecSlide()"
                                    class="text-sm bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl transition-colors font-medium">
                                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                                </button>
                            </div>
                            <div class="p-6 space-y-6">
                                {{-- 💡 กล่องคำอธิบาย --}}
                                <div
                                    class="bg-blue-900/20 border border-blue-700/30 rounded-2xl p-4 flex gap-3 text-sm text-blue-300">
                                    <i class="fas fa-info-circle mt-0.5 text-blue-400 text-lg"></i>
                                    <div>
                                        <strong class="text-blue-200 block mb-1">คำแนะนำขนาดรูปภาพ</strong>
                                        แนะนำให้ใช้รูปภาพขนาด <strong>800 x 320 พิกเซล</strong> (สัดส่วน 2.5:1)
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="(slide, index) in sec_slides" :key="index">
                                        <div
                                            class="p-4 bg-gray-900/50 rounded-2xl border border-gray-700 relative group hover:border-gray-500 transition-colors">
                                            <button type="button" @click="removeSecSlide(index)"
                                                class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 text-sm z-20 shadow-md transition-all">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <input type="hidden" :name="`secondary_banners[${index}][id]`"
                                                :value="slide.id">
                                            <input type="hidden" :name="`secondary_banners[${index}][existing_path]`"
                                                :value="slide.existing_path">

                                            <div
                                                class="w-full aspect-[2.5/1] bg-gray-800 rounded-xl border border-gray-600 overflow-hidden relative mb-3 group/img">
                                                <img :src="slide.image" class="w-full h-full object-cover">
                                                <div
                                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                    <i class="fas fa-camera text-white text-xl"></i>
                                                </div>
                                                <input type="file" :name="`secondary_banners[${index}][image]`"
                                                    accept="image/*" @change="previewImage($event, 'sec_slides', index)"
                                                    class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full">
                                            </div>

                                            <div class="relative">
                                                <i
                                                    class="fas fa-link absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs"></i>
                                                <input type="text" :name="`secondary_banners[${index}][link_url]`"
                                                    x-model="slide.link_url" placeholder="ลิงก์..."
                                                    class="w-full bg-gray-800 border-gray-600 rounded-lg text-xs text-gray-200 pl-8 pr-3 py-2 focus:border-blue-500">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- 6 Reasons --}}
                        <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-3">
                                    <div class="p-2 bg-emerald-500/10 rounded-lg"><i
                                            class="fas fa-th text-emerald-400"></i></div>
                                    เหตุผลที่ควรเลือกเรา (6 Reasons)
                                </h3>
                                <button type="button" @click="addReason()"
                                    class="text-sm bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl transition-colors font-medium">
                                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <template x-for="(reason, index) in reasons" :key="index">
                                        <div
                                            class="p-4 bg-gray-900/50 rounded-2xl border border-gray-700 flex gap-4 items-start relative group hover:border-emerald-500/30 transition-colors">
                                            <button type="button" @click="removeReason(index)"
                                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 text-xs shadow-md transition-all">
                                                <i class="fas fa-times"></i>
                                            </button>

                                            <div class="flex-shrink-0 w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center text-emerald-400 border border-gray-600 cursor-pointer hover:bg-emerald-500/10 transition-colors mt-1"
                                                @click="openIconPicker('reason', index)" title="คลิกเพื่อเปลี่ยนไอคอน">
                                                <i :class="reason.icon" class="text-xl"></i>
                                            </div>

                                            <div class="flex-grow space-y-2">
                                                <input type="text" :name="`reasons[${index}][title]`"
                                                    x-model="reason.title" placeholder="หัวข้อ"
                                                    class="w-full bg-transparent border-b border-gray-600 text-sm font-bold text-emerald-400 px-1 py-1 focus:ring-0 focus:border-emerald-500 transition-colors">
                                                <input type="hidden" :name="`reasons[${index}][icon]`"
                                                    x-model="reason.icon">

                                                <textarea :name="`reasons[${index}][description]`" x-model="reason.description" rows="2"
                                                    placeholder="คำอธิบาย..."
                                                    class="w-full bg-gray-800 border border-gray-600 rounded-lg text-xs text-gray-300 px-3 py-2 focus:border-emerald-500 focus:ring-0 custom-scrollbar"></textarea>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer Review Images (แยกเป็น Section กว้างเต็มหน้า) --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl mt-8">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-200 flex items-center gap-3">
                            <div class="p-2 bg-amber-500/10 rounded-lg"><i class="fas fa-comment-dots text-amber-400"></i>
                            </div>
                            รีวิวจากลูกค้า (Review Images)
                        </h3>
                        <button type="button" @click="addReviewImage()"
                            class="text-sm bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-xl transition-colors font-medium">
                            <i class="fas fa-plus mr-1"></i> เพิ่มรูปรีวิว
                        </button>
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                            <template x-for="(review, index) in review_images" :key="index">
                                <div
                                    class="relative group aspect-square bg-gray-900 rounded-2xl border border-gray-600 overflow-hidden hover:border-amber-500/50 transition-colors shadow-md">
                                    <button type="button" @click="removeReviewImage(index)"
                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 text-xs z-20 shadow-md transition-all">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                    <input type="hidden" :name="`review_images[${index}][id]`" :value="review.id">
                                    <input type="hidden" :name="`review_images[${index}][existing_path]`"
                                        :value="review.existing_path">

                                    <img :src="review.image" class="w-full h-full object-cover">

                                    <div
                                        class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-2xl mb-1"></i>
                                        <span class="text-white text-[10px]">เปลี่ยนรูปภาพ</span>
                                    </div>
                                    <input type="file" :name="`review_images[${index}][image]`" accept="image/*"
                                        @change="previewImage($event, 'review_images', index)"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                    <div
                                        class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/80 to-transparent flex justify-between items-end pointer-events-none">
                                        <span class="px-2 py-1 bg-amber-500 text-black font-bold rounded text-[10px]">
                                            รูปที่ <span x-text="index+1"></span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div x-show="review_images.length === 0"
                            class="text-center py-12 bg-gray-900/50 rounded-2xl border border-dashed border-gray-600 text-gray-500 text-sm font-medium">
                            <i class="fas fa-images text-4xl mb-3 text-gray-600 block"></i>
                            ยังไม่มีรูปภาพรีวิว คลิกที่ปุ่ม "เพิ่มรูปรีวิว" ด้านบนเพื่อเริ่มต้น
                        </div>
                    </div>
                </div>

            </div>

            {{-- 🟣 TAB: ALL PRODUCTS --}}
            <div class="space-y-8" x-show="activeTab === 'all_products'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                {{-- 1. HERO SLIDER (All Products) --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div
                        class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-purple-500/10 rounded-xl"><i
                                    class="fas fa-layer-group text-purple-400 text-xl"></i></div>
                            <h3 class="font-bold text-lg text-gray-100">แบนเนอร์หน้ารวมสินค้า</h3>
                        </div>
                        <button type="button" @click="addAllProdHeroSlide()"
                            class="w-full sm:w-auto btn bg-purple-600 hover:bg-purple-500 text-white border-none rounded-xl px-5 py-2.5 font-medium transition-all shadow-lg shadow-purple-900/20 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> เพิ่มแบนเนอร์
                        </button>
                    </div>
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <template x-for="(slide, index) in all_products_hero_slides" :key="index">
                                <div
                                    class="bg-gray-900/50 rounded-2xl border border-gray-700 overflow-hidden group relative hover:border-gray-500 transition-colors">
                                    <button type="button" @click="removeAllProdHeroSlide(index)"
                                        class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 transition-all z-20 shadow-lg">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>

                                    <input type="hidden" :name="`all_products_hero_banners[${index}][id]`"
                                        :value="slide.id">
                                    <input type="hidden" :name="`all_products_hero_banners[${index}][existing_path]`"
                                        :value="slide.existing_path">

                                    <div class="p-4 space-y-4">
                                        <div
                                            class="w-full aspect-[21/9] bg-gray-800 rounded-xl border border-gray-600 overflow-hidden relative group/img">
                                            <img :src="slide.image" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <i class="fas fa-camera text-white text-2xl"></i>
                                            </div>
                                            <input type="file" :name="`all_products_hero_banners[${index}][image]`"
                                                accept="image/*"
                                                @change="previewImage($event, 'all_products_hero_slides', index)"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        </div>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-link text-gray-500"></i>
                                            </div>
                                            <input type="text" :name="`all_products_hero_banners[${index}][link_url]`"
                                                x-model="slide.link_url" placeholder="ลิงก์ปลายทาง..."
                                                class="w-full bg-gray-800 border-gray-600 rounded-xl text-sm text-gray-200 pl-10 px-4 py-3 focus:border-purple-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 2. CATEGORY MENU --}}
                <div class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
                    <div
                        class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-emerald-500/10 rounded-xl"><i
                                    class="fas fa-th-large text-emerald-400 text-xl"></i></div>
                            <h3 class="font-bold text-lg text-gray-100">Category Menu (หมวดหมู่สินค้า)</h3>
                        </div>
                        <button type="button" @click="addCategory()"
                            class="w-full sm:w-auto btn bg-emerald-600 hover:bg-emerald-500 text-white border-none rounded-xl px-5 py-2.5 font-medium transition-all shadow-lg shadow-emerald-900/20 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> เพิ่มหมวดหมู่
                        </button>
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <template x-for="(cat, index) in categories" :key="index">
                                <div
                                    class="bg-gray-900/50 rounded-2xl border border-gray-700 p-5 relative group hover:border-emerald-500/50 transition-colors shadow-md">
                                    <button type="button" @click="removeCategory(index)"
                                        class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center opacity-100 lg:opacity-0 group-hover:opacity-100 text-xs shadow-lg transition-all z-20">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <input type="hidden" :name="`categories[${index}][id]`" :value="cat.id">
                                    <input type="hidden" :name="`categories[${index}][existing_path]`"
                                        :value="cat.existing_path">

                                    <div class="flex flex-col items-center gap-4">
                                        <div class="relative w-24 h-24">
                                            <div
                                                class="w-full h-full bg-gray-800 rounded-2xl border-2 border-dashed border-gray-600 overflow-hidden relative group/img shadow-inner">
                                                <img :src="cat.image" class="w-full h-full object-cover">
                                                <div
                                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                    <i class="fas fa-camera text-white"></i>
                                                </div>
                                                <input type="file" :name="`categories[${index}][image]`"
                                                    accept="image/*" @change="previewImage($event, 'categories', index)"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            </div>

                                            <button type="button" @click="openIconPicker('category', index)"
                                                class="absolute -bottom-2 -right-2 w-10 h-10 rounded-xl bg-gray-800 border-2 border-gray-700 flex items-center justify-center text-emerald-400 hover:bg-emerald-500/20 hover:border-emerald-500/50 transition-colors shadow-lg z-20"
                                                title="เลือกไอคอน">
                                                <i :class="cat.icon" class="text-lg"></i>
                                            </button>
                                            <input type="hidden" :name="`categories[${index}][icon]`"
                                                x-model="cat.icon">
                                        </div>

                                        <div class="w-full text-center space-y-2">
                                            <div class="space-y-1">
                                                <input type="text" :name="`categories[${index}][name]`" x-model="cat.name"
                                                    placeholder="ชื่อหมวดหมู่..."
                                                    class="w-full bg-gray-800 border border-gray-600 rounded-xl text-sm text-center font-bold text-gray-200 px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                                                
                                                <input type="text" :name="`categories[${index}][slug]`" x-model="cat.slug"
                                                    placeholder="slug-url-path..."
                                                    class="w-full bg-gray-800/50 border border-gray-700 rounded-lg text-[11px] text-center text-gray-400 px-3 py-1.5 focus:border-emerald-500 focus:ring-0 transition-colors">
                                            </div>

                                            <div
                                                class="space-y-2 text-left bg-gray-800/50 p-3 rounded-xl border border-gray-700/50">
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-[10px] uppercase font-black text-gray-500 tracking-widest ml-1">หมวดหมู่หลัก</label>
                                                    <select :name="`categories[${index}][parent_id]`"
                                                        x-model="cat.parent_id"
                                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-[11px] text-gray-300 px-2 py-1.5 focus:border-emerald-500 focus:ring-0">
                                                        <option value="">-- เป็นหมวดหมู่หลัก --</option>
                                                        @foreach ($categories ?? [] as $c)
                                                            <template x-if="cat.id != {{ $c->id }}">
                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                            </template>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-[10px] uppercase font-black text-gray-500 tracking-widest ml-1">ผูกกับสินค้า</label>
                                                    <select :name="`categories[${index}][linked_product_id]`"
                                                        x-model="cat.linked_product_id"
                                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-[11px] text-gray-300 px-2 py-1.5 focus:border-emerald-500 focus:ring-0">
                                                        <option value="">-- ไม่เลือก --</option>
                                                        {{-- ✅ เพิ่ม ?? [] เพื่อป้องกันหน้าขาวหากไม่ได้ส่งตัวแปรมา --}}
                                                        @foreach ($products ?? [] as $p)
                                                            <option value="{{ $p->pd_sp_id }}">{{ $p->pd_sp_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="space-y-1">
                                                    <label
                                                        class="text-[10px] uppercase font-black text-gray-500 tracking-widest ml-1">ลิงก์แบบกำหนดเอง</label>
                                                    <input type="text" :name="`categories[${index}][link_url]`"
                                                        x-model="cat.link_url" placeholder="https://..."
                                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-[11px] text-gray-300 px-2 py-1.5 focus:border-emerald-500 focus:ring-0">
                                                </div>
                                            </div>

                                            <div
                                                class="inline-block px-3 py-1 bg-gray-800 rounded-full border border-gray-700 text-xs text-gray-400">
                                                <i class="fas fa-sort mr-1"></i> ลำดับที่: <span x-text="index+1"
                                                    class="font-bold text-emerald-400"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 💾 SAVE BUTTON (Floating on mobile, sticky on desktop) --}}
            <div
                class="fixed bottom-0 left-0 right-0 p-4 bg-gray-900/90 border-t border-gray-800 z-40 md:sticky md:bottom-6 md:bg-transparent md:border-none md:p-0 md:pt-10 flex justify-center shadow-[0_-10px_30px_rgba(0,0,0,0.5)] md:shadow-none">
                <button type="submit"
                    class="w-full md:w-auto bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3.5 md:py-4 px-8 md:px-12 rounded-2xl md:rounded-full shadow-xl shadow-emerald-900/50 transform transition-all hover:-translate-y-1 hover:scale-105 flex items-center justify-center gap-3 border border-emerald-400/30">
                    <i class="fas fa-save text-xl"></i>
                    <span class="text-lg">บันทึกการตั้งค่าทั้งหมด</span>
                </button>
            </div>
        </form>

        {{-- 🎨 ICON PICKER MODAL --}}
        <div x-show="showIconPicker"
            class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 sm:p-6"
            style="display: none;" @click.self="showIconPicker = false">
            <div
                class="bg-gray-800 border border-gray-700 rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div
                    class="flex justify-between items-center px-6 py-5 bg-gray-900 flex-shrink-0 border-b border-gray-700">
                    <h4 class="text-gray-100 font-bold text-lg flex items-center gap-3">
                        <div class="p-2 bg-purple-500/20 rounded-lg"><i class="fas fa-icons text-purple-400"></i></div>
                        เลือกไอคอน (<span x-text="activeIconType" class="capitalize text-purple-300"></span>)
                    </h4>
                    <button type="button" @click="showIconPicker = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-800 text-gray-400 hover:text-white hover:bg-red-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar flex-grow bg-gray-800/50">
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3 sm:gap-4">
                        <template x-for="icon in iconList" :key="icon">
                            <button type="button" @click="selectIcon(icon)"
                                class="aspect-square rounded-2xl flex items-center justify-center transition-all border-2 text-2xl hover:scale-110"
                                :class="(activeIconType === 'service' ? services[activeIconIndex]?.icon : (
                                    activeIconType === 'category' ? categories[activeIconIndex]?.icon : reasons[
                                        activeIconIndex]?.icon)) === icon ?
                                    'bg-purple-600 text-white border-purple-400 shadow-[0_0_15px_rgba(168,85,247,0.5)]' :
                                    'text-gray-300 bg-gray-900 border-gray-700 hover:border-purple-500/50 hover:text-purple-400'">
                                <i :class="icon"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function siteSettings() {
            return {
                activeTab: 'homepage',
                showIconPicker: false,
                activeIconIndex: 0,
                activeIconType: 'service',
                iconList: [
                    'fas fa-star', 'fas fa-heart', 'fas fa-check', 'fas fa-truck', 'fas fa-box', 'fas fa-tag',
                    'fas fa-percent', 'fas fa-gift', 'fas fa-utensils', 'fas fa-coffee', 'fas fa-glass-cheers',
                    'fas fa-fire', 'fas fa-leaf', 'fas fa-seedling', 'fas fa-apple-alt', 'fas fa-carrot',
                    'fas fa-clock', 'fas fa-history', 'fas fa-calendar-alt', 'fas fa-hourglass-half',
                    'fas fa-credit-card', 'fas fa-wallet', 'fas fa-money-bill-wave', 'fas fa-qrcode',
                    'fas fa-shield-alt', 'fas fa-lock', 'fas fa-user-shield', 'fas fa-certificate', 'fas fa-thumbs-up',
                    'fas fa-smile', 'fas fa-award', 'fas fa-medal', 'fas fa-phone', 'fas fa-envelope',
                    'fas fa-comment-dots', 'fas fa-headset', 'fas fa-gem', 'fas fa-crown', 'fas fa-bolt'
                ],

                openIconPicker(type, index) {
                    this.activeIconType = type;
                    this.activeIconIndex = index;
                    this.showIconPicker = true;
                },
                selectIcon(iconClass) {
                    if (this.activeIconType === 'service') this.services[this.activeIconIndex].icon = iconClass;
                    else if (this.activeIconType === 'reason') this.reasons[this.activeIconIndex].icon = iconClass;
                    else if (this.activeIconType === 'category') this.categories[this.activeIconIndex].icon = iconClass;
                    this.showIconPicker = false;
                },

                // Homepage Data
                hero_slides: [
                    @foreach ($heroBanners ?? [] as $banner)
                        {
                            id: "{{ $banner->id }}",
                            image: "{{ Storage::url($banner->image_path) }}",
                            existing_path: "{{ $banner->image_path }}",
                            link_url: "{{ $banner->link_url }}"
                        },
                    @endforeach
                ],
                addHeroSlide() {
                    this.hero_slides.push({
                        id: null,
                        image: 'https://via.placeholder.com/1600x600?text=Upload+Banner',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeHeroSlide(index) {
                    this.hero_slides.splice(index, 1);
                },

                sec_slides: [
                    @foreach ($secondaryBanners ?? [] as $banner)
                        {
                            id: "{{ $banner->id }}",
                            image: "{{ Storage::url($banner->image_path) }}",
                            existing_path: "{{ $banner->image_path }}",
                            link_url: "{{ $banner->link_url }}"
                        },
                    @endforeach
                ],
                addSecSlide() {
                    this.sec_slides.push({
                        id: null,
                        image: 'https://via.placeholder.com/800x320?text=Upload+Image',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeSecSlide(index) {
                    this.sec_slides.splice(index, 1);
                },

                allergy_img: "{{ isset($infoBanner) && $infoBanner->image_path ? Storage::url($infoBanner->image_path) : '' }}",

                services: [
                    @foreach ($services ?? [] as $svc)
                        {
                            icon: "{{ $svc->icon }}",
                            title: "{{ $svc->title }}"
                        },
                    @endforeach
                ],
                addService() {
                    this.services.push({
                        icon: 'fas fa-star',
                        title: ''
                    });
                },
                removeService(index) {
                    this.services.splice(index, 1);
                },

                reasons: [
                    @foreach ($features ?? [] as $feature)
                        {
                            icon: "{{ $feature->icon }}",
                            title: "{{ $feature->title }}",
                            description: "{{ $feature->description }}"
                        },
                    @endforeach
                ],
                addReason() {
                    this.reasons.push({
                        icon: 'fas fa-check',
                        title: '',
                        description: ''
                    });
                },
                removeReason(index) {
                    this.reasons.splice(index, 1);
                },

                review_images: [
                    @foreach ($reviewImages ?? [] as $img)
                        {
                            id: "{{ $img->id }}",
                            image: "{{ Str::startsWith($img->image_url, 'http') ? $img->image_url : asset('storage/' . ltrim($img->image_url, '/')) }}",
                            existing_path: "{{ $img->image_url }}"
                        },
                    @endforeach
                ],
                addReviewImage() {
                    this.review_images.push({
                        id: null,
                        image: 'https://via.placeholder.com/600x600?text=Upload+Review',
                        existing_path: ''
                    });
                },
                removeReviewImage(index) {
                    this.review_images.splice(index, 1);
                },

                // All Products Data
                all_products_hero_slides: [
                    @foreach ($allProductsHeroBanners ?? [] as $banner)
                        {
                            id: "{{ $banner->id }}",
                            image: "{{ Storage::url($banner->image_path) }}",
                            existing_path: "{{ $banner->image_path }}",
                            link_url: "{{ $banner->link_url }}"
                        },
                    @endforeach
                ],
                addAllProdHeroSlide() {
                    this.all_products_hero_slides.push({
                        id: null,
                        image: 'https://via.placeholder.com/1600x600?text=Upload+Banner',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeAllProdHeroSlide(index) {
                    this.all_products_hero_slides.splice(index, 1);
                },

                categories: [
                    @foreach ($categories ?? [] as $cat)
                        {
                            
                            id: @json($cat->id ?? null),
                            name: @json($cat->name ?? ''),
                            slug: @json($cat->slug ?? ''),
                            parent_id: @json($cat->parent_id ?? ''),
                            icon: @json($cat->icon ?? 'fas fa-th'),
                            image: @json(!empty($cat->image_path) ? Storage::url($cat->image_path) : 'https://via.placeholder.com/300x300?text=Category'),
                            existing_path: @json($cat->image_path ?? ''),
                            link_url: @json($cat->link_url ?? ''),
                            linked_product_id: @json($cat->linked_product_id ?? '')
                        },
                    @endforeach
                ],
                addCategory() {
                    this.categories.push({
                        id: null,
                        name: '',
                        slug: '',
                        parent_id: '',
                        icon: 'fas fa-th',
                        image: 'https://via.placeholder.com/300x300?text=Upload+Category',
                        existing_path: '',
                        link_url: '',
                        linked_product_id: ''
                    });
                },
                removeCategory(index) {
                    this.categories.splice(index, 1);
                },

                previewImage(event, targetObj, index = null) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (index !== null && Array.isArray(this[targetObj])) this[targetObj][index].image = e
                                .target.result;
                            else if (index !== null) this[targetObj][index] = e.target.result;
                            else this[targetObj] = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Scrollbar for modern look */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(31, 41, 55, 0.5);
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Hide Scrollbar for Horizontal Scroll (Mobile Tabs) */
        .hide-scroll-bar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scroll-bar::-webkit-scrollbar {
            display: none;
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
