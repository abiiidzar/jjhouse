@extends('layouts.admin')

@section('title', 'Settings')
@section('header', 'Settings')
@section('subheader', 'Kelola pengaturan toko')

@section('content')
<div class="space-y-6">

    <!-- Card Settings -->
    <div class="bg-white rounded-lg shadow-sm p-6 max-w-3xl">

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800">⚙️ General Settings</h3>
            <p class="text-sm text-gray-500">Atur informasi dasar toko Anda</p>
        </div>

        <!-- Error -->
        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-4 rounded">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success -->
        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-4 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Store Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Name</label>
                <input
                    type="text"
                    name="store_name"
                    value="{{ cache('setting_store_name') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('store_name') border-red-500 @enderror"
                    required
                >
                @error('store_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Description</label>
                <textarea
                    name="store_description"
                    rows="4"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('store_description') border-red-500 @enderror"
                >{{ cache('setting_store_description') }}</textarea>
                @error('store_description')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Email</label>
                <input
                    type="email"
                    name="store_email"
                    value="{{ cache('setting_store_email') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('store_email') border-red-500 @enderror"
                    required
                >
                @error('store_email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Phone</label>
                <input
                    type="text"
                    name="store_phone"
                    value="{{ cache('setting_store_phone') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none @error('store_phone') border-red-500 @enderror"
                    required
                >
                @error('store_phone')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Button -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    💾 Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
