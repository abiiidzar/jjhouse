@extends('layouts.karyawan')

@section('title', 'Stock Management')
@section('header', 'Kelola Stok Produk')
@section('subheader', 'Monitor dan update stok real-time')

@section('content')
<div class="space-y-6">
    @if ($message = Session::get('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
        <span>{{ $message }}</span>
        <button onclick="this.parentElement.style.display='none'" class="text-green-700 font-bold">×</button>
    </div>
    @endif

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">🔍 Cari & Filter Produk</h3>
        <form method="GET" action="{{ route('karyawan.stock.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="search" placeholder="Cari nama produk..." value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Stok</label>
                    <select name="stock_filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Stok</option>
                        <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Stok Rendah (< 5)</option>
                        <option value="critical" {{ request('stock_filter') === 'critical' ? 'selected' : '' }}>Kritis (0 Unit)</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Cari
                    </button>
                    <a href="{{ route('karyawan.stock.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kategori</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Stok Saat Ini</th>
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
                                <img src="{{ $product->primaryImage->image_path }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                                @else
                                <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center">📷</div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    @if($product->is_preorder)
                                    <p class="text-xs text-orange-600">Pre-order</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : '' }}
                                {{ $product->stock <= 10 && $product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $product->stock == 0 ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ $product->stock }} unit
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs {{ $product->is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('karyawan.stock.edit', $product) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Edit Stok</a>
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
        <div class="px-4 py-4 border-t bg-gray-50">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded p-4">
        <p class="font-semibold text-blue-900">💡 Tips Manajemen Stok</p>
        <ul class="text-sm text-blue-800 mt-2 space-y-1">
            <li>✓ Update stok setiap hari untuk mencegah overselling</li>
            <li>✓ Perhatikan produk dengan stok kuning (< 10 unit) dan merah (habis)</li>
            <li>✓ Catat perubahan stok dengan catatan jelas untuk audit trail</li>
        </ul>
    </div>
</div>
@endsection

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
