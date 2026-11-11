@extends('layouts.karyawan')

@section('title', 'Products')
@section('header', 'Daftar Produk')
@section('subheader', 'Kelola katalog produk lengkap')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Produk</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_products'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Stok</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['total_stock'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Produk Aktif</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['active_products'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Habis</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock'] }}</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('karyawan.products.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
                    <input type="text" name="search" placeholder="Nama atau SKU..." value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Cari
                    </button>
                    <a href="{{ route('karyawan.products.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Stok</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="w-8 h-8 rounded object-cover">
                                @else
                                <div class="w-8 h-8 rounded bg-gray-200 flex items-center justify-center text-xs">📷</div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->is_preorder)
                                    <p class="text-xs text-orange-600">📌 Pre-order</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : '' }}
                                {{ $product->stock <= 10 && $product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $product->stock == 0 ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $product->stock }} unit
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs {{ $product->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_active ? '✓ Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('karyawan.stock.edit', $product) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit Stok</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
