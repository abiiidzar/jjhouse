@extends('layouts.admin')

@section('title', 'Promo Code')
@section('header', 'Promo Code Management')
@section('subheader', 'Manage promo codes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ isset($promoCode) ? 'Edit Kode Promo' : 'Tambah Kode Promo Baru' }}
        </h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($promoCode) ? route('admin.promo-codes.update', $promoCode) : route('admin.promo-codes.store') }}"
              method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @if(isset($promoCode))
                @method('PUT')
            @endif

            <!-- Pilih Diskon -->
            <div>
                <label for="discount_id" class="block text-sm font-medium text-gray-900 mb-2">Pilih Diskon</label>
                <select id="discount_id" name="discount_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Diskon --</option>
                    @foreach($discounts as $discount)
                        <option value="{{ $discount->id }}"
                                {{ old('discount_id', $promoCode->discount_id ?? '') == $discount->id ? 'selected' : '' }}>
                            {{ $discount->name }}
                            ({{ $discount->discount_type }} -
                            @if($discount->discount_type === 'percentage')
                                {{ $discount->discount_value }}%
                            @else
                                Rp {{ number_format($discount->discount_value, 0, ',', '.') }}
                            @endif
                            )
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Kode Promo -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-900 mb-2">Kode Promo</label>
                <input type="text" id="code" name="code" required placeholder="contoh: SUMMER2024"
                       value="{{ old('code', strtoupper($promoCode->code ?? '')) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                <p class="text-xs text-gray-500 mt-1">Kode akan otomatis diubah ke HURUF BESAR</p>
            </div>

            <!-- Batas Penggunaan -->
            <div>
                <label for="usage_limit" class="block text-sm font-medium text-gray-900 mb-2">Batas Penggunaan</label>
                <input type="number" id="usage_limit" name="usage_limit" min="1"
                       value="{{ old('usage_limit', $promoCode->usage_limit ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong untuk unlimited</p>
            </div>

            <!-- Tanggal & Waktu Berlaku Dari -->
            <div>
                <label for="valid_from" class="block text-sm font-medium text-gray-900 mb-2">Berlaku Dari</label>
                <input type="datetime-local" id="valid_from" name="valid_from" required
                       value="{{ old('valid_from', isset($promoCode) ? $promoCode->valid_from->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tanggal & Waktu Berlaku Sampai -->
            <div>
                <label for="valid_until" class="block text-sm font-medium text-gray-900 mb-2">Berlaku Sampai</label>
                <input type="datetime-local" id="valid_until" name="valid_until"
                       value="{{ old('valid_until', isset($promoCode) && $promoCode->valid_until ? $promoCode->valid_until->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ada batas akhir</p>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $promoCode->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Kode Promo Aktif
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    {{ isset($promoCode) ? 'Perbarui Kode Promo' : 'Simpan Kode Promo' }}
                </button>
                <a href="{{ route('admin.promo-codes.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Konversi ke HURUF BESAR otomatis
document.getElementById('code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
