@extends('layouts.kasir')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Welcome back, ' . Auth::user()->name)

@section('content')
<div class="min-h-screen bg-gray-100">

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Pesanan Belum Dibayar -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Belum Dibayar</p>
                        <p class="text-3xl font-bold text-red-600">
                            {{ $unpaid }}</p>
                    </div>
                    <div class="text-4xl text-red-200">💳</div>
                </div>

            </div>

            <!-- Total Hari Ini -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Pesanan Hari Ini</p>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</p>
                    </div>
                    <div class="text-4xl text-blue-200">📅</div>
                </div>
            </div>

            <!-- Pendapatan Hari Ini -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Pendapatan Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600">Rp {{ number_format(\App\Models\Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount'), 0, ',', '.') }}</p>
                    </div>
                    <div class="text-4xl text-green-200">💰</div>
                </div>
            </div>

            <!-- Rata-rata Transaksi -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Rata-rata Transaksi</p>
                        @php
                            $paidOrders = \App\Models\Order::whereDate('created_at', today())->where('payment_status', 'paid')->count();
                            $avgTransaction = $paidOrders > 0 ? \App\Models\Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount') / $paidOrders : 0;
                        @endphp
                        <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-4xl text-purple-200">📊</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg shadow p-6 ">
                <p class="text-sm text-gray-500">Cash</p>
                <p class="text-xl font-bold text-green-600">
                    Rp {{ number_format($cashTotal,0,',','.') }}
                </p>
            </div>

            <div class="bg-white p-5 rounded-lg shadow p-6 ">
                <p class="text-sm text-gray-500">Transfer</p>
                <p class="text-xl font-bold text-blue-600">
                    Rp {{ number_format($transferTotal,0,',','.') }}
                </p>
            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Menu Cepat -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">💼 Menu Cepat</h2>
                <div class="space-y-3">
                    <a href="{{ route('kasir.orders.index') }}" class="block p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                        <p class="font-semibold text-red-600">Proses Pembayaran</p>
                        <p class="text-sm text-gray-600">Pesanan yang belum dibayar</p>
                    </a>
                    <a href="{{ route('kasir.reports.daily') }}" class="block p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <p class="font-semibold text-green-600">Laporan Harian</p>
                        <p class="text-sm text-gray-600">Lihat laporan penjualan hari ini</p>
                    </a>
                    <a href="{{ route('kasir.reports.weekly') }}" class="block p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                        <p class="font-semibold text-purple-600">Laporan Mingguan</p>
                        <p class="text-sm text-gray-600">Lihat laporan penjualan minggu ini</p>
                    </a>
                    <a href="{{ route('kasir.reports.cashflow') }}" class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <p class="font-semibold text-blue-600">Arus Kas</p>
                        <p class="text-sm text-gray-600">Pantau aliran kas harian</p>
                    </a>
                </div>
            </div>

            <!-- Informasi Penting -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">ℹ️ Informasi Penting</h2>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <p class="text-sm text-red-800">
                        <strong>Perhatian:</strong> Selalu konfirmasi jumlah pembayaran sebelum menerima uang dari pelanggan.
                    </p>
                </div>
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                    <p class="text-sm text-green-800">
                        <strong>Tips:</strong> Pastikan memberikan bukti pembayaran/struk ke pelanggan setiap transaksi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Pesanan Belum Dibayar -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">💳 Pesanan Menunggu Pembayaran</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">ID Pesanan</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Waktu</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($latestUnpaid as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-semibold">#{{ $order->id }}</td>
                            <td class="px-6 py-3">{{ $order->user->name }}</td>
                            <td class="px-6 py-3 font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3">{{ $order->created_at->format('H:i') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('kasir.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">Proses</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-center text-gray-500">Semua pesanan sudah dibayar ✓</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
