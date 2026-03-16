@extends('layouts.admin')

@section('title', 'ตั้งค่าหน้าเว็บไซต์ & Live Preview')

@section('content')
    <div class="container mx-auto pb-24 max-w-5xl" x-data="siteSettings()">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center mr-3">
                    <i class="fas fa-magic text-emerald-400"></i>
                </div>
                ตกแต่งหน้าเว็บไซต์
            </h1>

            @if (session('success'))
                <div
                    class="px-6 py-2 bg-emerald-500/20 border border-emerald-500 text-emerald-300 rounded-full flex items-center gap-2 animate-fade-in shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="px-6 py-2 bg-red-500/20 border border-red-500 text-red-300 rounded-xl flex flex-col gap-1 animate-fade-in shadow-lg shadow-red-500/10">
                    <div class="flex items-center gap-2 font-bold">
                        <i class="fas fa-exclamation-circle"></i> พบข้อผิดพลาด:
                    </div>
                    <ul class="text-xs list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Tab Navigation --}}
        <div
            class="flex items-center gap-2 mb-8 bg-gray-800/50 p-1.5 rounded-2xl border border-gray-700 w-fit mx-auto md:mx-0 shadow-lg backdrop-blur-md">
            <button type="button" @click="activeTab = 'homepage'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 text-sm md:text-base"
                :class="activeTab === 'homepage' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/50' :
                    'text-gray-400 hover:text-emerald-400 hover:bg-emerald-500/10'">
                <i class="fas fa-home"></i>
                หน้าหลัก (Homepage)
            </button>
            <button type="button" @click="activeTab = 'all_products'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 text-sm md:text-base"
                :class="activeTab === 'all_products' ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/50' :
                    'text-gray-400 hover:text-purple-400 hover:bg-purple-500/10'">
                <i class="fas fa-store"></i>
                หน้าสินค้าทั้งหมด (All Products)
            </button>
            <button type="button" @click="activeTab = 'site_settings'"
                class="px-6 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 text-sm md:text-base"
                :class="activeTab === 'site_settings' ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' :
                    'text-gray-400 hover:text-blue-400 hover:bg-blue-500/10'">
                <i class="fas fa-globe"></i>
                ตั้งค่าร้านค้า (Site Settings)
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 🔵 TAB: SITE SETTINGS --}}
            <div class="space-y-8" x-show="activeTab === 'site_settings'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- 1. Logo & Background --}}
                    <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg"><i class="fas fa-image text-blue-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">รูปภาพเว็บไซต์ (Logo & Background)</h3>
                        </div>
                        <div class="p-6 space-y-8">
                            {{-- Site Logo --}}
                            <div class="space-y-3">
                                <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider">โลโก้เว็บไซต์
                                    (Site Logo)</label>
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-24 h-24 bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative group shrink-0">
                                        @php
                                            $currentLogo = \App\Models\SiteSetting::get('site_logo');
                                            $logoUrl = $currentLogo
                                                ? asset('storage/' . $currentLogo)
                                                : asset('images/logo/logo1.png');
                                        @endphp
                                        <img src="{{ $logoUrl }}" class="w-full h-full object-contain p-2"
                                            id="logo-preview-site">
                                        <div
                                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                            <i class="fas fa-camera text-white text-xl"></i>
                                        </div>
                                        <input type="file" name="site_logo" accept="image/*"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            onchange="document.getElementById('logo-preview-site').src = window.URL.createObjectURL(this.files[0])">
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-500">แนะนำเป็นไฟล์ PNG พื้นหลังโปร่งใส</p>
                                        <p class="text-xs text-gray-500">ขนาดที่แนะนำ: 512x512 px</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Site Cover Image (Background) --}}
                            <div class="space-y-3">
                                <label
                                    class="block text-sm font-bold text-gray-400 uppercase tracking-wider">ภาพพื้นหลังเว็บไซต์
                                    (Site Background Image)</label>
                                <div
                                    class="relative w-full aspect-[2/1] bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden group">
                                    @php
                                        $currentCover = \App\Models\SiteSetting::get('site_cover_image');
                                        $coverUrl = $currentCover
                                            ? asset('storage/' . $currentCover)
                                            : asset('images/BG/fruit2.png');
                                    @endphp
                                    <img src="{{ $coverUrl }}" class="w-full h-full object-cover" id="cover-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-3xl"></i>
                                    </div>
                                    <input type="file" name="site_cover_image" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="document.getElementById('cover-preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">ภาพพื้นหลังที่จะแสดงทั่วทั้งเว็บไซต์ แนะนำขนาด:
                                    1920x1080 px</p>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Contact Information --}}
                    <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                        <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 rounded-lg"><i
                                    class="fas fa-address-book text-blue-400 text-xl"></i></div>
                            <h3 class="font-bold text-lg text-gray-100">ข้อมูลติดต่อ (Contact Information)</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase mb-1">ชื่อบริษัท/ชื่อร้าน</label>
                                <input type="text" name="settings[site_name]"
                                    value="{{ \App\Models\SiteSetting::get('site_name') }}"
                                    class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-200 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">เบอร์โทรศัพท์</label>
                                <input type="text" name="settings[site_phone]"
                                    value="{{ \App\Models\SiteSetting::get('site_phone') }}"
                                    class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-200 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">อีเมล</label>
                                <input type="email" name="settings[site_email]"
                                    value="{{ \App\Models\SiteSetting::get('site_email') }}"
                                    class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-200 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">ที่อยู่</label>
                                <textarea name="settings[site_address]" rows="3"
                                    class="w-full bg-gray-900 border-gray-700 rounded-xl text-gray-200 focus:ring-blue-500">{{ \App\Models\SiteSetting::get('site_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Social Media Links --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2 bg-blue-500/10 rounded-lg"><i class="fas fa-share-alt text-blue-400 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">โซเชียลมีเดีย (Social Media)</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center shrink-0"><i
                                        class="fab fa-facebook-f text-white"></i></div>
                                <div class="flex-grow">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Facebook
                                        URL</label>
                                    <input type="text" name="settings[social_facebook]"
                                        value="{{ \App\Models\SiteSetting::get('social_facebook') }}"
                                        placeholder="https://facebook.com/yourpage"
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200">
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-sky-500 flex items-center justify-center shrink-0"><i
                                        class="fab fa-twitter text-white"></i></div>
                                <div class="flex-grow">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Twitter
                                        URL</label>
                                    <input type="text" name="settings[social_twitter]"
                                        value="{{ \App\Models\SiteSetting::get('social_twitter') }}"
                                        placeholder="https://twitter.com/yourhandle"
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-pink-600 flex items-center justify-center shrink-0"><i
                                        class="fab fa-instagram text-white"></i></div>
                                <div class="flex-grow">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Instagram
                                        URL</label>
                                    <input type="text" name="settings[social_instagram]"
                                        value="{{ \App\Models\SiteSetting::get('social_instagram') }}"
                                        placeholder="https://instagram.com/yourprofile"
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200">
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center shrink-0">
                                    <i class="fab fa-line text-white"></i>
                                </div>
                                <div class="flex-grow">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-0.5">Line OA
                                        URL</label>
                                    <input type="text" name="settings[social_line]"
                                        value="{{ \App\Models\SiteSetting::get('social_line') }}"
                                        placeholder="https://line.me/ti/p/@yourid"
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200">
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

                {{-- 0. GENERAL SETTINGS (Logo) --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 rounded-lg"><i class="fas fa-cog text-emerald-400 text-xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-100">ตั้งค่าทั่วไป (General Settings)</h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Site Logo --}}
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider">โลโก้เว็บไซต์
                                (Site Logo)</label>
                            <div class="flex items-center gap-6">
                                <div
                                    class="w-24 h-24 bg-gray-900 rounded-2xl border-2 border-dashed border-gray-700 overflow-hidden relative group shrink-0">
                                    @php
                                        $currentLogo = \App\Models\SiteSetting::get('site_logo');
                                        $logoUrl = $currentLogo
                                            ? asset('storage/' . $currentLogo)
                                            : asset('images/logo/logo1.png');
                                    @endphp
                                    <img src="{{ $logoUrl }}" class="w-full h-full object-contain p-2"
                                        id="logo-preview">
                                    <div
                                        class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                        <i class="fas fa-camera text-white text-xl"></i>
                                    </div>
                                    <input type="file" name="site_logo" accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        onchange="document.getElementById('logo-preview').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-500">แนะนำเป็นไฟล์ PNG พื้นหลังโปร่งใส</p>
                                    <p class="text-xs text-gray-500">ขนาดที่แนะนำ: 512x512 px</p>
                                </div>
                            </div>
                        </div>

                        {{-- Site Description (SEO) --}}
                        <div class="space-y-3">
                            <label class="block text-sm font-bold text-gray-400 uppercase tracking-wider">คำอธิบายเว็บไซต์
                                (Site Description)</label>
                            <textarea name="settings[site_description]" rows="3"
                                class="w-full h-24 bg-gray-900 border-gray-700 rounded-xl text-sm text-gray-200 px-4 py-2 focus:ring-emerald-500">{{ \App\Models\SiteSetting::get('site_description') }}</textarea>
                        </div>
                    </div>
                </div>


                {{-- 1. HERO SLIDER (Homepage) --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-500/10 rounded-lg"><i class="fas fa-images text-red-400 text-xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-100">Hero Slides (หน้าหลัก)</h3>
                        </div>
                        <button type="button" @click="addHeroSlide()"
                            class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-none rounded-lg px-4">
                            <i class="fas fa-plus mr-2"></i> เพิ่มสไลด์
                        </button>
                    </div>
                    <div class="p-6 space-y-6">

                        {{-- 💡 เพิ่มกล่องคำอธิบายขนาดรูปภาพตรงนี้ --}}
                        <div
                            class="bg-blue-900/30 border border-blue-700/50 rounded-xl p-4 flex gap-3 text-sm text-blue-300">
                            <i class="fas fa-info-circle mt-0.5 text-blue-400 text-lg"></i>
                            <div>
                                <strong class="text-blue-200">คำแนะนำขนาดรูปภาพ:</strong>
                                เพื่อความสวยงามและแสดงผลได้ดีในทุกอุปกรณ์ แนะนำให้ใช้รูปภาพขนาด <strong>1600 x 600
                                    พิกเซล</strong>
                                <p class="text-xs text-blue-400/80 mt-1">
                                    * รูปภาพจะถูกปรับสัดส่วนอัตโนมัติตามขนาดหน้าจอ (มือถือ 16:10, แท็บเล็ต 2.5:1,
                                    คอมพิวเตอร์ 3:1)
                                </p>
                            </div>
                        </div>
                        {{-- จบส่วนคำอธิบาย --}}

                        <template x-for="(slide, index) in hero_slides" :key="index">
                            <div class="bg-gray-700/30 rounded-xl border border-gray-600 overflow-hidden group relative">
                                <button type="button" @click="removeHeroSlide(index)"
                                    class="absolute top-4 right-4 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all z-20"><i
                                        class="fas fa-times"></i></button>
                                <input type="hidden" :name="`hero_banners[${index}][id]`" :value="slide.id">
                                <input type="hidden" :name="`hero_banners[${index}][existing_path]`"
                                    :value="slide.existing_path">
                                <div class="p-4">
                                    <div
                                        class="w-full aspect-[3/1] bg-gray-800 rounded-lg border border-gray-600 overflow-hidden relative mb-4">
                                        <img :src="slide.image" class="w-full h-full object-cover">
                                        <input type="file" :name="`hero_banners[${index}][image]`" accept="image/*"
                                            @change="previewImage($event, 'hero_slides', index)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    </div>
                                    <input type="text" :name="`hero_banners[${index}][link_url]`"
                                        x-model="slide.link_url" placeholder="ลิงก์ปลายทาง..."
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200 px-4 py-2">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-8">
                        {{-- Allergy Banner --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2"><i
                                        class="fas fa-exclamation-triangle text-yellow-400"></i> ข้อมูลแพ้อาหาร</h3>
                                <button type="button" x-show="allergy_img"
                                    @click="allergy_img = ''; document.getElementById('remove_allergy_image').value = '1'"
                                    class="text-xs text-red-400 underline">ลบรูป</button>
                            </div>
                            <div class="p-6">
                                <div
                                    class="relative w-full aspect-[4/1] bg-gray-900 rounded-xl border border-gray-700 overflow-hidden group">
                                    <img :src="allergy_img || 'https://via.placeholder.com/800x200?text=No+Image'"
                                        class="w-full h-full object-cover">
                                    <input type="file" name="allergy_image" accept="image/*"
                                        @change="previewImage($event, 'allergy_img'); document.getElementById('remove_allergy_image').value = '0'"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <input type="hidden" name="remove_allergy_image" id="remove_allergy_image"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        {{-- Service Bar --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl relative">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2"><i
                                        class="fas fa-concierge-bell text-purple-400"></i> Service Bar</h3>
                                <button type="button" @click="addService()"
                                    class="text-xs bg-purple-600/20 text-purple-400 px-2 py-1 rounded">เพิ่ม</button>
                            </div>
                            <div class="p-6 grid grid-cols-1 gap-4">
                                <template x-for="(svc, index) in services" :key="index">
                                    <div
                                        class="p-3 bg-gray-700/30 rounded-lg border border-gray-600 flex gap-3 items-center relative group">
                                        <button type="button" @click="removeService(index)"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 text-xs"><i
                                                class="fas fa-times"></i></button>
                                        <div class="flex-shrink-0 w-10 h-10 bg-gray-800 rounded flex items-center justify-center text-gray-400 border border-gray-600 cursor-pointer"
                                            @click="openIconPicker('service', index)">
                                            <i :class="svc.icon"></i>
                                        </div>
                                        <input type="text" :name="`services[${index}][title]`" x-model="svc.title"
                                            placeholder="ข้อความ..."
                                            class="flex-grow bg-transparent border-b border-gray-600 text-sm text-gray-200 focus:ring-0">
                                        <input type="hidden" :name="`services[${index}][icon]`" x-model="svc.icon">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        {{-- Secondary Sliders --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2"><i
                                        class="fas fa-tags text-blue-400"></i> สไลด์รอง</h3>
                                <button type="button" @click="addSecSlide()"
                                    class="text-xs bg-blue-600/20 text-blue-400 px-2 py-1 rounded">เพิ่ม</button>
                            </div>
                            <div class="p-6 space-y-4">

                                {{-- 💡 เพิ่มกล่องคำอธิบายขนาดรูปภาพตรงนี้ --}}
                                <div
                                    class="bg-blue-900/30 border border-blue-700/50 rounded-xl p-4 flex gap-3 text-sm text-blue-300 mb-4">
                                    <i class="fas fa-info-circle mt-0.5 text-blue-400 text-lg"></i>
                                    <div>
                                        <strong class="text-blue-200">คำแนะนำขนาดรูปภาพ:</strong>
                                        สำหรับสไลด์รอง แนะนำให้ใช้รูปภาพขนาด <strong>800 x 320 พิกเซล</strong>
                                        <p class="text-xs text-blue-400/80 mt-1">
                                            * รูปภาพจะแสดงผลในสัดส่วน 2.5:1 (แนวนอนยาว)
                                        </p>
                                    </div>
                                </div>
                                {{-- จบส่วนคำอธิบาย --}}

                                <template x-for="(slide, index) in sec_slides" :key="index">
                                    <div class="p-3 bg-gray-700/30 rounded-lg border border-gray-600 relative group">
                                        <button type="button" @click="removeSecSlide(index)"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 text-xs z-20"><i
                                                class="fas fa-times"></i></button>
                                        <input type="hidden" :name="`secondary_banners[${index}][id]`"
                                            :value="slide.id">
                                        <input type="hidden" :name="`secondary_banners[${index}][existing_path]`"
                                            :value="slide.existing_path">
                                        <div
                                            class="w-full aspect-[2.5/1] bg-gray-800 rounded overflow-hidden relative mb-2">
                                            <img :src="slide.image" class="w-full h-full object-cover">
                                            <input type="file" :name="`secondary_banners[${index}][image]`"
                                                accept="image/*" @change="previewImage($event, 'sec_slides', index)"
                                                class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                        </div>
                                        <input type="text" :name="`secondary_banners[${index}][link_url]`"
                                            x-model="slide.link_url" placeholder="ลิงก์..."
                                            class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-200 px-3 py-1">
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- 6 Reasons --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl relative">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2"><i
                                        class="fas fa-th text-emerald-400"></i> 6 Reasons</h3>
                                <button type="button" @click="addReason()"
                                    class="text-xs bg-emerald-600/20 text-emerald-400 px-2 py-1 rounded">เพิ่ม</button>
                            </div>
                            <div class="p-6 grid grid-cols-1 gap-3">
                                <template x-for="(reason, index) in reasons" :key="index">
                                    <div
                                        class="p-3 bg-gray-700/30 rounded border border-gray-600 flex gap-3 items-center relative group">
                                        <button type="button" @click="removeReason(index)"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 text-xs"><i
                                                class="fas fa-times"></i></button>
                                        <div class="flex-shrink-0 w-10 h-10 bg-gray-800 rounded flex items-center justify-center text-emerald-400 border border-gray-600 cursor-pointer"
                                            @click="openIconPicker('reason', index)">
                                            <i :class="reason.icon"></i>
                                        </div>
                                        <div class="flex-grow space-y-1">
                                            <input type="text" :name="`reasons[${index}][title]`"
                                                x-model="reason.title" placeholder="หัวข้อ"
                                                class="w-full bg-transparent border-b border-gray-600 text-sm font-bold text-emerald-400 px-0 py-1 focus:ring-0">
                                            <input type="hidden" :name="`reasons[${index}][icon]`"
                                                x-model="reason.icon">
                                            <textarea :name="`reasons[${index}][description]`" x-model="reason.description" rows="1"
                                                placeholder="คำอธิบาย..." class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-300 px-2 py-1"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Customer Review Images --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl relative">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2"><i
                                        class="fas fa-comment-dots text-amber-400"></i> รีวิวจากลูกค้า (Review Images)</h3>
                                <button type="button" @click="addReviewImage()"
                                    class="text-xs bg-amber-600/20 text-amber-400 px-2 py-1 rounded">เพิ่มรูปรีวิว</button>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <template x-for="(review, index) in review_images" :key="index">
                                        <div
                                            class="relative group aspect-square bg-gray-900 rounded-xl border border-gray-700 overflow-hidden">
                                            <button type="button" @click="removeReviewImage(index)"
                                                class="absolute top-2 right-2 bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 text-[10px] z-20"><i
                                                    class="fas fa-times"></i></button>
                                            <input type="hidden" :name="`review_images[${index}][id]`"
                                                :value="review.id">
                                            <input type="hidden" :name="`review_images[${index}][existing_path]`"
                                                :value="review.existing_path">

                                            <img :src="review.image" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                                <i class="fas fa-camera text-white text-xl"></i>
                                            </div>
                                            <input type="file" :name="`review_images[${index}][image]`"
                                                accept="image/*" @change="previewImage($event, 'review_images', index)"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                            <div
                                                class="absolute bottom-2 left-2 px-2 py-0.5 bg-black/60 rounded text-[10px] text-gray-400">
                                                ลำดับ: <span x-text="index+1"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div x-show="review_images.length === 0"
                                    class="text-center py-8 text-gray-500 text-sm italic">
                                    ยังไม่มีรูปภาพรีวิว
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🟣 TAB: ALL PRODUCTS --}}
            <div class="space-y-8" x-show="activeTab === 'all_products'"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">

                {{-- 1. HERO SLIDER (All Products) --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-purple-500/10 rounded-lg"><i
                                    class="fas fa-layer-group text-purple-400 text-xl"></i></div>
                            <h3 class="font-bold text-lg text-gray-100">All Products Banners (หน้าสินค้าทั้งหมด)</h3>
                        </div>
                        <button type="button" @click="addAllProdHeroSlide()"
                            class="btn btn-sm bg-purple-600 hover:bg-purple-700 text-white border-none rounded-lg px-4">
                            <i class="fas fa-plus mr-2"></i> เพิ่มสไลด์
                        </button>
                    </div>
                    <div class="p-6 space-y-6">
                        <template x-for="(slide, index) in all_products_hero_slides" :key="index">
                            <div class="bg-gray-700/30 rounded-xl border border-gray-600 overflow-hidden group relative">
                                <button type="button" @click="removeAllProdHeroSlide(index)"
                                    class="absolute top-4 right-4 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all z-20"><i
                                        class="fas fa-times"></i></button>
                                <input type="hidden" :name="`all_products_hero_banners[${index}][id]`"
                                    :value="slide.id">
                                <input type="hidden" :name="`all_products_hero_banners[${index}][existing_path]`"
                                    :value="slide.existing_path">
                                <div class="p-4">
                                    <div
                                        class="w-full aspect-[3/1] bg-gray-800 rounded-lg border border-gray-600 overflow-hidden relative mb-4">
                                        <img :src="slide.image" class="w-full h-full object-cover">
                                        <input type="file" :name="`all_products_hero_banners[${index}][image]`"
                                            accept="image/*"
                                            @change="previewImage($event, 'all_products_hero_slides', index)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    </div>
                                    <input type="text" :name="`all_products_hero_banners[${index}][link_url]`"
                                        x-model="slide.link_url" placeholder="ลิงก์ปลายทาง..."
                                        class="w-full bg-gray-900 border-gray-700 rounded-lg text-sm text-gray-200 px-4 py-2">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 2. CATEGORY MENU --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-500/10 rounded-lg"><i
                                    class="fas fa-th-large text-emerald-400 text-xl"></i></div>
                            <h3 class="font-bold text-lg text-gray-100">Category Menu (หมวดหมู่สินค้า)</h3>
                        </div>
                        <button type="button" @click="addCategory()"
                            class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white border-none rounded-lg px-4">
                            <i class="fas fa-plus mr-2"></i> เพิ่มหมวดหมู่
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <template x-for="(cat, index) in categories" :key="index">
                                <div class="bg-gray-700/30 rounded-2xl border border-gray-600 p-4 relative group">
                                    <button type="button" @click="removeCategory(index)"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 text-[10px]"><i
                                            class="fas fa-times"></i></button>
                                    <input type="hidden" :name="`categories[${index}][id]`" :value="cat.id">
                                    <input type="hidden" :name="`categories[${index}][existing_path]`"
                                        :value="cat.existing_path">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-20 h-20 bg-gray-800 rounded-xl border border-gray-600 overflow-hidden relative group/img">
                                                <img :src="cat.image" class="w-full h-full object-cover">
                                                <input type="file" :name="`categories[${index}][image]`"
                                                    accept="image/*" @change="previewImage($event, 'categories', index)"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            </div>
                                            <div class="mt-2 flex justify-center">
                                                <button type="button" @click="openIconPicker('category', index)"
                                                    class="w-8 h-8 rounded-lg bg-gray-800 border border-gray-600 flex items-center justify-center text-gray-400 hover:text-emerald-400">
                                                    <i :class="cat.icon"></i>
                                                </button>
                                                <input type="hidden" :name="`categories[${index}][icon]`"
                                                    x-model="cat.icon">
                                            </div>
                                        </div>
                                        <div class="flex-grow space-y-3">
                                            <input type="text" :name="`categories[${index}][name]`" x-model="cat.name"
                                                placeholder="ชื่อหมวดหมู่..."
                                                class="w-full bg-gray-900 border-gray-600 rounded-xl text-sm text-gray-200 px-4 py-2">
                                            <div class="text-[10px] text-gray-500"><i class="fas fa-sort mr-1"></i> ลำดับ:
                                                <span x-text="index+1"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 💾 SAVE BUTTON --}}
            <div class="sticky bottom-6 z-30 pt-8 flex justify-center">
                <button type="submit"
                    class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3 px-10 rounded-full shadow-xl shadow-emerald-900/50 transform transition-all hover:-translate-y-1 hover:scale-105 flex items-center gap-3 border-2 border-emerald-400/30">
                    <i class="fas fa-save text-xl"></i>
                    <span class="text-lg">บันทึกการตั้งค่าทั้งหมด</span>
                </button>
            </div>
        </form>

        {{-- 🎨 ICON PICKER MODAL --}}
        <div x-show="showIconPicker"
            class="fixed inset-0 bg-gray-900/90 backdrop-blur-md z-[100] flex items-center justify-center p-4"
            style="display: none;" @click.self="showIconPicker = false">
            <div class="bg-gray-800 border border-gray-700 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="flex justify-between items-center px-6 py-4 bg-gray-900/80 border-b border-gray-700">
                    <h4 class="text-gray-200 font-bold capitalize flex items-center gap-2"><i
                            class="fas fa-icons text-purple-400"></i> เลือกไอคอน (<span x-text="activeIconType"></span>)
                    </h4>
                    <button type="button" @click="showIconPicker = false"
                        class="text-gray-400 hover:text-white transition-colors"><i
                            class="fas fa-times text-xl"></i></button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-6 gap-3 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                        <template x-for="icon in iconList" :key="icon">
                            <button type="button" @click="selectIcon(icon)"
                                class="aspect-square rounded-xl flex items-center justify-center transition-all border-2"
                                :class="(activeIconType === 'service' ? services[activeIconIndex]?.icon : (
                                    activeIconType === 'category' ? categories[activeIconIndex]?.icon : reasons[
                                        activeIconIndex]?.icon)) === icon ?
                                    'bg-purple-600 text-white border-purple-400 shadow-lg' :
                                    'text-gray-400 bg-gray-900/50 border-gray-700 hover:border-purple-500/50'">
                                <i :class="icon" class="text-2xl"></i>
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
                iconList: ['fas fa-star', 'fas fa-heart', 'fas fa-check', 'fas fa-truck', 'fas fa-box', 'fas fa-tag',
                    'fas fa-percent', 'fas fa-gift', 'fas fa-utensils', 'fas fa-coffee', 'fas fa-glass-cheers',
                    'fas fa-fire', 'fas fa-leaf', 'fas fa-seedling', 'fas fa-apple-alt', 'fas fa-carrot',
                    'fas fa-clock', 'fas fa-history', 'fas fa-calendar-alt', 'fas fa-hourglass-half',
                    'fas fa-credit-card', 'fas fa-wallet', 'fas fa-money-bill-wave', 'fas fa-qrcode',
                    'fas fa-shield-alt', 'fas fa-lock', 'fas fa-user-shield', 'fas fa-certificate', 'fas fa-thumbs-up',
                    'fas fa-smile', 'fas fa-award', 'fas fa-medal', 'fas fa-phone', 'fas fa-envelope',
                    'fas fa-comment-dots', 'fas fa-headset'
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
                    @foreach ($heroBanners as $banner)
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
                        image: 'https://via.placeholder.com/1200x400',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeHeroSlide(index) {
                    this.hero_slides.splice(index, 1);
                },

                sec_slides: [
                    @foreach ($secondaryBanners as $banner)
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
                        image: 'https://via.placeholder.com/800x320',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeSecSlide(index) {
                    this.sec_slides.splice(index, 1);
                },

                allergy_img: "{{ isset($infoBanner) && $infoBanner->image_path ? Storage::url($infoBanner->image_path) : '' }}",

                services: [
                    @foreach ($services as $svc)
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
                    @foreach ($features as $feature)
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
                    @foreach ($reviewImages as $img)
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
                        image: 'https://via.placeholder.com/600x600?text=Review',
                        existing_path: ''
                    });
                },
                removeReviewImage(index) {
                    this.review_images.splice(index, 1);
                },

                // All Products Data
                all_products_hero_slides: [
                    @foreach ($allProductsHeroBanners as $banner)
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
                        image: 'https://via.placeholder.com/1200x400',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeAllProdHeroSlide(index) {
                    this.all_products_hero_slides.splice(index, 1);
                },

                categories: [
                    @foreach ($categories as $cat)
                        {
                            id: "{{ $cat->id }}",
                            name: "{{ $cat->name }}",
                            icon: "{{ $cat->icon }}",
                            image: "{{ $cat->image_path ? Storage::url($cat->image_path) : 'https://via.placeholder.com/150' }}",
                            existing_path: "{{ $cat->image_path }}"
                        },
                    @endforeach
                ],
                addCategory() {
                    this.categories.push({
                        id: null,
                        name: '',
                        icon: 'fas fa-th',
                        image: 'https://via.placeholder.com/150',
                        existing_path: ''
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
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
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
