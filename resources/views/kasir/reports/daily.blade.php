@extends('layouts.kasir')

@section('title', 'Laporan Harian')
@section('header', 'Laporan Penjualan Harian')
@section('subheader', 'Laporan penjualan tanggal ' . now()->format('d M Y'))

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('kasir.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Kembali ke Dashboard</a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Total Pesanan</p>
                <p class="text-3xl font-bold text-blue-600">{{ $dailyStats['totalOrders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Pesanan Dibayar</p>
                <p class="text-3xl font-bold text-green-600">{{ $dailyStats['paidOrders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Pesanan Belum Dibayar</p>
                <p class="text-3xl font-bold text-red-600">{{ $dailyStats['unpaidOrders'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Total Pendapatan</p>
                <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($dailyStats['totalRevenue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Payment Methods Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Pembayaran Tunai</p>
                        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($dailyStats['cashRevenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-green-50 text-green-500">
                        <x-icon name="banknotes" class="w-8 h-8" />
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Pembayaran Transfer</p>
                        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($dailyStats['transferRevenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-blue-50 text-blue-500">
                        <x-icon name="building-library" class="w-8 h-8" />
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Pembayaran Kartu</p>
                        <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($dailyStats['cardRevenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-purple-50 text-purple-500">
                        <x-icon name="credit-card" class="w-8 h-8" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <x-icon name="clipboard-list" class="w-5 h-5 text-gray-700" />
                <span>Detail Transaksi</span>
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Order ID</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Metode</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($transactions as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-semibold text-blue-600">#{{ $order->id }}</td>
                            <td class="px-6 py-3">{{ $order->user->name }}</td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ ucfirst($order->payment_method ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $order->created_at->format('H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-3 text-center text-gray-500">Tidak ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-6 text-right">
            <button onclick="window.print()" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium inline-flex items-center gap-2">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>
</div>
@endsection
