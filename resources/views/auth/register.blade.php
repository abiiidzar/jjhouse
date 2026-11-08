@extends('layouts.app')

@section('content')

<section class="min-h-screen flex md:flex-row flex-col" data-theme="light">

    <!-- LEFT IMAGE - Desktop -->
    <div class="hidden md:block md:w-1/2 relative">
        @if(isset($homeImages['hero_image_2']) && $homeImages['hero_image_2'])
            <img src="{{ $homeImages['hero_image_2'] }}"
                 class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                <span class="text-gray-400">No Image</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="absolute bottom-10 left-10 text-white">
            <h2 class="text-3xl font-semibold">JOIN US</h2>
            <p class="text-gray-300 text-sm mt-2">
                Create your JJHOUSE account
            </p>
        </div>
    </div>

    <!-- IMAGE MOBILE (Muncul hanya di mobile) -->
    <div class="md:hidden w-full relative h-64">
        @if(isset($homeImages['hero_image_2']) && $homeImages['hero_image_2'])
            <img src="{{ $homeImages['hero_image_2'] }}"
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                <span class="text-gray-400">No Image</span>
            </div>
        @endif

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="absolute bottom-6 left-6 text-white">
            <h2 class="text-2xl font-semibold">JOIN US</h2>
            <p class="text-gray-300 text-xs mt-1">
                Create your JJHOUSE account
            </p>
        </div>
    </div>

    <!-- RIGHT FORM -->
    <div class="w-full md:w-1/2 flex items-center justify-center px-6 md:px-8 py-10 md:py-0 bg-white">

        <div class="w-full max-w-sm">

            <!-- TITLE -->
            <h1 class="text-xl md:text-2xl tracking-[0.2em] uppercase mb-6 md:mb-10 text-black">
                Register
            </h1>

            <!-- ERROR -->
            @if ($errors->any())
                <div class="text-red-500 text-sm mb-4 md:mb-6">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-6">
                @csrf

                <!-- NAME -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        Name
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2 text-black bg-transparent text-sm md:text-base">
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        Email
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2 text-black bg-transparent text-sm md:text-base">
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        password
                    </label>
                    <input type="password"
                           name="password"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2 text-black bg-transparent text-sm md:text-base">
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        Confirm Password
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2 text-black bg-transparent text-sm md:text-base">
                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="w-full border border-black py-3 text-xs tracking-widest uppercase hover:bg-black hover:text-white transition text-black">
                    Register
                </button>

            </form>

            <!-- LOGIN -->
            <div class="mt-6 md:mt-8 text-xs text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-black hover:underline">
                    Login
                </a>
            </div>

        </div>

    </div>

</section>

@endsection
