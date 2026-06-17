<nav
    x-data="{ mobileMenuOpen: false, cartOpen: false, searchOpen: false, dark: true }"

    x-init="
        const sections = document.querySelectorAll('[data-theme]');

        function updateNavbar() {
            let current = null;

            sections.forEach(section => {
                const rect = section.getBoundingClientRect();

                if (rect.top <= 100 && rect.bottom >= 100) {
                    current = section;
                }
            });

            if (current) {
                dark = current.dataset.theme === 'dark';
            }
        }

        updateNavbar();
        window.addEventListener('scroll', updateNavbar);
    "

    :class="dark
        ? 'bg-transparent text-white'
        : 'bg-white text-black border-b border-gray-200'
    "

    class="sticky top-0 z-50 transition duration-300"
>    <!-- Top Bar -->
    <!-- Overlay Navbar -->
    <div class="absolute top-1 left-0 w-full flex justify-between px-10 py-6 text-xs tracking-[0.2em] uppercase z-20">

        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter">
                <span :class="dark ? 'bg-white text-black' : 'bg-black text-white'"
                    class="px-3 py-1">
                    JJ
                </span>

                <span :class="dark ? 'text-white' : 'text-black' ">
                    HOUSE
                </span>
            </a>

        <div class=" absolute top-2 right-10 flex gap-8">
            @auth
            <div class="relative group">

                <span class=" hover:opacity-60 uppercase tracking-widest cursor-pointer">
                    {{ auth()->user()->name }}
                </span>

                <!-- Dropdown -->
                <div
                    class="absolute right-0 mt-3 w-44
                        bg-white text-black
                        border border-gray-100
                        shadow-lg
                        opacity-0 translate-y-2 scale-95
                        group-hover:opacity-100
                        group-hover:translate-y-0
                        group-hover:scale-100
                        transition-all duration-200 ease-out
                        origin-top-right
                        z-50"
                    >
                        <a href="{{ route('home') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                            Home
                        </a>
                        {{-- 🔥 ADMIN MENU --}}
                        @if(auth()->user()->hasRole('admin'))

                            <a href="{{ route('admin.dashboard') }}"
                            class="block px-4 py-2 text-sm hover:bg-gray-100">
                                Dashboard
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                            My Orders
                        </a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">
                            Profile
                        </a>


                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>

                </div>

            </div>
        @else
            <a href="{{ route('login') }}"
            class="hover:opacity-60 uppercase tracking-widest">
                Login
            </a>
        @endauth
            <a href="{{ route('shop') }}" class="hover:opacity-60 uppercase tracking-widest">
                Shop Now
            </a>
            <div class="relative" x-data="{ cartOpen: false }">
                <button @click="cartOpen = !cartOpen"
                        class="hover:opacity-60 uppercase tracking-widest">
                    CART ( <span class="">@livewire('cart-counter')</span> )
                </button>

                <!-- Dropdown -->
                <div x-show="cartOpen"
                x-transition
                @click.outside="cartOpen = false"
                x-cloak
                class="absolute right-0 mt-4 w-96 bg-white text-black border z-50">
                    @livewire('cart-dropdown')
                </div>

            </div>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div class="h-[1px]"></div>
