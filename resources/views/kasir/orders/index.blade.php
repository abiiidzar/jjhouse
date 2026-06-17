@extends('layouts.kasir')

@section('title', 'Add Product')
@section('header', 'Add New Orders')
@section('subheader', 'Create a new orders for your store')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('kasir.dashboard') }}" class="text-gray-600 hover:text-gray-800 mr-4">← Kembali</a>
                    <h1 class="text-2xl font-bold text-gray-800">Proses Pembayaran</h1>
                </div>
                <form method="GET" class="mb-4 flex gap-3">

                    <input type="text" name="search"
                        placeholder="Cari pelanggan..."
                        class="border px-3 py-2 rounded">

                    <select name="status" class="border px-3 py-2 rounded">
                        <option value="">Semua</option>
                        <option value="unpaid">Belum Bayar</option>
                        <option value="paid">Sudah Bayar</option>
                    </select>

                    <button class="bg-black text-white px-4 py-2 rounded">
                        Filter
                    </button>

                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID Pesanan</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pelanggan</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status Pembayaran</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Waktu</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-blue-600">#{{ $order->id }}</td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->user->phone }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('kasir.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">Proses</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Semua pesanan sudah dibayar ✓</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
