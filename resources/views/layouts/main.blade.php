<!DOCTYPE html>
<html lang="th">
<head>
    <style>
        :root {
            --primary-color: {{ $settings['theme_color'] ?? '#059669' }};
            --hero-font-size: {{ $settings['font_size'] ?? '32' }}px;
        }

        /* ตัวอย่าง Class ที่ใช้ Variable */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .hero-title { font-size: var(--hero-font-size); }

        /* General Swiper Arrow Styling */
        .swiper-button-next,
        .swiper-button-prev {
            width: 28px !important;
            height: 28px !important;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 14px !important;
        }
    </style>
</head>
<body class="{{ $settings['bg_tone'] ?? 'bg-white' }}" id="main-body">

    @yield('content')

    @if(request()->has('customizer_mode'))
    <script>
        // Listener: รอรับคำสั่งจาก Admin Frame
        window.addEventListener('message', function(event) {
            const data = event.data;
            
            if (data.action === 'updateTheme') {
                
                // กรณีปรับ CSS Variable (สี, ขนาด)
                if (data.type === 'style') {
                    document.documentElement.style.setProperty(data.key, data.value);
                }
                
                // กรณีแก้ข้อความ (Text)
                else if (data.type === 'text') {
                    // หา element ที่มี attribute data-customize-id ตรงกับ key
                    const el = document.querySelector(`[data-customize-id="${data.key}"]`);
                    if (el) el.innerText = data.value;
                }
                
                // กรณีเปลี่ยน Class (เช่น เปลี่ยน Background Body)
                else if (data.type === 'class') {
                    if (data.key === 'body-bg') {
                        const body = document.getElementById('main-body');
                        // ลบ class bg เดิมๆ ออกให้หมดก่อน
                        body.classList.remove('bg-white', 'bg-gray-50', 'bg-gray-900');
                        // ใส่ class ใหม่
                        body.classList.add(data.value);
                    }
                }
            }
        });

        // ป้องกันการคลิกลิงก์เปลี่ยนหน้าในโหมด Customizer (ถ้าต้องการ)
        document.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', e => e.preventDefault());
        });
    </script>
    @endif

</body>
</html>