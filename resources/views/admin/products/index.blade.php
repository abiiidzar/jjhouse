@extends('layouts.admin')

@section('title', 'Products')
@section('header', 'Products Management')
@section('subheader', 'Manage your product catalog')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <x-icon name="check" class="w-4 h-4 text-green-500 shrink-0" />
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
        <x-icon name="x-mark" class="w-4 h-4 text-red-500 shrink-0" />
        <span>{{ session('error') }}</span>
    </div>
    @endif


    {{-- Actions Bar --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex gap-3">
            <a href="{{ route('admin.products.create') }}"
               class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
        </div>
        <div class="text-sm text-gray-500">
            Total: <strong>{{ $products->total() }}</strong> produk
        </div>
        <form method="GET" class="flex flex-wrap gap-3 items-center mb-6">

            <!-- SEARCH -->
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari produk..."
                class="border px-3 py-2 rounded-lg text-sm w-64">

            <!-- SORT -->
            <select name="sort"
                    class="border px-3 py-2 rounded-lg text-sm">

                <option value="">Terbaru</option>



                <option value="category" {{ request('sort') == 'category' ? 'selected' : '' }}>
                    Kategori
                </option>

                <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>
                    Stok Terbanyak
                </option>

            </select>

            <!-- BUTTON -->
            <button class="px-4 py-2 bg-black text-white rounded-lg text-sm">
                Apply
            </button>
            <a href="{{ route('admin.products.index') }}"
            class="text-sm text-gray-500 hover:text-black">
                Reset
            </a>
        </form>

    </div>


    {{-- Products Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-16">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Harga Jual</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">HPP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keuntungan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- Thumbnail --}}
                        <td class="px-4 py-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_path }}"
                                         alt="{{ $product->name }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-lg">
                                        <img src="{{ asset('images/asset.png') }}" alt="No Image" class="w-6 h-6">
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Name --}}
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($product->short_description, 45) }}</p>
                        </td>

                        {{-- Category --}}
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $product->category->name ?? '-' }}
                        </td>

                        {{-- Price --}}
                        <td class="px-4 py-3 text-sm font-semibold text-blue-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        {{-- HPP --}}
                        <td class="px-4 py-3 text-sm">
                            @if($product->cost_price)
                                <span class="text-red-600 font-medium">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Profit --}}
                        <td class="px-4 py-3 text-sm">
                            @if($product->profit)
                                <div>
                                    <span class="text-green-600 font-semibold">Rp {{ number_format($product->profit, 0, ',', '.') }}</span>
                                    <br>
                                    <span class="text-xs text-green-500">({{ number_format($product->profit_margin_percent, 1) }}%)</span>
                                </div>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Stock --}}
                        <td class="px-4 py-3 text-sm">
                            <span class="{{ $product->stock <= 5 ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                {{ $product->stock }}
                            </span>
                        </td>

                        {{-- Status Badges --}}
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                    {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                                @if($product->is_preorder)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700 font-medium">Pre-order</span>
                                @endif
                                @if($product->is_featured_home)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700 font-medium inline-flex items-center gap-1">
                                        <x-icon name="pin" class="w-3 h-3" />
                                        <span>Home</span>
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                {{-- Edit --}}
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   title="Edit"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                {{-- Toggle Active --}}
                                <form action="{{ route('admin.products.toggle-active', $product) }}" method="POST" class="inline">
                                    @csrf

                                    <button type="submit"
                                        title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl
                                        {{ $product->is_active
                                            ? 'text-yellow-600 hover:bg-yellow-50'
                                            : 'text-green-600 hover:bg-green-50' }}
                                        transition">

                                    @if($product->is_active)

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
                                        </svg>

                                    @else

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"/>
                                        </svg>

                                    @endif

                                </button>
                                </form>

                                {{-- Delete --}}
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center">
                            <div class="text-gray-300 mb-3 flex justify-center">
                                <x-icon name="cube" class="w-12 h-12" />
                            </div>
                            <p class="text-gray-400 text-sm">Belum ada produk. <a href="{{ route('admin.products.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-4 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
