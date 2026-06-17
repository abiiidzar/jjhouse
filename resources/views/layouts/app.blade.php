<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Custom Styles -->
    <style>
        html { scroll-behavior: smooth; }

        .btn-brand {
            @apply border px-6 py-2 text-xs tracking-widest uppercase transition;
        }

        .btn-brand:hover {
            @apply bg-black text-white;
        }
        [x-cloak] { display: none !important; }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
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
        }
        .scroll-btn:hover {
            opacity: 1;
        }
        .scroll-btn:after {
            opacity: 1;
            font-weight: bold;
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
            const toast = document.createElement('div');
            toast.className = `px-4 py-3 rounded-lg shadow-lg text-white text-sm transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            toast.innerHTML = message;
            container.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Listen for Livewire events
        Livewire.on('show-toast', ({ message, type }) => {
            showToast(message, type);
        });

        Livewire.on('cart-updated', () => {
            // Optional: play sound or additional feedback
        });
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 800,
                once: true
            });
        });


    document.addEventListener("DOMContentLoaded", () => {

        const slides = document.querySelectorAll(".hero-slide");
        const navBtns = document.querySelectorAll(".nav-btn");

        const preview = document.getElementById("preview");
        const previewImg = document.getElementById("previewImg");

        let current = 0;

        function showSlide(index) {

            slides.forEach((slide, i) => {
                gsap.set(slide, {
                    clipPath: i === index
                        ? "polygon(0 0%, 100% 0%, 100% 100%, 0 100%)"
                        : "polygon(0 100%, 100% 100%, 100% 100%, 0 100%)"
                });
            });

            const slide = slides[index];

            if (slide) {
            gsap.fromTo(slide,
                { clipPath: "polygon(0 100%, 100% 100%, 100% 100%, 0 100%)" },
                {
                    clipPath: "polygon(0 0%, 100% 0%, 100% 100%, 0 100%)",
                    duration: 1.2,
                    ease: "power4.out"
                }
            );
            }

            // update number
            // document.getElementById("slideNum").innerText =
            //     String(index + 1).padStart(2, '0');
            const slideNum = document.getElementById("slideNum");

            if (slideNum) {
                slideNum.innerText = String(index + 1).padStart(2, '0');
            }
            // active state
            // navBtns.forEach(btn => btn.classList.remove("active"));
            // navBtns[index].classList.add("active");
            navBtns.forEach(btn => btn.classList.remove("active"));

            if (navBtns[index]) {
                navBtns[index].classList.add("active");
            }

            current = index;
        }

        // CLICK NAV
        navBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const index = parseInt(btn.dataset.index);
                showSlide(index);
            });

            // HOVER PREVIEW
            btn.addEventListener("mouseenter", () => {
                previewImg.src = btn.dataset.image;

                gsap.to(preview, {
                    opacity: 1,
                    scale: 1,
                    duration: 0.3
                });
            });

            btn.addEventListener("mousemove", (e) => {
                gsap.to(preview, {
                    x: e.clientX + 20,
                    y: e.clientY - 120,
                    duration: 0.2,
                    ease: "power2.out"
                });
            });

            btn.addEventListener("mouseleave", () => {
                gsap.to(preview, {
                    opacity: 0,
                    scale: 0.9,
                    duration: 0.3
                });
            });

        });

        // AUTO SLIDE
        // setInterval(() => {
        //     current = (current + 1) % slides.length;
        //     showSlide(current);
        // }, 5000);

        // INIT
        showSlide(0);

    });


    </script>

    @stack('scripts')
</body>
</html>
