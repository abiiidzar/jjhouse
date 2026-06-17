@extends('layouts.karyawan')

@section('title', 'Dashboard')
@section('header', 'Dashboard Warehouse')
@section('subheader', 'Selamat datang kembali, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Stok -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Stok</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalStock }}</p>
                    <p class="text-xs text-gray-500 mt-2">Unit tersedia</p>
                </div>
                <div class="text-4xl text-blue-100">📦</div>
            </div>
        </div>

        <!-- Pesanan Tertunda -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pesanan Tertunda</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pendingOrders }}</p>
                    <a href="{{ route('karyawan.orders.index') }}?status=pending" class="text-xs text-yellow-600 hover:text-yellow-800 mt-2 inline-block">Lihat detail →</a>
                </div>
                <div class="text-4xl text-yellow-100">⏳</div>
            </div>
        </div>

        <!-- Pesanan Diproses -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pesanan Diproses</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $processingOrders }}</p>
                    <a href="{{ route('karyawan.orders.index') }}?status=processing" class="text-xs text-orange-600 hover:text-orange-800 mt-2 inline-block">Lihat detail →</a>
                </div>
                <div class="text-4xl text-orange-100">🔄</div>
            </div>
        </div>

        <!-- Siap Dikirim -->
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Siap Dikirim</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $readyToShipOrders }}</p>
                    <a href="{{ route('karyawan.orders.index') }}?status=ready_to_ship" class="text-xs text-green-600 hover:text-green-800 mt-2 inline-block">Lihat detail →</a>
                </div>
                <div class="text-4xl text-green-100">✅</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stok Rendah (Low Stock) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">⚠️ Produk Stok Rendah</h2>
                <span class="text-xs bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold">{{ $lowStockCount }} Produk</span>
            </div>
            
            @if($lowStockProducts->isNotEmpty())
                <div class="space-y-3">
                    @foreach($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-3 bg-red-50 border-l-4 border-red-500 rounded">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-xs text-gray-600">Kategori: {{ $product->category->name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-red-600">{{ $product->stock }}</p>
                            <p class="text-xs text-gray-600">unit</p>
                        </div>
                        <a href="{{ route('karyawan.stock.edit', $product) }}" class="ml-4 text-blue-600 hover:text-blue-800 text-sm font-medium">Update</a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">✓ Semua produk memiliki stok yang cukup</p>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📋 Menu Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('karyawan.stock.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                    <span class="text-2xl mr-3">📊</span>
                    <div class="flex-1">
                        <p class="font-semibold text-blue-900">Kelola Stok</p>
                        <p class="text-xs text-blue-700">Update stok produk</p>
                    </div>
                    <span class="text-blue-600">→</span>
                </a>

                <a href="{{ route('karyawan.orders.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                    <span class="text-2xl mr-3">📦</span>
                    <div class="flex-1">
                        <p class="font-semibold text-purple-900">Pesanan Baru</p>
                        <p class="text-xs text-purple-700">Proses pesanan masuk</p>
                    </div>
                    <span class="text-purple-600">→</span>
                </a>

                <a href="{{ route('karyawan.products.index') }}" class="flex items-center p-3 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                    <span class="text-2xl mr-3">📋</span>
                    <div class="flex-1">
                        <p class="font-semibold text-indigo-900">Daftar Produk</p>
                        <p class="text-xs text-indigo-700">Lihat semua produk</p>
                    </div>
                    <span class="text-indigo-600">→</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Orders to Process -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">🎯 Pesanan Butuh Tindakan</h2>
            <a href="{{ route('karyawan.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Lihat semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">ID</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Items</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Waktu</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pendingOrdersList as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-blue-600">#{{ $order->id }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $order->items->count() }} item</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('karyawan.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">Lihat</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Tidak ada pesanan yang tertunda</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Information Box -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded p-4">
            <p class="font-semibold text-yellow-900">💡 Tips Penting</p>
            <ul class="text-sm text-yellow-800 mt-2 space-y-1">
                <li>✓ Update stok setiap hari untuk menghindari overselling</li>
                <li>✓ Prioritaskan pesanan yang lebih dulu masuk</li>
                <li>✓ Catat semua perubahan stok dengan catatan yang jelas</li>
            </ul>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-400 rounded p-4">
            <p class="font-semibold text-blue-900">📌 Status Pesanan</p>
            <ul class="text-sm text-blue-800 mt-2 space-y-1">
                <li>🟡 <strong>Pending</strong> = Pesanan baru, belum dikemas</li>
                <li>🟠 <strong>Processing</strong> = Sedang dikemas</li>
                <li>🟢 <strong>Ready to Ship</strong> = Siap dikirim ke kurir</li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Auto refresh setiap 5 menit
    setTimeout(() => {
        location.reload();
    }, 5 * 60 * 1000);
</script>
@endsection
