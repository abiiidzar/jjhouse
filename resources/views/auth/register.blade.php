@extends('layouts.app')

@section('content')

<section class="min-h-screen flex">

    <!-- LEFT IMAGE -->
    <div class="hidden md:block md:w-1/2 relative">
        <img src="{{ asset('images/login.jpg') }}"
             class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/40"></div>

        <div class="absolute bottom-10 left-10 text-white">
            <h2 class="text-3xl font-semibold">JOIN US</h2>
            <p class="text-gray-300 text-sm mt-2">
                Create your JJHOUSE account
            </p>
        </div>
    </div>

    <!-- RIGHT FORM -->
    <div class="w-full md:w-1/2 flex items-center justify-center px-8">

        <div class="w-full max-w-sm">

            <!-- TITLE -->
            <h1 class="text-2xl tracking-[0.2em] uppercase mb-10">
                Register
            </h1>

            <!-- ERROR -->
            @if ($errors->any())
                <div class="text-red-500 text-sm mb-6">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
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
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2">
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
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2">
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        Password
                    </label>
                    <input type="password"
                           name="password"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2">
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-500">
                        Confirm Password
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           required
                           class="w-full border-b border-gray-300 focus:border-black outline-none py-2">
                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="w-full border border-black py-3 text-xs tracking-widest uppercase hover:bg-black hover:text-white transition">
                    Register
                </button>

            </form>

            <!-- LOGIN -->
            <div class="mt-8 text-xs text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="text-black hover:underline">
                    Login
                </a>
            </div>

        </div>

    </div>

</section>

@endsection
