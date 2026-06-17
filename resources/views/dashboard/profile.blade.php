@extends('layouts.app')

@section('title', 'Profile')
@section('header', 'Profile')
@section('subheader', 'Manage your account information')

@section('content')

<section data-theme="light" class="px-6 md:px-10 py-16 max-w-5xl mx-auto">

    <h1 class="text-2xl md:text-3xl uppercase tracking-[0.2em] mb-12">
        My Profile
    </h1>

    <!-- SUCCESS -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-10">

        <!-- ================= LEFT: PROFILE ================= -->
        <div class="bg-white border p-6 rounded-lg shadow-sm">

            <h2 class="text-sm uppercase tracking-widest mb-6">
                Account Information
            </h2>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <!-- NAME -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Name</label>
                    <input type="text" name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <!-- EMAIL -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Email</label>
                    <input type="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <!-- PHONE -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Phone</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', auth()->user()->phone) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <!-- ADDRESS -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">{{ old('address', auth()->user()->address) }}</textarea>
                </div>

                <!-- CITY -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">City</label>
                    <input type="text" name="city"
                        value="{{ old('city', auth()->user()->city) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black"
                        placeholder="e.g., Jakarta">
                </div>

                <!-- PROVINCE -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Province</label>
                    <input type="text" name="province"
                        value="{{ old('province', auth()->user()->province) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black"
                        placeholder="e.g., DKI Jakarta">
                </div>

                <!-- POSTAL CODE -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Postal Code</label>
                    <input type="text" name="postal_code"
                        value="{{ old('postal_code', auth()->user()->postal_code) }}"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black"
                        placeholder="e.g., 12345">
                </div>

                <button
                    class="mt-4 w-full bg-black text-white py-3 uppercase text-xs tracking-widest hover:opacity-80 transition">
                    Save Changes
                </button>

            </form>

        </div>


        <!-- ================= RIGHT: PASSWORD ================= -->
        <div class="bg-white border p-6 rounded-lg shadow-sm">

            <h2 class="text-sm uppercase tracking-widest mb-6">
                Change Password
            </h2>

            <form method="POST" action="{{ route('dashboard.password.update') }}">
                @csrf
                @method('PUT')

                <!-- CURRENT -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <!-- NEW -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">New Password</label>
                    <input type="password" name="password"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <!-- CONFIRM -->
                <div class="mb-4">
                    <label class="text-xs text-gray-500 uppercase">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border px-4 py-2 mt-1 rounded-lg focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <button
                    class="mt-4 w-full border py-3 uppercase text-xs tracking-widest hover:bg-black hover:text-white transition">
                    Update Password
                </button>

            </form>

        </div>

    </div>

</section>

@endsection
