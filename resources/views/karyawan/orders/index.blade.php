@extends('layouts.karyawan')

@section('title', 'Orders')
@section('header', 'Kelola Pesanan')
@section('subheader', 'Proses dan kelola fulfillment pesanan')

@section('content')
<div class="space-y-6">
    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <x-icon name="search" class="w-5 h-5 text-gray-600" />
            <span>Filter Pesanan</span>
        </h3>
        <form method="GET" action="{{ route('karyawan.orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="ready_to_ship" {{ request('status') === 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pelanggan</label>
                <input type="text" name="search" placeholder="Nama atau nomor pesanan..." value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Cari
                </button>
                <a href="{{ route('karyawan.orders.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm flex items-center gap-1.5">
                <x-icon name="dot" class="w-3 h-3 text-yellow-500 shrink-0" />
                <span>Tertunda (Pending)</span>
            </p>
            <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm flex items-center gap-1.5">
                <x-icon name="dot" class="w-3 h-3 text-orange-500 shrink-0" />
                <span>Diproses (Processing)</span>
            </p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Order::where('status', 'processing')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm flex items-center gap-1.5">
                <x-icon name="dot" class="w-3 h-3 text-green-500 shrink-0" />
                <span>Siap Dikirim (Ready)</span>
            </p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'ready_to_ship')->count() }}</p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">ID Pesanan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Items</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Total</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Waktu</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-blue-600">#{{ $order->id }}</td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->phone }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $order->items->count() }} item</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status === 'processing' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $order->status === 'ready_to_ship' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                            ">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $order->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('karyawan.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-4 border-t bg-gray-50">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded p-4">
        <p class="font-semibold text-blue-900 flex items-center gap-1.5">
            <x-icon name="light-bulb" class="w-4 h-4 text-blue-700" />
            <span>Tips Fulfillment Pesanan</span>
        </p>
        <ul class="text-sm text-blue-800 mt-2 space-y-1">
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Prioritaskan pesanan yang masuk lebih dulu (FIFO - First In First Out)</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Update status saat mulai mengemas (dari Pending → Processing)</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Ubah status menjadi "Ready to Ship" setelah selesai dikemas</span></li>
            <li class="flex items-center gap-1.5"><x-icon name="check" class="w-3.5 h-3.5 text-blue-700 shrink-0" /><span>Perhatikan pemesanan pre-order yang sudah siap untuk dikirim notifikasi ke customer</span></li>
        </ul>
    </div>
</div>
@endsection
