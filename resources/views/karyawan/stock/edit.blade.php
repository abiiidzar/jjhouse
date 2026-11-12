@extends('layouts.karyawan')

@section('title', 'Edit Stock Management')
@section('header', 'Edit Stock Management')
@section('subheader', 'Edit your product stock information')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6 md:p-8">
        <form action="{{ route('karyawan.stock.update', $stock) }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Product Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                    <div class="px-4 py-3 bg-gray-100 rounded-lg">
                        <p class="text-lg font-semibold text-gray-900">{{ $stock->name }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <div class="px-4 py-3 bg-gray-100 rounded-lg">
                        <p class="text-gray-900">{{ $stock->category->name ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual</label>
                    <div class="px-4 py-3 bg-gray-100 rounded-lg">
                        <p class="text-lg font-semibold text-blue-600">Rp {{ number_format($stock->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stok Saat Ini</label>
                    <div class="px-4 py-3 bg-gray-100 rounded-lg">
                        <p class="text-3xl font-bold text-green-600">{{ $stock->stock }} unit</p>
                    </div>
                </div>
            </div>

            <hr class="my-6">

            <!-- Stock Update Section -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <x-icon name="chart-bar" class="w-5 h-5 text-gray-700" />
                    <span>Perbarui Stok</span>
                </h3>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stok Baru</label>
                    <input type="number" name="stock" id="stock" value="{{ $stock->stock }}" required min="0"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                           placeholder="Masukkan jumlah stok baru">
                    @error('stock')
                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <button type="button" onclick="document.getElementById('stock').value = {{ $stock->stock }} + 5" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-sm font-medium">+5</button>
                    <button type="button" onclick="document.getElementById('stock').value = {{ $stock->stock }} + 10" class="px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-sm font-medium">+10</button>
                    <button type="button" onclick="document.getElementById('stock').value = Math.max(0, {{ $stock->stock }} - 5)" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded text-sm font-medium">-5</button>
                    <button type="button" onclick="document.getElementById('stock').value = Math.max(0, {{ $stock->stock }} - 10)" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded text-sm font-medium">-10</button>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="space-y-3">
                <label for="notes" class="flex items-center gap-1.5 text-sm font-medium text-gray-700">
                    <x-icon name="pencil-square" class="w-4 h-4 text-gray-600" />
                    <span>Catatan Perubahan (Opsional)</span>
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Contoh: Restock dari supplier, Koreksi stok fisik, dll"></textarea>
                <p class="text-xs text-gray-500">Catatan akan disimpan untuk audit trail</p>
                @error('notes')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold inline-flex items-center justify-center gap-2">
                    <x-icon name="check" class="w-5 h-5" />
                    <span>Simpan Perubahan</span>
                </button>
                <a href="{{ route('karyawan.stock.index') }}" class="flex-1 px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-semibold inline-flex items-center justify-center gap-2">
                    <x-icon name="x-mark" class="w-5 h-5" />
                    <span>Batal</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Historical Info (untuk future implementation) -->
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded p-4">
        <p class="font-semibold text-blue-900 flex items-center gap-1.5">
            <x-icon name="light-bulb" class="w-4 h-4 text-blue-700" />
            <span>Tips Update Stok</span>
        </p>
        <ul class="text-sm text-blue-800 mt-2 space-y-1">
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Gunakan tombol quick action untuk perubahan cepat</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Selalu berikan catatan untuk setiap perubahan stok</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Pastikan stok fisik sudah dihitung ulang sebelum update</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Stok yang benar akan mencegah overselling ke pelanggan</span></li>
        </ul>
    </div>
</div>
@endsection
