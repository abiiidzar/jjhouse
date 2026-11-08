<aside id="customer-dashboard-sidebar" data-sidebar class="w-full lg:w-72 shrink-0 lg:mr-8 bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4">
        <div>
            <p class="text-xs font-medium uppercase tracking-widest text-gray-400">Akun Saya</p>
            <h2 class="mt-1 font-semibold text-gray-800">Menu Akun</h2>
        </div>
        <button type="button" data-sidebar-toggle class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-black transition" aria-label="Tutup menu akun" aria-controls="customer-dashboard-sidebar" aria-expanded="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <div class="p-5">
    <div class="text-center mb-6">
        <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center text-2xl font-bold text-gray-600">
            {{ auth()->user()->initial }}
        </div>
        <h3 class="font-semibold">{{ auth()->user()->name }}</h3>
        <p class="text-sm text-gray-500 break-words">{{ auth()->user()->email }}</p>
    </div>

    <nav class="space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('dashboard', 'dashboard.order', 'dashboard.invoice') ? 'bg-gray-100 font-medium' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Orders</span>
        </a>

        <a href="{{ route('dashboard.wishlist') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('dashboard.wishlist') ? 'bg-gray-100 font-medium' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span>Wishlist</span>
        </a>

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 transition {{ request()->routeIs('profile.*') ? 'bg-gray-100 font-medium' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profile</span>
        </a>

        <hr class="my-3">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </nav>
    </div>
</aside>
