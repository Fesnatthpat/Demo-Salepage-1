@extends('layouts.admin')

@section('title', 'ตั้งค่าหน้าเว็บไซต์ & Live Preview')

@section('content')
    <div class="container mx-auto pb-24" x-data="siteSettings()">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-100"><i class="fas fa-magic mr-2 text-emerald-400"></i> ตกแต่งหน้าเว็บไซต์
            </h1>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    class="px-4 py-2 bg-emerald-500/20 border border-emerald-500 text-emerald-300 rounded-lg flex items-center gap-2 animate-fade-in">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 items-start">

                {{-- ================= LEFT COLUMN: FORM EDITORS ================= --}}
                <div class="space-y-6">

                    {{-- 1. HERO SLIDER (5 Slots) --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700 flex justify-between items-center">
                            <h3 class="font-bold text-lg text-gray-200"><i class="fas fa-images text-red-400 mr-2"></i> Hero
                                Slides (สไลด์หลัก)</h3>
                            <span class="text-xs text-gray-500">แนะนำขนาด 1200x500px</span>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach (range(1, 5) as $i)
                                <div class="p-4 bg-gray-700/30 rounded-lg border border-gray-600">
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Slide ที่
                                        {{ $i }}</label>
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex-shrink-0 w-20 h-12 bg-gray-800 rounded border border-gray-600 overflow-hidden relative">
                                            <img :src="hero_slides[{{ $i }}]" class="w-full h-full object-cover">
                                        </div>
                                        <input type="file" name="hero_slide_{{ $i }}" accept="image/*"
                                            @change="previewImage($event, 'hero_slides', {{ $i }})"
                                            class="block w-full text-sm text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-600 file:text-gray-200 hover:file:bg-gray-500 cursor-pointer">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. ALLERGY INFO --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-200"><i
                                    class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i> ข้อมูลแพ้อาหาร</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col items-center gap-4">
                                <div
                                    class="w-full h-32 bg-red-50/10 rounded-lg border-2 border-dashed border-gray-600 flex items-center justify-center overflow-hidden relative">
                                    <img :src="allergy_img" class="w-full h-full object-contain">
                                </div>
                                <input type="file" name="allergy_image" accept="image/*"
                                    @change="previewImage($event, 'allergy_img')"
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-600 file:text-white hover:file:bg-yellow-700 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    {{-- 3. SECONDARY SLIDER (3 Slots) --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-200"><i
                                    class="fas fa-photo-video text-blue-400 mr-2"></i> สไลด์รอง (3 รูป)</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach (range(1, 3) as $i)
                                <div class="flex items-center gap-4 p-3 bg-gray-700/30 rounded border border-gray-600">
                                    <span class="text-gray-400 font-bold">#{{ $i }}</span>
                                    <input type="file" name="sec_slide_{{ $i }}" accept="image/*"
                                        @change="previewImage($event, 'sec_slides', {{ $i }})"
                                        class="block w-full text-sm text-gray-400 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-900/50 file:text-blue-200 hover:file:bg-blue-900">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 4. SERVICE BAR (4 Items) --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-200"><i
                                    class="fas fa-concierge-bell text-purple-400 mr-2"></i> Service Bar (4 รายการ)</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach (range(1, 4) as $i)
                                <div class="p-4 bg-gray-700/30 rounded border border-gray-600 space-y-2">
                                    <div class="text-xs text-purple-300 font-bold mb-1">Service {{ $i }}</div>
                                    {{-- Icon --}}
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center text-gray-400">
                                            <i :class="services[{{ $i }}].icon"></i>
                                        </div>
                                        <input type="text" name="service_{{ $i }}_icon"
                                            x-model="services[{{ $i }}].icon"
                                            placeholder="Icon class (e.g. fas fa-star)"
                                            class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-200 px-2 py-1">
                                    </div>
                                    {{-- Text --}}
                                    <input type="text" name="service_{{ $i }}_text"
                                        x-model="services[{{ $i }}].text" placeholder="ข้อความบริการ"
                                        class="w-full bg-gray-900 border-gray-700 rounded text-sm text-gray-200 px-2 py-1.5 focus:ring-purple-500 focus:border-purple-500">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. 6 REASONS SECTION --}}
                    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-lg">
                        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700">
                            <h3 class="font-bold text-lg text-gray-200"><i class="fas fa-th text-emerald-400 mr-2"></i> 6
                                Reasons Section</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 gap-4">
                            @foreach (range(1, 6) as $i)
                                <div class="p-4 bg-gray-700/30 rounded border border-gray-600 flex gap-4">
                                    <div class="flex-shrink-0 flex flex-col gap-2 items-center justify-center w-12">
                                        <span class="text-lg font-bold text-emerald-500/50">{{ $i }}</span>
                                    </div>
                                    <div class="flex-grow space-y-2">
                                        <input type="text" name="reason_{{ $i }}_title"
                                            x-model="reasons[{{ $i }}].title"
                                            placeholder="หัวข้อเหตุผลที่ {{ $i }}"
                                            class="w-full bg-gray-900 border-gray-700 rounded text-sm font-bold text-emerald-400 px-3 py-1.5">
                                        <textarea name="reason_{{ $i }}_desc" x-model="reasons[{{ $i }}].desc" rows="2"
                                            placeholder="คำอธิบาย..." class="w-full bg-gray-900 border-gray-700 rounded text-xs text-gray-300 px-3 py-1.5"></textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="sticky bottom-4 z-20">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-4 px-6 rounded-xl shadow-xl transform transition hover:-translate-y-1 flex items-center justify-center gap-3">
                            <i class="fas fa-save text-xl"></i>
                            <span class="text-lg">บันทึกการตั้งค่าทั้งหมด</span>
                        </button>
                    </div>

                </div>

                {{-- ================= RIGHT COLUMN: LIVE PREVIEW (Mockup) ================= --}}
                <div class="hidden xl:block relative">
                    <div class="sticky top-6">
                        <div
                            class="bg-white rounded-[2rem] border-8 border-gray-800 shadow-2xl overflow-hidden h-[850px] relative">
                            {{-- Browser Header --}}
                            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center gap-2">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div
                                    class="flex-1 bg-white rounded-md px-3 py-1 text-xs text-gray-400 text-center shadow-sm">
                                    tidjai-thaisnack.com
                                </div>
                            </div>

                            {{-- Website Content (Mockup) --}}
                            <div class="h-full overflow-y-auto bg-gray-50 pb-20 custom-scrollbar">

                                {{-- Navbar Mockup --}}
                                <div
                                    class="bg-red-600 h-14 flex items-center justify-between px-4 shadow-sm sticky top-0 z-10">
                                    <div class="w-8 h-8 bg-white/20 rounded-full"></div>
                                    <div class="flex gap-2">
                                        <div class="w-16 h-2 bg-white/20 rounded-full"></div>
                                        <div class="w-16 h-2 bg-white/20 rounded-full"></div>
                                    </div>
                                    <div class="w-6 h-6 bg-white/20 rounded-full"></div>
                                </div>

                                {{-- Hero Slider Preview --}}
                                <div class="relative w-full aspect-[2.5/1] bg-gray-200 group overflow-hidden">
                                    <template x-for="i in 5">
                                        <img :src="hero_slides[i]" x-show="currentSlide === i"
                                            class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                            x-transition:enter="opacity-0" x-transition:enter-end="opacity-100">
                                    </template>
                                    {{-- Fake Navigation --}}
                                    <div class="absolute inset-0 flex justify-between items-center px-2">
                                        <button @click="currentSlide = currentSlide > 1 ? currentSlide - 1 : 5"
                                            class="w-6 h-6 bg-black/30 text-white rounded-full flex items-center justify-center text-xs hover:bg-black/50"><i
                                                class="fas fa-chevron-left"></i></button>
                                        <button @click="currentSlide = currentSlide < 5 ? currentSlide + 1 : 1"
                                            class="w-6 h-6 bg-black/30 text-white rounded-full flex items-center justify-center text-xs hover:bg-black/50"><i
                                                class="fas fa-chevron-right"></i></button>
                                    </div>
                                </div>

                                {{-- Allergy Info Preview --}}
                                <div class="w-full bg-red-50 p-2">
                                    <img :src="allergy_img" class="w-full h-auto rounded shadow-sm">
                                </div>

                                {{-- Products Mockup (Static) --}}
                                <div class="p-4">
                                    <div class="h-4 w-32 bg-gray-200 rounded mb-4"></div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="aspect-square bg-white rounded shadow-sm border border-gray-100"></div>
                                        <div class="aspect-square bg-white rounded shadow-sm border border-gray-100"></div>
                                    </div>
                                </div>

                                {{-- Secondary Slider Preview --}}
                                <div class="p-4 bg-white/50">
                                    <div class="flex gap-2 overflow-x-auto pb-2 snap-x">
                                        <template x-for="i in 3">
                                            <div class="flex-shrink-0 w-3/4 snap-center">
                                                <img :src="sec_slides[i]"
                                                    class="w-full h-24 object-cover rounded-lg shadow-sm bg-gray-200">
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Service Bar Preview --}}
                                <div class="bg-white py-4 px-2 border-t border-gray-100">
                                    <div class="grid grid-cols-4 gap-1 text-center divide-x divide-gray-100">
                                        <template x-for="i in 4">
                                            <div class="flex flex-col items-center gap-1">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-[10px]">
                                                    <i :class="services[i].icon"></i>
                                                </div>
                                                <span class="text-[8px] font-bold text-gray-600 truncate w-full"
                                                    x-text="services[i].text"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 6 Reasons Preview --}}
                                <div class="py-6 bg-white px-4">
                                    <h3 class="text-center font-bold text-gray-800 text-sm mb-4">6 เหตุผลทำไมต้องเลือกเรา
                                    </h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        <template x-for="i in 6">
                                            <div class="text-center group">
                                                <div
                                                    class="text-red-600 mb-1 text-xs transform group-hover:scale-110 transition">
                                                    <i class="fas fa-heart"></i>
                                                </div>
                                                <h4 class="text-[10px] font-bold text-red-700" x-text="reasons[i].title">
                                                </h4>
                                                <p class="text-[9px] text-gray-500 leading-tight mt-0.5 line-clamp-2"
                                                    x-text="reasons[i].desc"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Footer Mockup --}}
                                <div class="bg-red-600 p-4 mt-4">
                                    <div class="flex gap-2 mb-2">
                                        <div class="w-10 h-10 bg-white rounded-full"></div>
                                        <div class="flex-1 space-y-1">
                                            <div class="w-20 h-2 bg-white/20 rounded"></div>
                                            <div class="w-32 h-1.5 bg-white/20 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2 mt-4">
                                        <div class="space-y-1">
                                            <div class="w-full h-1 bg-white/20"></div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="w-full h-1 bg-white/20"></div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="w-full h-1 bg-white/20"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Phone Frame Button --}}
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-32 h-1 bg-gray-300 rounded-full">
                            </div>
                        </div>
                        <div class="text-center mt-4 text-gray-500 text-sm">
                            <i class="fas fa-mobile-alt animate-pulse"></i> Live Preview (จำลองหน้าจอ)
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function siteSettings() {
            return {
                // State for Hero Slides
                currentSlide: 1,
                hero_slides: {
                    @foreach (range(1, 5) as $i)
                        {{-- FIX: Check if key exists before accessing --}}
                        {{ $i }}:
                            "{{ !empty($settings['hero_slide_' . $i]) ? Storage::url($settings['hero_slide_' . $i]) : asset('images/th-' . $i . '.png') }}",
                    @endforeach
                },

                // State for Allergy Image
                {{-- FIX: Check if key exists --}}
                allergy_img: "{{ !empty($settings['allergy_image']) ? Storage::url($settings['allergy_image']) : asset('images/image_27e610.png') }}",

                // State for Secondary Slides
                sec_slides: {
                    @foreach (range(1, 3) as $i)
                        {{-- FIX: Check if key exists --}}
                        {{ $i }}:
                            "{{ !empty($settings['sec_slide_' . $i]) ? Storage::url($settings['sec_slide_' . $i]) : 'https://via.placeholder.com/400x200?text=Promo+' . $i }}",
                    @endforeach
                },

                // State for Services (Fixed 4 slots)
                services: {
                    @foreach (range(1, 4) as $i)
                        {{ $i }}: {
                            icon: "{{ $settings['service_' . $i . '_icon'] ?? 'fas fa-star' }}",
                            text: "{{ $settings['service_' . $i . '_text'] ?? 'บริการที่ ' . $i }}"
                        },
                    @endforeach
                },

                // State for Reasons (Fixed 6 slots)
                reasons: {
                    @foreach (range(1, 6) as $i)
                        {{ $i }}: {
                            title: "{{ $settings['reason_' . $i . '_title'] ?? 'เหตุผลที่ ' . $i }}",
                            desc: "{{ $settings['reason_' . $i . '_desc'] ?? 'รายละเอียดสั้นๆ...' }}"
                        },
                    @endforeach
                },

                // Helper to preview uploaded image immediately
                previewImage(event, targetObj, index = null) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (index) {
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
