<nav x-data="{ mobileMenuOpen: false, cartOpen: false, dark: true }"
    x-init="
        const sections = document.querySelectorAll('[data-theme]');
        function updateNavbar() {
            let current = null;
            sections.forEach(section => {
                const rect = section.getBoundingClientRect();
                if (rect.top <= 100 && rect.bottom >= 100) { current = section; }
            });
            if (current) { dark = current.dataset.theme === 'dark'; }
        }
        updateNavbar();
        window.addEventListener('scroll', updateNavbar);
    "
    :class="dark ? 'bg-transparent text-white' : 'bg-white text-black border-b border-gray-200'"
    class="sticky top-0 z-50 transition-colors duration-300">

    <div class="absolute top-0 left-0 w-full px-4 md:px-10 py-4 md:py-6 z-20">

        {{-- MAIN NAVBAR --}}
        <div class="flex items-center justify-between">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="flex items-center flex-shrink-0 font-bold tracking-tighter">
                <span :class="dark ? 'bg-white text-black' : 'bg-black text-white'" class="flex items-center justify-center px-2 md:px-3 h-8 md:h-9 text-xl md:text-2xl">
                    JJ
                </span>
                <span :class="dark ? 'text-white' : 'text-black'" class="ml-1 text-xl md:text-2xl leading-none">
                    HOUSE
                </span>
            </a>

            {{-- DESKTOP MENU --}}
            <div class="hidden md:flex items-center gap-4 lg:gap-6 text-xs uppercase tracking-[0.2em]">

                {{-- ACCOUNT --}}
                @auth
                    <div class="relative group">
                        <button type="button" class="flex items-center h-7 leading-none hover:opacity-60 uppercase tracking-widest whitespace-nowrap transition">
                            {{ auth()->user()->name }}
                        </button>

                        {{-- ACCOUNT DROPDOWN --}}
                        <div class="absolute right-0 mt-3 w-44 bg-white text-black border border-gray-100 shadow-lg opacity-0 invisible translate-y-2 scale-95 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:scale-100 transition-all duration-200 origin-top-right z-50">
                            <a href="{{ route('home') }}" class="block px-4 py-3 hover:bg-gray-100">Home</a>
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 hover:bg-gray-100">Dashboard</a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="block px-4 py-3 hover:bg-gray-100">My Orders</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-red-500 hover:bg-gray-100">LOGOUT</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">
                        Login
                    </a>
                @endauth

                {{-- NOTIFICATION DESKTOP --}}
                <div class="flex items-center justify-center h-7">
                    <x-notification-bell variant="store" />
                </div>

                {{-- LINKS --}}
                <a href="{{ route('shop') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">Shop Now</a>
                {{-- <a href="{{ route('blog.index') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">Journal</a> --}}
                {{-- <a href="{{ route('community') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">Community</a> --}}
                {{-- <a href="{{ route('about') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">About</a> --}}
                {{-- <a href="{{ route('contact') }}" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">Contact</a> --}}

                {{-- CART DESKTOP --}}
                <div class="relative">
                    <button type="button" @click="cartOpen = !cartOpen; mobileMenuOpen = false;" class="flex items-center h-7 leading-none hover:opacity-60 tracking-widest whitespace-nowrap transition">
                        Cart (<span class="mx-1">@livewire('cart-counter')</span>)
                    </button>

                    <div x-show="cartOpen" x-transition @click.outside="cartOpen = false" x-cloak class="absolute right-0 top-full mt-4 w-96 bg-white text-black border shadow-lg z-50">
                        @livewire('cart-dropdown')
                    </div>
                </div>

            </div>

            {{-- MOBILE ACTIONS --}}
            <div class="flex md:hidden items-center justify-end gap-4">
                <a href="{{ route('shop') }}" class="flex items-center justify-center h-7 text-xs uppercase tracking-widest whitespace-nowrap leading-none hover:opacity-60 transition">
                    Shop Now
                </a>

                {{-- CART MOBILE --}}
                <button type="button" @click="cartOpen = !cartOpen; mobileMenuOpen = false;" class="flex items-center justify-center gap-1 h-7 leading-none" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835L5.25 5.25m0 0L6.75 12a2.25 2.25 0 002.196 1.76h7.608a2.25 2.25 0 002.196-1.76L19.5 6.75H5.25z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19.5h.008v.008H9V19.5zm7.5 0h.008v.008H16.5V19.5z" />
                    </svg>
                    <span class="text-xs font-medium leading-none">@livewire('cart-counter')</span>
                </button>

                {{-- HAMBURGER --}}
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen; cartOpen = false;" class="flex items-center justify-center h-7" aria-label="Menu">
                    <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>

        {{-- MOBILE MENU DROPDOWN --}}
        <div x-show="mobileMenuOpen" x-transition @click.outside="mobileMenuOpen = false" x-cloak class="md:hidden absolute top-full left-4 right-4 mt-2 bg-white text-black border shadow-xl uppercase tracking-[0.15em] text-xs z-50">
            <a href="{{ route('shop') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Shop Now</a>
            {{-- <a href="{{ route('blog.index') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Journal</a> --}}
            {{-- <a href="{{ route('community') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Community</a> --}}
            {{-- <a href="{{ route('about') }}" class="block px-5 py-4 border-b hover:bg-gray-50">About</a> --}}
            {{-- <a href="{{ route('contact') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Contact</a> --}}

            @auth
                <div class="px-5 py-4 border-b font-semibold">{{ auth()->user()->name }}</div>
                <x-notification-bell variant="mobile-menu" />
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Dashboard</a>
                @endif
                <a href="{{ route('dashboard') }}" class="block px-5 py-4 border-b hover:bg-gray-50">My Orders</a>
                <a href="{{ route('profile.edit') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-5 py-4 text-red-500 hover:bg-gray-50">LOGOUT</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-5 py-4 border-b hover:bg-gray-50">Login</a>
            @endauth
        </div>

        {{-- MOBILE CART DROPDOWN --}}
        <div x-show="cartOpen" x-transition @click.outside="cartOpen = false" x-cloak class="md:hidden absolute top-full left-4 right-4 mt-2 bg-white text-black border shadow-xl z-50">
            @livewire('cart-dropdown')
        </div>

    </div>

</nav>