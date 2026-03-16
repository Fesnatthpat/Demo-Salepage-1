@extends('layout')

@section('title', 'เกี่ยวกับเรา | Salepage Demo')

@section('content')
    {{-- Import AOS Animation CSS & FontAwesome --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @php
        $aboutTitle = $settings['about_title'] ?? 'เกี่ยวกับติดใจ';
        $aboutSub = $settings['about_subtitle'] ?? 'ความตั้งใจของเรา...เพื่อส่งมอบความสุขให้คุณ';
        
        $lifeTitle = $settings['life_title'] ?? '🧋 Life with ติดใจ 💖';
        $lifeSub = $settings['life_subtitle'] ?? 'รับชมภาพกิจกรรมต่างๆ ของพนักงานติดใจ ที่ไม่เหมือนใครแน่นอน🤩';
        
        $teamTitle = $settings['team_title'] ?? '💗 Team People';
        $teamSub = $settings['team_subtitle'] ?? 'หากต้องการสอบถามรายละเอียดเพิ่มเติมเกี่ยวกับเรา';
        $teamPhone = $settings['team_phone'] ?? '09X-XXX-XXXX / 09X-XXX-XXXX (Team People)';
        $teamEmail = $settings['team_email'] ?? 'hr@tidjai.com';
        
        $socialTitle = $settings['social_title'] ?? '🫶 สามารถรับชมเพิ่มเติมได้ตามช่องทางด้านล่างนี้ 🫶';
        $socialFB = $settings['social_facebook'] ?? '#';
        $socialIG = $settings['social_instagram'] ?? '#';
        $socialLI = $settings['social_linkedin'] ?? '#';
        $socialTT = $settings['social_tiktok'] ?? '#';
    @endphp

    <div class="bg-gray-50 min-h-screen overflow-x-hidden font-sans pb-20">

        {{-- ★★★ HERO SECTION ★★★ --}}
        <div class="relative bg-gradient-to-br from-red-700 via-red-600 to-red-500 text-white pt-20 pb-32 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
                <div class="absolute -top-20 -left-20 w-72 h-72 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
            </div>
            <div class="container mx-auto px-4 relative z-10 text-center" data-aos="zoom-in" data-aos-duration="1000">
                <div class="mb-8 flex justify-center">
                    <div class="p-6 bg-white/10 backdrop-blur-md rounded-full shadow-2xl ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500">
                        @php
                            $siteLogo = $settings['site_logo'] ?? null;
                            $logoUrl = $siteLogo ? asset('storage/' . $siteLogo) : asset('images/logo/logo1.png');
                        @endphp
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-24 w-auto drop-shadow-lg object-contain" onerror="this.src='{{ asset('images/logo/logo1.png') }}';" />
                    </div>
                </div>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 tracking-wide text-shadow-lg">{{ $aboutTitle }}</h1>
                <p class="text-red-100 text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">{{ $aboutSub }}</p>
            </div>
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none">
                <svg class="relative block w-full h-16 md:h-24" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-gray-50"></path>
                </svg>
            </div>
        </div>

        {{-- ★★★ MAIN CONTENT ZONE ★★★ --}}
        <div class="container mx-auto max-w-5xl px-4 -mt-20 relative z-20">

            {{-- 1. Favorites Loop --}}
            @forelse($favorites as $index => $fav)
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12 relative overflow-hidden" data-aos="fade-up">
                    @php
                        $imageCollection = [];
                        if (isset($fav->images) && $fav->images->count() > 0) {
                            foreach ($fav->images as $img) { $imageCollection[] = asset('storage/' . $img->image_path); }
                        }
                        
                        if (empty($imageCollection) && $fav->image_path) {
                            $imageCollection[] = asset('storage/' . $fav->image_path);
                        }

                        $videoPath = $fav->video_path ?? $fav->video ?? null;
                        $hasMultipleImages = count($imageCollection) > 1;
                        $hasAnyImage = count($imageCollection) > 0;
                        $hasMedia = $hasAnyImage || $videoPath;
                    @endphp

                    <div class="flex flex-col md:flex-row gap-12 {{ !$hasMedia ? 'justify-center' : '' }}">
                        <div class="w-full {{ $hasMedia ? 'md:w-1/2' : 'md:w-3/4' }} {{ $index % 2 == 0 && $hasMedia ? 'order-2 md:order-1' : 'order-2 md:order-2' }}">
                            <div class="flex items-start gap-4 mb-6">
                                <span class="w-1.5 h-10 bg-red-600 rounded-full mt-1 flex-shrink-0"></span>
                                <h2 class="text-3xl font-bold text-gray-800 leading-tight">{{ $fav->title }}</h2>
                            </div>
                            <div class="prose prose-lg text-gray-600 leading-loose font-light">{!! $fav->content !!}</div>
                        </div>

                        @if ($hasMedia)
                            <div class="w-full md:w-1/2 {{ $index % 2 == 0 ? 'order-1 md:order-2' : 'order-1 md:order-1' }} relative">
                                @if ($videoPath)
                                    <div class="relative w-full h-full min-h-[300px] rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-black flex items-center justify-center">
                                        <video controls class="w-full h-full object-cover"><source src="{{ asset('storage/' . $videoPath) }}" type="video/mp4"></video>
                                    </div>
                                @elseif ($hasMultipleImages)
                                    <div class="relative group h-full min-h-[300px] rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-gray-100" id="carousel-{{ $fav->id }}">
                                        <div class="carousel-track flex transition-transform duration-500 ease-in-out h-full w-full" data-index="0">
                                            @foreach ($imageCollection as $key => $imgUrl)
                                                <div class="min-w-full h-full relative cursor-pointer" onclick='openLightbox(@json($imageCollection), {{ $key }})'>
                                                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" alt="{{ $fav->title }}">
                                                    <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                                        <i class="fas fa-search-plus text-white text-3xl drop-shadow-md"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button onclick="moveCarousel('{{ $fav->id }}', -1)" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 md:p-3 rounded-full shadow-lg opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 z-10"><i class="fas fa-chevron-left text-base md:text-lg"></i></button>
                                        <button onclick="moveCarousel('{{ $fav->id }}', 1)" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 md:p-3 rounded-full shadow-lg opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 z-10"><i class="fas fa-chevron-right text-base md:text-lg"></i></button>
                                        <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-10">
                                            @foreach ($imageCollection as $key => $imgUrl)
                                                <div class="carousel-dot w-2 h-2 rounded-full bg-white/50 transition-all duration-300 {{ $key == 0 ? 'bg-white w-4' : '' }}" data-target="{{ $fav->id }}" data-slide="{{ $key }}"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="relative group-img h-full min-h-[300px] cursor-pointer" onclick='openLightbox(@json($imageCollection), 0)'>
                                        <div class="absolute inset-0 bg-red-600 rounded-2xl transform rotate-3 transition-transform duration-300 opacity-10 group-hover:rotate-6"></div>
                                        <img src="{{ $imageCollection[0] }}" alt="{{ $fav->title }}" class="relative rounded-2xl shadow-lg w-full h-full object-cover border-4 border-white transform transition-transform duration-300 group-hover:-translate-y-2">
                                        <div class="absolute inset-0 z-10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                                            <div class="bg-black/40 p-3 rounded-full backdrop-blur-sm"><i class="fas fa-search-plus text-white text-xl"></i></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-20 bg-white rounded-3xl shadow-lg border-dashed border-2 border-gray-300 mb-12">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">เนื้อหากำลังปรับปรุง</p>
                </div>
            @endforelse

            {{-- 2. LIFE WITH ติดใจ --}}
            @if(isset($videos) && $videos->count() > 0)
                <div class="mt-20 pt-10" data-aos="fade-up">
                    <div class="mb-8 border-l-4 border-red-500 pl-4">
                        <h2 class="text-2xl font-bold text-gray-800 tracking-wide">{{ $lifeTitle }}</h2>
                        <p class="text-[15px] text-gray-500 mt-2 font-light">{{ $lifeSub }}</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-12">
                        @foreach($videos as $video)
                            <div class="relative rounded-2xl overflow-hidden aspect-[9/16] bg-gray-900 group cursor-pointer shadow-lg" 
                                data-video="{{ json_encode($video) }}" onclick="playVideo(this)">
                                
                                @php
                                    $thumb = $video->thumbnail_path ? asset('storage/' . $video->thumbnail_path) : ($video->thumbnail_url ?? '');
                                @endphp
                                @if($thumb)
                                    <img src="{{ $thumb }}" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300 group-hover:scale-105">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-gray-600 bg-gray-800"><i class="fas fa-video text-5xl"></i></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 pointer-events-none"></div>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-16 h-16 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300 shadow-xl"><i class="fas fa-play text-white text-2xl ml-1"></i></div>
                                </div>
                                <div class="absolute bottom-4 left-4 right-4 text-white"><h3 class="font-bold text-lg drop-shadow-md leading-tight line-clamp-2">{{ $video->title }}</h3></div>
                                @if($video->duration)
                                    <div class="absolute bottom-4 right-4 bg-black/60 text-white text-xs px-2 py-1 rounded">{{ $video->duration }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- 3. GALLERY --}}
                    @if(isset($galleries) && $galleries->count() > 0)
                        <div class="mt-8 mb-6 border-b border-gray-200 pb-3">
                            <button class="flex items-center gap-2 text-gray-700 font-bold hover:text-red-500 transition-colors"><i class="fas fa-th-large"></i> <span>รวมภาพบรรยากาศ</span> <i class="fas fa-chevron-down text-sm"></i></button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            @foreach($galleries as $gallery)
                                @php
                                    $galImages = $gallery->images->pluck('image_path')->map(fn($p) => asset('storage/' . $p))->toArray();
                                @endphp
                                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 overflow-hidden group cursor-pointer" onclick='openLightbox(@json($galImages), 0)'>
                                    <div class="relative aspect-[4/3] w-full overflow-hidden">
                                        @if($gallery->images->count() > 0)
                                            <img src="{{ asset('storage/' . $gallery->images[0]->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1.5">
                                                @foreach($gallery->images->take(4) as $idx => $img)
                                                    <div class="w-1.5 h-1.5 rounded-full {{ $idx == 0 ? 'bg-white' : 'bg-white/50' }}"></div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4"><h4 class="text-[15px] font-medium text-gray-800">{{ $gallery->title }}</h4></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- 4. SOCIAL & TEAM & CONTACT --}}
            <div class="mt-20 pt-16 border-t border-gray-200" data-aos="fade-up">
                <div class="text-center mb-16">
                    <h2 class="text-xl font-bold text-gray-800 mb-10">{{ $socialTitle }}</h2>
                    
                    {{-- ★ ปรับปรุงส่วน Social (แสดงแค่ไอคอนเพียวๆ ไม่มีเอฟเฟกต์ ไม่มีข้อความ) ★ --}}
                    <div class="flex flex-wrap justify-center gap-8 md:gap-12">
                        @if(isset($socialLinks) && count($socialLinks) > 0)
                            {{-- กรณี 1: มีข้อมูลดึงมาจาก Database --}}
                            @foreach($socialLinks as $link)
                                <div class="cursor-pointer social-icon-item" onclick="window.open('{{ $link->url }}', '_blank')">
                                    <div class="flex items-center justify-center text-4xl md:text-5xl">
                                        @if($link->image_path)
                                            <img src="{{ asset('storage/' . $link->image_path) }}" class="w-12 h-12 object-contain">
                                        @else
                                            <div style="color: {{ $link->bg_color ?? '#333' }};">
                                                <i class="{{ $link->icon_class }}"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- กรณี 2: Database ยังไม่มีข้อมูล (Fallback) --}}
                            @if(isset($socialFB) && $socialFB !== '#')
                                <div class="cursor-pointer" onclick="window.open('{{ $socialFB }}', '_blank')">
                                    <div class="flex items-center justify-center text-4xl md:text-5xl" style="color: #1877F2;">
                                        <i class="fab fa-facebook-f"></i>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($socialIG) && $socialIG !== '#')
                                <div class="cursor-pointer" onclick="window.open('{{ $socialIG }}', '_blank')">
                                    <div class="flex items-center justify-center text-4xl md:text-5xl">
                                        <i class="fab fa-instagram" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($socialTT) && $socialTT !== '#')
                                <div class="cursor-pointer" onclick="window.open('{{ $socialTT }}', '_blank')">
                                    <div class="flex items-center justify-center text-4xl md:text-5xl" style="color: #000000;">
                                        <i class="fab fa-tiktok"></i>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($socialLI) && $socialLI !== '#')
                                <div class="cursor-pointer" onclick="window.open('{{ $socialLI }}', '_blank')">
                                    <div class="flex items-center justify-center text-4xl md:text-5xl" style="color: #0077b5;">
                                        <i class="fab fa-linkedin-in"></i>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="text-center mb-16 space-y-3" data-aos="fade-up">
                    <h2 class="text-xl font-extrabold text-gray-800">{{ $teamTitle }}</h2>
                    <p class="text-sm text-gray-600 font-light">{{ $teamSub }}</p>
                    <p class="text-sm font-bold text-gray-700">โทร & Line : <span class="text-gray-600 font-normal">{{ $teamPhone }}</span></p>
                    <p class="text-sm font-bold text-gray-700">E-mail : <a href="mailto:{{ $teamEmail }}" class="text-blue-500 hover:underline font-normal">{{ $teamEmail }}</a></p>
                </div>

                <div class="text-center mb-10" data-aos="fade-up"><h2 class="text-2xl font-extrabold text-gray-800">🏢 ติดต่อเรา 🏢</h2></div>
                <div class="space-y-16">
                    @forelse($contacts ?? [] as $index => $contact)
                        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12" data-aos="fade-up">
                            <div class="w-full md:w-1/2 text-center space-y-4 {{ $index % 2 != 0 ? 'md:order-2' : '' }}">
                                <h3 class="font-bold text-lg text-gray-800 underline decoration-2 underline-offset-8">{{ $contact->title }}</h3>
                                <p class="text-[14px] text-gray-600 leading-relaxed font-light mt-4">{!! nl2br(e($contact->address)) !!}</p>
                                @if($contact->map_link)
                                <div class="text-[13px] flex items-center justify-center gap-2"><i class="fas fa-map text-emerald-500"></i><span class="font-bold text-gray-700">Map :</span> <a href="{{ $contact->map_link }}" target="_blank" class="text-blue-500 hover:underline truncate max-w-[200px] md:max-w-xs">{{ $contact->map_link }}</a></div>
                                @endif
                                @if($contact->transport_info)
                                <div class="text-[13px] text-gray-600 space-y-2 mt-4 pt-4 border-t border-gray-200 max-w-sm mx-auto">{!! $contact->transport_info !!}</div>
                                @endif
                            </div>
                            <div class="w-full md:w-1/2 flex justify-center {{ $index % 2 != 0 ? 'md:order-1' : '' }}">
                                @if($contact->image_path)
                                    <img src="{{ asset('storage/' . $contact->image_path) }}" class="w-full max-w-sm aspect-[4/3] object-cover shadow-lg border-4 border-white" alt="{{ $contact->title }}">
                                @else
                                    <div class="w-full max-w-sm aspect-[4/3] bg-gray-200 flex items-center justify-center text-gray-400 border-4 border-white shadow-lg"><i class="fas fa-building text-5xl"></i></div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-white rounded-2xl border-2 border-dashed border-gray-300"><p class="text-gray-500 text-sm">ยังไม่มีข้อมูลติดต่อสาขา</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ★ LIGHTBOX MODAL ★ --}}
    <div id="lightboxModal" class="fixed inset-0 z-[999] bg-black/95 hidden items-center justify-center opacity-0 transition-opacity duration-300 select-none">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-red-500 transition-colors z-50 p-4"><i class="fas fa-times text-4xl shadow-lg"></i></button>
        <button id="lb-prev-btn" onclick="navigateLightbox(-1)" class="absolute left-2 md:left-8 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-4 rounded-full hover:bg-white/10 transition-all z-50 hidden"><i class="fas fa-chevron-left text-4xl md:text-5xl drop-shadow-lg"></i></button>
        <button id="lb-next-btn" onclick="navigateLightbox(1)" class="absolute right-2 md:right-8 top-1/2 -translate-y-1/2 text-white/70 hover:text-white p-4 rounded-full hover:bg-white/10 transition-all z-50 hidden"><i class="fas fa-chevron-right text-4xl md:text-5xl drop-shadow-lg"></i></button>
        <div class="relative w-full h-full flex items-center justify-center p-4 md:p-10" onclick="closeLightbox()"><img id="lightboxImage" src="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300" onclick="event.stopPropagation()"><div id="lb-counter" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/80 bg-black/50 px-4 py-1 rounded-full text-sm font-light tracking-widest hidden">1 / 5</div></div>
    </div>

    {{-- ★ VIDEO PLAYER MODAL ★ --}}
    <div id="videoPlayerModal" class="fixed inset-0 z-[1000] bg-black/90 hidden items-center justify-center opacity-0 transition-opacity duration-300">
        <button onclick="closeVideoPlayer()" class="absolute top-4 right-4 text-white hover:text-red-500 transition-colors z-[1010] p-4"><i class="fas fa-times text-4xl"></i></button>
        <div class="w-full max-w-[380px] max-h-[85vh] overflow-y-auto relative bg-white rounded-2xl shadow-2xl mx-4 scrollbar-hide" id="videoContainer">
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true }));
        
        // Carousel Logic
        function moveCarousel(id, direction) {
            const container = document.getElementById(`carousel-${id}`);
            const track = container.querySelector('.carousel-track'), dots = container.querySelectorAll('.carousel-dot');
            const totalItems = track.children.length;
            let currentIndex = parseInt(track.dataset.index || 0), newIndex = (currentIndex + direction + totalItems) % totalItems;
            track.dataset.index = newIndex; track.style.transform = `translateX(-${newIndex * 100}%)`;
            dots.forEach((dot, idx) => { if (idx === newIndex) { dot.classList.add('bg-white', 'w-4'); dot.classList.remove('bg-white/50'); } else { dot.classList.remove('bg-white', 'w-4'); dot.classList.add('bg-white/50'); } });
        }

        // Lightbox Logic
        const lightbox = document.getElementById('lightboxModal'), lightboxImg = document.getElementById('lightboxImage'), lbPrevBtn = document.getElementById('lb-prev-btn'), lbNextBtn = document.getElementById('lb-next-btn'), lbCounter = document.getElementById('lb-counter');
        let currentLightboxImages = [], currentLightboxIndex = 0;
        function openLightbox(images, startIndex) {
            currentLightboxImages = images; currentLightboxIndex = startIndex; updateLightboxContent();
            lightbox.classList.remove('hidden'); lightbox.classList.add('flex'); setTimeout(() => { lightbox.classList.remove('opacity-0'); lightboxImg.classList.replace('scale-95', 'scale-100'); }, 10);
            document.body.style.overflow = 'hidden';
        }
        function updateLightboxContent() {
            lightboxImg.src = currentLightboxImages[currentLightboxIndex];
            if (currentLightboxImages.length > 1) { [lbPrevBtn, lbNextBtn, lbCounter].forEach(el => el.classList.remove('hidden')); lbCounter.innerText = `${currentLightboxIndex + 1} / ${currentLightboxImages.length}`; }
            else { [lbPrevBtn, lbNextBtn, lbCounter].forEach(el => el.classList.add('hidden')); }
        }
        function navigateLightbox(dir) { currentLightboxIndex = (currentLightboxIndex + dir + currentLightboxImages.length) % currentLightboxImages.length; lightboxImg.style.opacity = '0.5'; setTimeout(() => { updateLightboxContent(); lightboxImg.style.opacity = '1'; }, 150); }
        function closeLightbox() { lightbox.classList.add('opacity-0'); lightboxImg.classList.replace('scale-100', 'scale-95'); setTimeout(() => { lightbox.classList.add('hidden'); lightbox.classList.remove('flex'); lightboxImg.src = ''; currentLightboxImages = []; }, 300); document.body.style.overflow = ''; }

        // Video Player Logic
        function playVideo(element) {
            const video = JSON.parse(element.getAttribute('data-video'));
            const modal = document.getElementById('videoPlayerModal');
            const container = document.getElementById('videoContainer');
            
            let embedHtml = video.embed_html;
            
            if (!embedHtml && video.video_url && video.video_url.includes('tiktok.com')) {
                embedHtml = `<blockquote class="tiktok-embed" cite="${video.video_url}" data-video-id="${video.video_url.split('/').pop()}" style="max-width: 605px;min-width: 325px;" > <section> </section> </blockquote>`;
            }

            if (embedHtml) {
                container.innerHTML = embedHtml;
                const oldScript = document.getElementById('tiktok-embed-script');
                if(oldScript) oldScript.remove();

                const script = document.createElement('script');
                script.id = 'tiktok-embed-script';
                script.src = "https://www.tiktok.com/embed.js";
                script.async = true;
                document.body.appendChild(script);
            } else {
                container.innerHTML = `<div class="text-gray-800 text-center p-10"><i class="fas fa-video-slash text-5xl mb-4"></i><br>ไม่สามารถโหลดวิดีโอได้</div>`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
            document.body.style.overflow = 'hidden';
        }

        function closeVideoPlayer() {
            const modal = document.getElementById('videoPlayerModal');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('videoContainer').innerHTML = '';
            }, 300);
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { 
            if (e.key === "Escape") { closeLightbox(); closeVideoPlayer(); }
            else if (!lightbox.classList.contains('hidden')) {
                if (e.key === "ArrowLeft" && currentLightboxImages.length > 1) navigateLightbox(-1);
                else if (e.key === "ArrowRight" && currentLightboxImages.length > 1) navigateLightbox(1);
            }
        });
    </script>

    <style>
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        #videoContainer blockquote { margin: 0 auto !important; width: 100% !important; border-radius: 1rem; }
        #videoContainer iframe { width: 100% !important; display: block; border-radius: 1rem; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        /* =========================================
           CSS สำหรับจัดการ Social Icons (Simplified)
        ========================================= */
        .social-icon-item {
            transition: transform 0.2s ease;
        }
        .social-icon-item:hover {
            transform: scale(1.1);
        }
    </style>
@endsection