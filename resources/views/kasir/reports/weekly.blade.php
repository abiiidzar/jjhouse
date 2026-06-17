@extends('layouts.kasir')

@section('title', 'Laporan Mingguan')
@section('header', 'Laporan Penjualan Mingguan')
@section('subheader', 'Laporan penjualan minggu ini (' . now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y') . ')')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('kasir.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Kembali ke Dashboard</a>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Pendapatan Minggu Ini</p>
                    <p class="text-4xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="text-6xl text-green-200">📊</div>
            </div>
        </div>

        <!-- Weekly Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-7 gap-3 mb-6">
            @foreach($weeklyData as $data)
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <p class="text-sm font-semibold text-gray-600">{{ substr($data['day'], 0, 3) }}</p>
                <p class="text-xs text-gray-500 mb-2">{{ $data['date'] }}</p>
                <p class="text-2xl font-bold text-blue-600">{{ $data['orders'] }}</p>
                <p class="text-xs text-gray-600 mt-2">Pesanan</p>
                <p class="text-lg font-semibold text-green-600 mt-2">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        <!-- Chart Placeholder -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📈 Grafik Penjualan Harian</h2>
            <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                <p class="text-gray-500">Grafik akan ditampilkan di sini (integrasi Chart.js atau Recharts)</p>
            </div>
        </div>

        <!-- Weekly Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Total Pesanan</p>
                <p class="text-3xl font-bold text-blue-600">{{ collect($weeklyData)->sum('orders') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Rata-rata Per Hari</p>
                <p class="text-3xl font-bold text-purple-600">{{ number_format(collect($weeklyData)->sum('orders') / 7, 0) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Rata-rata Revenue</p>
                <p class="text-3xl font-bold text-green-600">Rp {{ number_format($totalRevenue / 7, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-6 text-right">
            <button onclick="window.print()" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium">
                🖨️ Cetak Laporan
            </button>
        </div>
    </div>
</div>
@endsection
