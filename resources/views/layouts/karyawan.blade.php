<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Karyawan Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .sidebar {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }

        .card-hover {
            transition: all 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden" data-sidebar-layout data-sidebar-key="karyawan">
    <div data-sidebar-overlay class="fixed inset-0 z-40 bg-black/50 md:hidden"></div>

    <!-- 🔥 SIDEBAR -->
    <aside data-sidebar class="sidebar w-64 shrink-0 bg-gray-900 text-white flex flex-col">

        <!-- LOGO -->
        <div class="p-4 border-b border-gray-800">
            <div class="flex items-center justify-between gap-3">
                <h1 class="text-xl font-bold tracking-tight">
                    <span class="bg-white text-black px-2 py-1">JJ</span>
                    KARYAWAN
                </h1>
                <button type="button" data-sidebar-toggle class="text-white hover:text-gray-300 transition" aria-label="Toggle sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- MENU -->
        <nav class="flex-1 p-4 space-y-2">

            <a href="{{ route('karyawan.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('karyawan.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('karyawan.products.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('karyawan.products.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Produk</span>
            </a>
            <a href="{{ route('karyawan.stock.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition
               {{ request()->routeIs('karyawan.stock.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Stock Produk</span>
            </a>


        </nav>

        <!-- 🔥 USER + LOGOUT -->
        <div class="p-4 border-t border-gray-800 flex items-center gap-3">

            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">Karyawan</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>

        </div>

    </aside>


    <!-- 🔥 MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- TOPBAR -->
        {{-- <header class="bg-white border-b px-6 py-4 flex justify-between items-center">

            <h2 class="text-lg font-semibold text-gray-800">
                @yield('page-title', 'Dashboard')
            </h2>

            <div class="text-sm text-gray-500">
                {{ now()->format('d M Y') }}
            </div>

        </header> --}}

        <header class="bg-white border-b px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <button type="button" data-sidebar-toggle class="p-2 -ml-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-black transition" aria-label="Toggle sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h2 class="text-lg font-semibold text-gray-800">
                    @yield('page-title', 'Dashboard')
                </h2>
            </div>

            <div class="flex items-center gap-3 text-sm text-gray-500">
                <x-notification-bell />
                {{ now()->format('d M Y') }}
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
