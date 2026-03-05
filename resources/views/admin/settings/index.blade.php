@extends('layouts.admin')

@section('title', 'ตั้งค่าหน้าเว็บไซต์ & Live Preview')

@section('content')
    {{-- ใช้ max-w-5xl และ mx-auto เพื่อจัดกึ่งกลางหน้าจอ --}}
    <div class="container mx-auto pb-24 max-w-5xl" x-data="siteSettings()">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-gray-100 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center mr-3">
                    <i class="fas fa-magic text-emerald-400"></i>
                </div>
                ตกแต่งหน้าเว็บไซต์
            </h1>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    class="px-6 py-2 bg-emerald-500/20 border border-emerald-500 text-emerald-300 rounded-full flex items-center gap-2 animate-fade-in shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-8">

                {{-- 1. HERO SLIDER (Dynamic) - ปรับรูปให้ยาวและใหญ่ขึ้น --}}
                <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                    <div
                        class="px-6 py-5 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center backdrop-blur-sm">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-red-500/10 rounded-lg">
                                <i class="fas fa-images text-red-400 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-100">Hero Slides (สไลด์หลัก)</h3>
                                <p class="text-xs text-gray-400">ภาพสไลด์ขนาดใหญ่ด้านบนสุดของเว็บ</p>
                            </div>
                        </div>
                        <button type="button" @click="addHeroSlide()"
                            class="btn btn-sm bg-red-600 hover:bg-red-700 text-white border-none shadow-lg shadow-red-600/20 rounded-lg px-4 transition-transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> เพิ่มสไลด์
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        <template x-for="(slide, index) in hero_slides" :key="index">
                            <div
                                class="bg-gray-700/30 rounded-xl border border-gray-600 overflow-hidden group relative hover:border-gray-500 transition-colors">

                                {{-- ปุ่มลบ --}}
                                <button type="button" @click="removeHeroSlide(index)"
                                    class="absolute top-4 right-4 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-lg z-20 hover:bg-red-700 hover:scale-110">
                                    <i class="fas fa-times"></i>
                                </button>

                                <input type="hidden" :name="`hero_banners[${index}][id]`" :value="slide.id">
                                <input type="hidden" :name="`hero_banners[${index}][existing_path]`"
                                    :value="slide.existing_path">

                                <div class="p-4">
                                    {{-- ส่วนแสดงรูปภาพแบบยาว (Wide Aspect Ratio) --}}
                                    <div
                                        class="w-full aspect-[3/1] bg-gray-800 rounded-lg border border-gray-600 overflow-hidden relative mb-4 shadow-inner group-hover:shadow-md transition-shadow">
                                        <img :src="slide.image" class="w-full h-full object-cover">

                                        {{-- Overlay เมื่อ Hover เพื่อให้รู้ว่ากดเปลี่ยนรูปได้ --}}
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                            <span
                                                class="text-white text-sm font-semibold border border-white/50 px-3 py-1 rounded-full bg-black/30 backdrop-blur-sm">
                                                <i class="fas fa-camera mr-2"></i> เปลี่ยนรูปภาพ
                                            </span>
                                        </div>

                                        {{-- Input File ซ่อนอยู่ด้านบน --}}
                                        <input type="file" :name="`hero_banners[${index}][image]`" accept="image/*"
                                            @change="previewImage($event, 'hero_slides', index)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    </div>

                                    {{-- Input URL --}}
                                    <div
                                        class="flex items-center gap-3 bg-gray-900/50 p-2 rounded-lg border border-gray-700 focus-within:border-red-500 transition-colors">
                                        <span class="text-gray-400 pl-2"><i class="fas fa-link"></i></span>
                                        <input type="text" :name="`hero_banners[${index}][link_url]`"
                                            x-model="slide.link_url" placeholder="ลิงก์ปลายทาง (เช่น /promotion-1)"
                                            class="w-full bg-transparent border-none text-sm text-gray-200 focus:ring-0 placeholder-gray-500">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="hero_slides.length === 0"
                            class="text-center py-12 bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-700">
                            <div class="text-gray-600 text-5xl mb-3"><i class="fas fa-images"></i></div>
                            <p class="text-gray-400">ยังไม่มีสไลด์หลัก</p>
                            <button type="button" @click="addHeroSlide()"
                                class="text-red-400 hover:text-red-300 text-sm mt-2 underline">
                                กดเพื่อเพิ่มสไลด์แรก
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Grid 2 คอลัมน์สำหรับส่วนเนื้อหารอง --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

                    {{-- Left Column --}}
                    <div class="space-y-8">
                        {{-- 2. ALLERGY INFO --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i> ข้อมูลแพ้อาหาร
                                </h3>
                                <button type="button" x-show="allergy_img && !allergy_img.includes('placeholder')"
                                    @click="allergy_img = ''; document.getElementById('remove_allergy_image').value = '1'"
                                    class="text-xs text-red-400 hover:text-red-300 underline">
                                    ลบรูปภาพ
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col items-center gap-4">
                                    <input type="hidden" name="remove_allergy_image" id="remove_allergy_image"
                                        value="0">

                                    <div class="relative w-full group cursor-pointer">
                                        <div x-show="allergy_img"
                                            class="w-full h-40 bg-red-50/5 rounded-xl border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden relative">
                                            <img :src="allergy_img" class="w-full h-full object-contain p-2">
                                        </div>
                                        <div x-show="!allergy_img"
                                            class="w-full h-40 bg-gray-900/50 rounded-xl border-2 border-dashed border-gray-700 flex flex-col items-center justify-center text-gray-500 hover:bg-gray-900/80 transition-colors">
                                            <i class="fas fa-image text-3xl mb-2"></i>
                                            <span class="text-xs">คลิกเพื่ออัปโหลดรูปภาพ</span>
                                        </div>
                                        <input type="file" name="allergy_image" accept="image/*"
                                            @change="previewImage($event, 'allergy_img'); document.getElementById('remove_allergy_image').value = '0'"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 4. SERVICE BAR (4 Items) --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2">
                                    <i class="fas fa-concierge-bell text-purple-400"></i> Service Bar
                                </h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 gap-4">
                                @foreach (range(1, 4) as $i)
                                    <div
                                        class="p-3 bg-gray-700/30 rounded-lg border border-gray-600 flex gap-3 items-center">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 bg-gray-800 rounded flex items-center justify-center text-gray-400">
                                            <i :class="services[{{ $i }}].icon"></i>
                                        </div>
                                        <div class="flex-grow space-y-1">
                                            <input type="text" name="service_{{ $i }}_text"
                                                x-model="services[{{ $i }}].text" placeholder="ข้อความบริการ"
                                                class="w-full bg-transparent border-b border-gray-600 text-sm text-gray-200 px-0 py-1 focus:ring-0 focus:border-purple-500 placeholder-gray-600">
                                            <input type="text" name="service_{{ $i }}_icon"
                                                x-model="services[{{ $i }}].icon" placeholder="Icon class"
                                                class="w-full bg-transparent border-none text-[10px] text-gray-500 px-0 py-0 focus:ring-0">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-8">
                        {{-- 3. SECONDARY SLIDER --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div
                                class="px-6 py-4 bg-gray-900/80 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2">
                                    <i class="fas fa-photo-video text-blue-400"></i> สไลด์รอง
                                </h3>
                                <button type="button" @click="addSecSlide()"
                                    class="text-xs bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition-colors">
                                    <i class="fas fa-plus mr-1"></i> เพิ่ม
                                </button>
                            </div>
                            <div class="p-6 space-y-4">
                                <template x-for="(slide, index) in sec_slides" :key="index">
                                    <div class="p-3 bg-gray-700/30 rounded-lg border border-gray-600 relative group">
                                        <button type="button" @click="removeSecSlide(index)"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-lg z-10 text-xs">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <input type="hidden" :name="`secondary_banners[${index}][id]`"
                                            :value="slide.id">
                                        <input type="hidden" :name="`secondary_banners[${index}][existing_path]`"
                                            :value="slide.existing_path">

                                        <div class="space-y-3">
                                            <div
                                                class="w-full aspect-[2.5/1] bg-gray-800 rounded border border-gray-600 overflow-hidden relative">
                                                <img :src="slide.image" class="w-full h-full object-cover">
                                                <input type="file" :name="`secondary_banners[${index}][image]`"
                                                    accept="image/*" @change="previewImage($event, 'sec_slides', index)"
                                                    class="absolute inset-0 opacity-0 cursor-pointer">
                                            </div>
                                            <input type="text" :name="`secondary_banners[${index}][link_url]`"
                                                x-model="slide.link_url" placeholder="Link URL"
                                                class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-200 px-3 py-1.5 focus:ring-blue-500 focus:border-blue-500">
                                        </div>
                                    </div>
                                </template>
                                <div x-show="sec_slides.length === 0" class="text-center py-4 text-gray-500 text-xs">
                                    ยังไม่มีแบนเนอร์
                                </div>
                            </div>
                        </div>

                        {{-- 5. 6 REASONS SECTION --}}
                        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                            <div class="px-6 py-4 bg-gray-900/80 border-b border-gray-700">
                                <h3 class="font-bold text-lg text-gray-200 flex items-center gap-2">
                                    <i class="fas fa-th text-emerald-400"></i> 6 Reasons
                                </h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 gap-3">
                                @foreach (range(1, 6) as $i)
                                    <div class="p-3 bg-gray-700/30 rounded border border-gray-600 flex gap-3">
                                        <div class="flex-shrink-0 flex flex-col items-center justify-center w-8 pt-1">
                                            <span class="text-sm font-bold text-emerald-500/50">{{ $i }}</span>
                                        </div>
                                        <div class="flex-grow space-y-2">
                                            <input type="text" name="reason_{{ $i }}_title"
                                                x-model="reasons[{{ $i }}].title" placeholder="หัวข้อ"
                                                class="w-full bg-transparent border-b border-gray-600 text-sm font-bold text-emerald-400 px-0 py-1 focus:ring-0 focus:border-emerald-500">
                                            <textarea name="reason_{{ $i }}_desc" x-model="reasons[{{ $i }}].desc" rows="2"
                                                placeholder="คำอธิบาย..." class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-300 px-2 py-1"></textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="sticky bottom-6 z-30 pt-4 flex justify-center">
                    <button type="submit"
                        class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3 px-10 rounded-full shadow-xl shadow-emerald-900/50 transform transition-all hover:-translate-y-1 hover:scale-105 flex items-center gap-3 border-2 border-emerald-400/30">
                        <i class="fas fa-save text-xl"></i>
                        <span class="text-lg">บันทึกการตั้งค่าทั้งหมด</span>
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function siteSettings() {
            return {
                // State for Hero Slides (Dynamic)
                currentSlide: 1,
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
                        image: 'https://via.placeholder.com/1200x400?text=New+Slide+Image',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeHeroSlide(index) {
                    this.hero_slides.splice(index, 1);
                },

                // State for Allergy Image
                @php
                    $infoPath = isset($infoBanner) ? $infoBanner->image_path : $settings['allergy_image'] ?? null;
                @endphp
                allergy_img: "{{ $infoPath ? Storage::url($infoPath) : asset('images/image_27e610.png') }}",

                // State for Secondary Slides (Dynamic)
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
                        image: 'https://via.placeholder.com/800x320?text=New+Promo',
                        existing_path: '',
                        link_url: ''
                    });
                },
                removeSecSlide(index) {
                    this.sec_slides.splice(index, 1);
                },

                // State for Services (Fixed 4 slots)
                services: {
                    @php $serviceList = $services->keyBy('sort_order'); @endphp
                    @foreach (range(1, 4) as $i)
                        @php $svc = $serviceList[$i] ?? null; @endphp
                        {{ $i }}: {
                                icon: "{{ $svc ? $svc->icon : $settings['service_' . $i . '_icon'] ?? 'fas fa-star' }}",
                                text: "{{ $svc ? $svc->title : $settings['service_' . $i . '_text'] ?? 'บริการที่ ' . $i }}"
                            },
                    @endforeach
                },

                // State for Reasons (Fixed 6 slots)
                reasons: {
                    @php $featureList = $features->keyBy('sort_order'); @endphp
                    @foreach (range(1, 6) as $i)
                        @php $feature = $featureList[$i] ?? null; @endphp
                        {{ $i }}: {
                                title: "{{ $feature ? $feature->title : $settings['reason_' . $i . '_title'] ?? 'เหตุผลที่ ' . $i }}",
                                desc: "{{ $feature ? $feature->description : $settings['reason_' . $i . '_desc'] ?? 'รายละเอียดสั้นๆ...' }}"
                            },
                    @endforeach
                },

                // Helper to preview uploaded image immediately
                previewImage(event, targetObj, index = null) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (index !== null && Array.isArray(this[targetObj])) {
                                this[targetObj][index].image = e.target.result;
                            } else if (index !== null) {
                                this[targetObj][index] = e.target.result;
                            } else {
                                this[targetObj] = e.target.result;
                            }
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
