<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name') . ' - Official Store')</title>
    <meta name="description" content="@yield('description', 'Jjhouse - Streetwear modern untuk generasi masa kini. Temukan koleksi terbaru hoodie, t-shirt, dan aksesoris eksklusif.')">
    <meta name="keywords" content="streetwear, fashion, hoodie, tshirt, jjhouse, clothing, indonesia">

    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Jjhouse - Streetwear modern')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', 'Jjhouse - Streetwear modern')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('jj.png') }}">
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} Journal RSS Feed" href="{{ route('blog.feed') }}">

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Custom Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        .btn-brand {
            @apply border px-6 py-2 text-xs tracking-widest uppercase transition;
        }

        .btn-brand:hover {
            @apply bg-black text-white;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes marquee {
            0% {
                transform: translateX(0%);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            animation: marquee 10s linear infinite;
        }

        .marquee:hover {
            animation-play-state: paused;
        }

        .animate-marquee-navbar {
            animation: marquee 40s linear infinite;
        }

        .nav-btn {
            opacity: 0.4;
            transition: 0.3s;
            cursor: pointer;
        }

        .nav-btn:hover {
            opacity: 1;
        }

        .nav-btn.active {
            opacity: 1;
            font-weight: bold;
        }

        .scroll-btn {
            opacity: 0.4;
            transition: 0.3s;
            cursor: pointer;
        }

        .scroll-btn:hover {
            opacity: 1;
        }

        /* ===== MOBILE RESPONSIVE FIXES ===== */
        @media (max-width: 640px) {
            /* Hero - teks lebih kecil dan rapi */
            .hero-slide .absolute.bottom-10 {
                padding-right: 1rem;
            }

            /* Nav buttons - lebih besar untuk touch */
            .nav-btn {
                min-height: 36px;
                min-width: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px !important;
                padding: 0 8px;
            }

            /* Perbaiki marquee speed untuk mobile */
            .animate-marquee-navbar {
                animation-duration: 20s !important;
            }

            /* Editorial grid - height lebih pendek di mobile */
            .grid-cols-1 img {
                height: 250px !important;
            }

            /* Hide scrollbar untuk category di mobile */
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            /* Perbaiki select dropdown di mobile */
            select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 0.5rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                padding-right: 2.5rem;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }

            /* Perbaiki grid produk di mobile */
            .grid-cols-2 {
                gap: 0.75rem;
            }

            /* Perbaiki pagination di mobile */
            nav[role="navigation"] .flex {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }

            nav[role="navigation"] .flex span,
            nav[role="navigation"] .flex a {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }

        /* Line clamp untuk nama produk */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== NAVBAR MOBILE ONLY ===== */
        @media (max-width: 768px) {
            /* Perbaiki padding navbar */
            nav .absolute.top-1 {
                padding-left: 1rem;
                padding-right: 1rem;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }

            /* Perbaiki cart dropdown di mobile */
            .absolute.right-0.mt-4 {
                right: -0.5rem !important;
                width: calc(100vw - 2.5rem) !important;
                max-width: 300px !important;
            }
        }

        @media (max-width: 480px) {
            /* Cart dropdown lebih kecil di HP kecil */
            .absolute.right-0.mt-4 {
                width: calc(100vw - 2rem) !important;
                max-width: 280px !important;
                right: -0.25rem !important;
            }
        }

        /* Safe area untuk iPhone notch */
        @supports (padding: max(0px)) {
            .safe-area-bottom {
                padding-bottom: max(env(safe-area-inset-bottom), 1rem);
            }
        }

        /* Touch target minimal 44px untuk aksesibilitas */
        button,
        a,
        .nav-btn,
        [role="button"] {
            touch-action: manipulation;
            min-height: 44px;
            min-width: 44px;
        }

        /* Prevent zoom pada input di iOS */
        input, select, textarea {
            font-size: 16px !important;
        }

        /* Smooth scrolling untuk mobile */
        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="font-['Inter'] antialiased bg-white">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        @include('layouts.navbar')

        <!-- Main Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    @livewireScripts

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `px-4 py-3 rounded-lg shadow-lg text-white text-sm transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            toast.innerHTML = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        if (typeof Livewire !== 'undefined') {
            Livewire.on('show-toast', ({ message, type }) => {
                showToast(message, type);
            });

            Livewire.on('cart-updated', () => {});
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true
                });
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            const slides = document.querySelectorAll(".hero-slide");
            const navBtns = document.querySelectorAll(".nav-btn");
            const preview = document.getElementById("preview");
            const previewImg = document.getElementById("previewImg");

            if (slides.length === 0) return;

            let current = 0;

            function showSlide(index) {
                if (index < 0 || index >= slides.length) return;

                slides.forEach((slide, i) => {
                    if (typeof gsap !== 'undefined') {
                        gsap.set(slide, {
                            clipPath: i === index
                                ? "polygon(0 0%, 100% 0%, 100% 100%, 0 100%)"
                                : "polygon(0 100%, 100% 100%, 100% 100%, 0 100%)"
                        });
                    } else {
                        slide.style.clipPath = i === index
                            ? "polygon(0 0%, 100% 0%, 100% 100%, 0 100%)"
                            : "polygon(0 100%, 100% 100%, 100% 100%, 0 100%)";
                    }
                });

                const slide = slides[index];
                if (slide && typeof gsap !== 'undefined') {
                    gsap.fromTo(slide,
                        { clipPath: "polygon(0 100%, 100% 100%, 100% 100%, 0 100%)" },
                        {
                            clipPath: "polygon(0 0%, 100% 0%, 100% 100%, 0 100%)",
                            duration: 1.2,
                            ease: "power4.out"
                        }
                    );
                }

                const slideNum = document.getElementById("slideNum");
                if (slideNum) {
                    slideNum.innerText = String(index + 1).padStart(2, '0');
                }

                navBtns.forEach(btn => btn.classList.remove("active"));
                if (navBtns[index]) {
                    navBtns[index].classList.add("active");
                }

                current = index;
            }

            navBtns.forEach(btn => {
                btn.addEventListener("click", () => {
                    const index = parseInt(btn.dataset.index);
                    if (!isNaN(index)) {
                        showSlide(index);
                    }
                });

                if (preview && previewImg && !('ontouchstart' in window)) {
                    btn.addEventListener("mouseenter", () => {
                        previewImg.src = btn.dataset.image || '';
                        if (typeof gsap !== 'undefined') {
                            gsap.to(preview, {
                                opacity: 1,
                                scale: 1,
                                duration: 0.3
                            });
                        }
                    });

                    btn.addEventListener("mousemove", (e) => {
                        if (typeof gsap !== 'undefined') {
                            gsap.to(preview, {
                                x: e.clientX + 20,
                                y: e.clientY - 120,
                                duration: 0.2,
                                ease: "power2.out"
                            });
                        }
                    });

                    btn.addEventListener("mouseleave", () => {
                        if (typeof gsap !== 'undefined') {
                            gsap.to(preview, {
                                opacity: 0,
                                scale: 0.9,
                                duration: 0.3
                            });
                        }
                    });
                }
            });

            navBtns.forEach(btn => {
                btn.addEventListener("touchstart", (e) => {
                    const index = parseInt(btn.dataset.index);
                    if (!isNaN(index)) {
                        showSlide(index);
                        e.preventDefault();
                    }
                }, { passive: false });
            });

            let touchStartX = 0;
            let touchEndX = 0;
            const slider = document.getElementById('heroSlider');

            if (slider && 'ontouchstart' in window) {
                slider.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });

                slider.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    const diff = touchStartX - touchEndX;
                    const threshold = 50;

                    if (Math.abs(diff) > threshold) {
                        if (diff > 0) {
                            current = (current + 1) % slides.length;
                        } else {
                            current = (current - 1 + slides.length) % slides.length;
                        }
                        showSlide(current);
                    }
                }, { passive: true });
            }

            showSlide(0);
        });
    </script>

    @stack('scripts')
</body>
</html>
