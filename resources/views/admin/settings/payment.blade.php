@extends('layouts.admin')

@section('title', 'Payment Settings')
@section('header', 'Payment Settings')
@section('subheader', 'Manage your payment gateway settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Payment Settings</h1>
        <p class="text-sm text-gray-500">Konfigurasi Midtrans untuk pembayaran</p>
    </div>

    <!-- ALERT ERROR -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ALERT SUCCESS -->
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <x-icon name="check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- CARD -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">

        <form action="{{ route('admin.settings.payment.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- SERVER KEY -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Midtrans Server Key
                </label>

                <input
                    type="password"
                    name="midtrans_server_key"
                    value="{{ cache('payment_midtrans_server_key') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('midtrans_server_key') border-red-500 @enderror"
                    required
                >

                @error('midtrans_server_key')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-1">
                    Ambil dari dashboard Midtrans
                </p>
            </div>

            <!-- CLIENT KEY -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Midtrans Client Key
                </label>

                <input
                    type="password"
                    name="midtrans_client_key"
                    value="{{ cache('payment_midtrans_client_key') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('midtrans_client_key') border-red-500 @enderror"
                    required
                >

                @error('midtrans_client_key')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <p class="text-xs text-gray-400 mt-1">
                    Digunakan untuk frontend (Snap / Payment UI)
                </p>
            </div>

            <!-- ENVIRONMENT -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Environment
                </label>

                <select
                    name="midtrans_environment"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                           focus:ring-2 focus:ring-black focus:outline-none
                           @error('midtrans_environment') border-red-500 @enderror"
                    required
                >
                    <option value="sandbox"
                        {{ cache('payment_midtrans_environment') === 'sandbox' ? 'selected' : '' }}>
                        Sandbox (Testing)
                    </option>

                    <option value="production"
                        {{ cache('payment_midtrans_environment') === 'production' ? 'selected' : '' }}>
                        Production (Live)
                    </option>
                </select>

                @error('midtrans_environment')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
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
