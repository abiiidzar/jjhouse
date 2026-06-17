<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
        <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }

        /* Admin specific styles */
        .admin-sidebar {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
        }

        /* Stats card hover effect */
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 font-['Inter']">
    <div class="flex h-screen overflow-hidden" data-sidebar-layout data-sidebar-key="admin">
        <div data-sidebar-overlay class="fixed inset-0 z-40 bg-black/50 md:hidden"></div>
        <!-- Sidebar -->
        <aside x-data="{ sidebarOpen: false }" data-sidebar class="admin-sidebar w-64 shrink-0 bg-gray-900 text-white flex flex-col h-screen">
            <div class="p-4 border-b border-gray-800">
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tighter">
                        <span class="bg-white text-black px-2 py-1">JJ</span>
                        <span class="text-white">ADMIN</span>
                    </a>
                    <button type="button" data-sidebar-toggle class="text-white hover:text-gray-300 transition" aria-label="Toggle sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <nav class="p-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Products -->
                <div x-data="{ open: {{ request()->routeIs(['admin.products.*','admin.stock-alerts.*','admin.stock-history.*']) ? 'true' : 'false'  }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Products</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('admin.products.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.products.index') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        All Products
                        </a>

                        <a href="{{ route('admin.products.create') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.products.create') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Add Product
                        </a>

                        <a href="{{ route('admin.stock-alerts.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.stock-alerts.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Stock Alerts
                        </a>

                        <a href="{{ route('admin.stock-history.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.stock-history.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Stock History
                        </a>
                    </div>
                </div>

                <!-- Orders -->
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.orders.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Orders</span>
                </a>

                <!-- Discounts -->
                <div x-data="{ open: {{ request()->routeIs(['admin.discounts.*','admin.discount-products.*','admin.promo-codes.*']) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 5v14H5V5h14z"/>
                            </svg>
                            <span>Discounts</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('admin.discounts.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.discounts.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        All Discounts
                        </a>

                        <a href="{{ route('admin.discount-products.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.discount-products.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Discount Products
                        </a>

                        <a href="{{ route('admin.promo-codes.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.promo-codes.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Promo Codes
                        </a>
                    </div>
                </div>

                <!-- Pre-orders -->
                <div x-data="{ open: {{ request()->routeIs('admin.preorders.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pre-orders</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('admin.preorders.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.preorders.index') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        All Pre-orders
                        </a>

                        <a href="{{ route('admin.preorders.report') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.preorders.report') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Reports
                        </a>
                    </div>
                </div>

                <!-- Blog -->
                <div x-data="{ open: {{ request()->routeIs('admin.blog.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            <span>Blog</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('admin.blog.posts') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.blog.posts') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Posts
                        </a>

                        <a href="{{ route('admin.blog.categories.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.blog.categories.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Categories
                        </a>

                        <a href="{{ route('admin.blog.comments') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.blog.comments') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Comments
                        </a>
                    </div>
                </div>

                <!-- Instagram -->
                <div x-data="{ open: {{ request()->routeIs(['admin.instagram.*','admin.media.*','admin.newsletter.*']) ? 'true' : 'false'  }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Media</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                        <a href="{{ route('admin.instagram.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.instagram.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        All Posts
                        </a>

                        <a href="{{ route('admin.media.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.media.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Media
                        </a>

                        <a href="{{ route('admin.newsletter.subscribers.index') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.newsletter.*') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Newsletter Subscribers
                        </a>
                    </div>
                </div>
                {{-- <!-- Instagram -->
                <a href="{{ route('admin.instagram.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.instagram.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Instagram Feed</span>
                </a>

                <!-- Media Management -->
                <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.media.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Media</span>
                </a>
                <a href="{{ route('admin.newsletter.subscribers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.newsletter.subscribers.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Newsletter</span>
                </a> --}}

                <!-- Settings -->
                <div x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-gray-400 hover:bg-gray-800 hover:text-white">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Settings</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                       <a href="{{ route('admin.settings.general') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.settings.general') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        General
                        </a>

                        <a href="{{ route('admin.settings.payment') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.settings.payment') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Payment
                        </a>

                        <a href="{{ route('admin.settings.shipping') }}"
                        class="block px-3 py-1 text-sm transition
                        {{ request()->routeIs('admin.settings.shipping') ? 'text-white font-semibold' : 'text-gray-400 hover:text-white' }}">
                        Shipping
                        </a>
                    </div>
                </div>
            </nav>

            <div class="flex-1"></div>

            <div class="p-4 border-t border-gray-800 flex items-center gap-3">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">Administrator</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header -->
            <div class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button type="button" data-sidebar-toggle class="p-2 -ml-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-black transition" aria-label="Toggle sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                        <p class="text-sm text-gray-500 mt-1">@yield('subheader', 'Welcome back, ' . auth()->user()->name)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-600 hover:text-black transition">View Store →</a>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('admin', () => ({
            sidebarOpen: false
        }));

    });
</script>

    @stack('scripts')
</body>
</html>
