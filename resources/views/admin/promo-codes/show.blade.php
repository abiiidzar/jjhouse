@extends('layouts.admin')

@section('title', 'Detail Promo Code')
@section('header', 'Promo Code Management')
@section('subheader', 'Detail promo code')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-mono">{{ $promoCode->code }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kode promo untuk {{ $promoCode->discount->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.promo-codes.edit', $promoCode) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('admin.promo-codes.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kode Promo</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Diskon</dt>
                    <dd class="font-medium text-gray-900">{{ $promoCode->discount->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nilai Diskon</dt>
                    <dd class="font-medium text-gray-900">
                        @if($promoCode->discount->discount_type === 'percentage')
                            {{ $promoCode->discount->discount_value }}%
                        @else
                            Rp {{ number_format($promoCode->discount->discount_value, 0, ',', '.') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Batas Penggunaan</dt>
                    <dd class="font-medium text-gray-900">{{ $promoCode->usage_limit ?: 'Unlimited' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Sudah Digunakan</dt>
                    <dd class="font-medium text-gray-900">{{ $promoCode->used_count }} kali</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Berlaku Dari</dt>
                    <dd class="font-medium text-gray-900">{{ $promoCode->valid_from->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Berlaku Sampai</dt>
                    <dd class="font-medium text-gray-900">{{ $promoCode->valid_until ? $promoCode->valid_until->format('d M Y H:i') : 'Tidak ada batas akhir' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
            <div class="mb-4">
                @if($promoCode->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Tidak Aktif</span>
                @endif
            </div>
            <form action="{{ route('admin.promo-codes.toggle-status', $promoCode) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded">
                    Ubah Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
