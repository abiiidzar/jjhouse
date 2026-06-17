@extends('layouts.admin')

@section('title', 'Form Discount')
@section('header', 'Form Discount Management')
@section('subheader', ' Form Manage discounts')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ isset($discount) ? 'Edit Diskon' : 'Tambah Diskon Baru' }}
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

        <form action="{{ isset($discount) ? route('admin.discounts.update', $discount) : route('admin.discounts.store') }}"
              method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @if(isset($discount))
                @method('PUT')
            @endif

            <!-- Nama Diskon -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Nama Diskon</label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name', $discount->name ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $discount->description ?? '') }}</textarea>
            </div>

            <!-- Tipe Diskon -->
            <div>
                <label for="discount_type" class="block text-sm font-medium text-gray-900 mb-2">Tipe Diskon</label>
                <select id="discount_type" name="discount_type" required onchange="updateDiscountFields()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="percentage" {{ old('discount_type', $discount->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                    <option value="fixed" {{ old('discount_type', $discount->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                    <option value="bogo" {{ old('discount_type', $discount->discount_type ?? '') === 'bogo' ? 'selected' : '' }}>BOGO (Buy One Get One)</option>
                    <option value="volume" {{ old('discount_type', $discount->discount_type ?? '') === 'volume' ? 'selected' : '' }}>Volume</option>
                    <option value="flash" {{ old('discount_type', $discount->discount_type ?? '') === 'flash' ? 'selected' : '' }}>Flash Sale</option>
                </select>
            </div>

            <!-- Nilai Diskon -->
            <div>
                <label for="discount_value" class="block text-sm font-medium text-gray-900 mb-2">
                    <span id="discount_value_label">Nilai Diskon (%)</span>
                </label>
                <input type="number" id="discount_value" name="discount_value" required step="0.01"
                       value="{{ old('discount_value', $discount->discount_value ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Nilai Diskon Maksimum (untuk persentase) -->
            <div id="maximum_discount_field" class="hidden">
                <label for="maximum_discount" class="block text-sm font-medium text-gray-900 mb-2">Nilai Diskon Maksimum (Rp)</label>
                <input type="number" id="maximum_discount" name="maximum_discount" step="0.01"
                       value="{{ old('maximum_discount', $discount->maximum_discount ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ada batas maksimum</p>
            </div>

            <!-- Pembelian Minimum -->
            <div>
                <label for="minimum_purchase" class="block text-sm font-medium text-gray-900 mb-2">Pembelian Minimum (Rp)</label>
                <input type="number" id="minimum_purchase" name="minimum_purchase" step="0.01"
                       value="{{ old('minimum_purchase', $discount->minimum_purchase ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ada minimum</p>
            </div>

            <!-- Berlaku Untuk -->
            <div>
                <label for="applicable_to" class="block text-sm font-medium text-gray-900 mb-2">Berlaku Untuk</label>
                <select id="applicable_to" name="applicable_to" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all" {{ old('applicable_to', $discount->applicable_to ?? '') === 'all' ? 'selected' : '' }}>Semua Produk</option>
                    <option value="categories" {{ old('applicable_to', $discount->applicable_to ?? '') === 'categories' ? 'selected' : '' }}>Kategori Tertentu</option>
                    <option value="products" {{ old('applicable_to', $discount->applicable_to ?? '') === 'products' ? 'selected' : '' }}>Produk Tertentu</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-900 mb-2">Tanggal & Waktu Mulai</label>
                <input type="datetime-local" id="starts_at" name="starts_at" required
                       value="{{ old('starts_at', isset($discount) ? $discount->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label for="ends_at" class="block text-sm font-medium text-gray-900 mb-2">Tanggal & Waktu Akhir</label>
                <input type="datetime-local" id="ends_at" name="ends_at"
                       value="{{ old('ends_at', isset($discount) && $discount->ends_at ? $discount->ends_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ada batas akhir</p>
            </div>

            <!-- Prioritas -->
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-900 mb-2">Prioritas</label>
                <input type="number" id="priority" name="priority" value="{{ old('priority', $discount->priority ?? 0) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Nilai lebih tinggi = prioritas lebih tinggi</p>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Diskon Aktif
                </label>
            </div>

            <!-- Tombol Submit -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    {{ isset($discount) ? 'Perbarui Diskon' : 'Simpan Diskon' }}
                </button>
                <a href="{{ route('admin.discounts.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateDiscountFields() {
    const discountType = document.getElementById('discount_type').value;
    const maximumDiscountField = document.getElementById('maximum_discount_field');
    const discountValueLabel = document.getElementById('discount_value_label');

    if (discountType === 'percentage') {
        maximumDiscountField.classList.remove('hidden');
        discountValueLabel.textContent = 'Nilai Diskon (%)';
    } else if (discountType === 'fixed') {
        maximumDiscountField.classList.add('hidden');
        discountValueLabel.textContent = 'Nilai Diskon (Rp)';
    } else {
        maximumDiscountField.classList.add('hidden');
        discountValueLabel.textContent = 'Nilai Diskon';
    }
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', updateDiscountFields);
</script>
@endsection
