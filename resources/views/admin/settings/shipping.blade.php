@extends('layouts.admin')

@section('title', 'Shipping Settings')
@section('header', 'Shipping Settings')
@section('subheader', 'Manage your shipping gateway settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Shipping Settings</h1>
        <p class="text-sm text-gray-500">Konfigurasi pengiriman (RajaOngkir & Free Shipping)</p>
    </div>

    <!-- ERROR -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- SUCCESS -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <x-icon name="check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- CARD -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.settings.shipping.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- API KEY -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    RajaOngkir API Key
                </label>

                <input
                    type="password"
                    name="rajaongkir_api_key"
                    value="{{ cache('shipping_rajaongkir_api_key') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('rajaongkir_api_key') border-red-500 @enderror"
                    required
                >

                @error('rajaongkir_api_key')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-1">
                    Ambil dari dashboard RajaOngkir
                </p>
            </div>

            <!-- ORIGIN CITY -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Default Origin City ID
                </label>

                <input
                    type="number"
                    name="default_origin_city"
                    value="{{ cache('shipping_default_origin_city') ?? 495 }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('default_origin_city') border-red-500 @enderror"
                    required
                >

                @error('default_origin_city')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-1">
                    Default: 495 (Bukittinggi)
                </p>
            </div>

            <!-- FREE SHIPPING -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Free Shipping Threshold (Rp)
                </label>

                <input
                    type="number"
                    name="free_shipping_threshold"
                    value="{{ cache('shipping_free_shipping_threshold') ?? 0 }}"
                    min="0"
                    step="1000"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('free_shipping_threshold') border-red-500 @enderror"
                    required
                >

                @error('free_shipping_threshold')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-1">
                    Isi 0 untuk menonaktifkan free shipping
                </p>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="px-6 py-2 bg-black text-white rounded-lg text-sm font-medium
                           hover:bg-gray-800 transition"
                >
                    Save Changes
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
