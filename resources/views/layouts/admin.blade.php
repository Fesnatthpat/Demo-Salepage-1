<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>

    {{-- 1. Tailwind CSS + DaisyUI (อันนี้คุณน่าจะมีแล้ว แต่อย่าลืมเช็ค) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 2. FontAwesome (สำหรับไอคอนต่างๆ) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- 3. TomSelect (สำหรับ Dropdown เลือกสินค้าสวยๆ) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    {{-- 4. Alpine.js (หัวใจสำคัญสำหรับระบบฟอร์มนี้) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- สไตล์เพิ่มเติมเล็กน้อยเพื่อให้ TomSelect เข้ากับ DaisyUI --}}
    <style>
        /* ปรับแต่ง TomSelect ให้เข้ากับ Theme */
        .ts-control {
            border-radius: 0.5rem;
            padding: 0.75rem;
            border-color: #d1d5db;
            background-color: white;
        }

        .ts-control.focus {
            border-color: var(--p, #4f46e5);
            /* สี Primary */
            box-shadow: 0 0 0 2px var(--pf, #4f46e5);
        }

        .ts-dropdown {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 50;
        }
    </style>
</head>

<body class="bg-base-200 min-h-screen text-base-content font-sans">

    {{-- ส่วนเนื้อหา --}}
    @yield('content')

</body>

</html>
