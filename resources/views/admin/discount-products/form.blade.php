@extends('layouts.admin')

@section('title', 'Discount Product')
@section('header', 'Discount Product Management')
@section('subheader', 'Manage discount products')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">
            {{ isset($discountProduct) ? 'Edit Produk dalam Diskon' : 'Tambah Produk dalam Diskon Baru' }}
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

        <form action="{{ isset($discountProduct) ? route('admin.discount-products.update', $discountProduct) : route('admin.discount-products.store') }}"
              method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @if(isset($discountProduct))
                @method('PUT')
            @endif

            <!-- Pilih Diskon -->
            <div>
                <label for="discount_id" class="block text-sm font-medium text-gray-900 mb-2">Pilih Diskon</label>
                <select id="discount_id" name="discount_id" required onchange="updateDiscountInfo()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Diskon --</option>
                    @foreach($discounts as $discount)
                        <option value="{{ $discount->id }}"
                                data-type="{{ $discount->discount_type }}"
                                {{ old('discount_id', $discountProduct->discount_id ?? '') == $discount->id ? 'selected' : '' }}>
                            {{ $discount->name }} ({{ ucfirst($discount->discount_type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Info Diskon -->
            <div id="discount_info" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                <p class="text-sm text-blue-800" id="discount_info_text"></p>
            </div>

            <!-- Pilih Produk -->
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-900 mb-2">Pilih Produk</label>
                <select id="product_id" name="product_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                {{ old('product_id', $discountProduct->product_id ?? '') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (SKU: {{ $product->sku ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quantity Diperlukan (untuk Volume) -->
            <div id="quantity_required_field">
                <label for="quantity_required" class="block text-sm font-medium text-gray-900 mb-2">
                    Jumlah Pembelian yang Diperlukan (untuk Volume Discount)
                </label>
                <input type="number" id="quantity_required" name="quantity_required" min="1"
                       value="{{ old('quantity_required', $discountProduct->quantity_required ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak berlaku</p>
            </div>

            <!-- Quantity Gratis (untuk BOGO) -->
            <div id="quantity_free_field">
                <label for="quantity_free" class="block text-sm font-medium text-gray-900 mb-2">
                    Jumlah Item Gratis (untuk BOGO)
                </label>
                <input type="number" id="quantity_free" name="quantity_free" min="1"
                       value="{{ old('quantity_free', $discountProduct->quantity_free ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak berlaku</p>
            </div>

            <!-- Info Bantuan -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="font-semibold text-yellow-900 mb-2">Petunjuk Penggunaan</h3>
                <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                    <li><strong>Volume Discount:</strong> Isi "Jumlah Pembelian" jika pelanggan membeli minimal jumlah tersebut</li>
                    <li><strong>BOGO (Buy One Get One):</strong> Isi "Jumlah Item Gratis" untuk jumlah item gratis yang akan didapat</li>
                    <li><strong>Tipe Lain:</strong> Biarkan kedua field kosong</li>
                </ul>
            </div>

            <!-- Tombol Submit -->
            <div class="flex gap-4 pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    {{ isset($discountProduct) ? 'Perbarui Produk Diskon' : 'Simpan Produk Diskon' }}
                </button>
                <a href="{{ route('admin.discount-products.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateDiscountInfo() {
    const discountSelect = document.getElementById('discount_id');
    const selectedOption = discountSelect.options[discountSelect.selectedIndex];
    const discountType = selectedOption.getAttribute('data-type');
    const discountInfo = document.getElementById('discount_info');
    const discountInfoText = document.getElementById('discount_info_text');

    if (discountType) {
        let infoText = '';
        switch(discountType) {
            case 'volume':
                infoText = '<strong>Volume Discount:</strong> Masukkan jumlah minimum pembelian di field "Jumlah Pembelian yang Diperlukan"';
                break;
            case 'bogo':
                infoText = '<strong>BOGO:</strong> Masukkan jumlah item gratis di field "Jumlah Item Gratis"';
                break;
            case 'percentage':
                infoText = '<strong>Percentage Discount:</strong> Diskon persentase berlaku otomatis untuk produk ini';
                break;
            case 'fixed':
                infoText = '<strong>Fixed Discount:</strong> Diskon nominal tetap berlaku otomatis untuk produk ini';
                break;
            case 'flash':
                infoText = '<strong>Flash Sale:</strong> Flash sale berlaku otomatis untuk produk ini';
                break;
        }
        discountInfoText.innerHTML = infoText;
        discountInfo.classList.remove('hidden');
    } else {
        discountInfo.classList.add('hidden');
    }
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', updateDiscountInfo);
</script>
@endsection
