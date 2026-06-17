@extends('layouts.kasir')

@section('title', 'Arus Kas')
@section('header', 'Tracking Arus Kas (Cash Flow)')
@section('subheader', 'Pantau aliran kas harian - ' . now()->format('d M Y'))

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('kasir.dashboard') }}" class="text-gray-600 hover:text-gray-800">← Kembali ke Dashboard</a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Cash -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">TUNAI (CASH)</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($paymentMethods['cash'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-2">Pembayaran tunai masuk</p>
                    </div>
                    <div class="text-5xl text-green-200">💵</div>
                </div>
            </div>

            <!-- Total Transfer -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">TRANSFER</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">Rp {{ number_format($paymentMethods['transfer'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-2">Transfer bank masuk</p>
                    </div>
                    <div class="text-5xl text-blue-200">🏦</div>
                </div>
            </div>

            <!-- Total Card -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">KARTU KREDIT</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">Rp {{ number_format($paymentMethods['card'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-2">Pembayaran kartu masuk</p>
                    </div>
                    <div class="text-5xl text-purple-200">💳</div>
                </div>
            </div>
        </div>

        <!-- Total Cash Flow -->
        <div class="bg-gradient-to-r from-green-500 to-blue-500 rounded-lg shadow p-8 mb-6 text-white">
            <div class="text-center">
                <p class="text-lg font-semibold opacity-90">Total Arus Kas Hari Ini</p>
                <p class="text-5xl font-bold mt-2">Rp {{ number_format($paymentMethods['cash'] + $paymentMethods['transfer'] + $paymentMethods['card'], 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Breakdown Table -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Ringkasan Metode Pembayaran</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">💵</span>
                            <p class="font-semibold text-gray-700">Tunai</p>
                        </div>
                        <p class="text-xl font-bold text-green-600">Rp {{ number_format($paymentMethods['cash'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🏦</span>
                            <p class="font-semibold text-gray-700">Transfer</p>
                        </div>
                        <p class="text-xl font-bold text-blue-600">Rp {{ number_format($paymentMethods['transfer'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">💳</span>
                            <p class="font-semibold text-gray-700">Kartu Kredit</p>
                        </div>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($paymentMethods['card'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Percentage Breakdown -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🎯 Persentase Pembayaran</h2>
                @php
                    $total = $paymentMethods['cash'] + $paymentMethods['transfer'] + $paymentMethods['card'];
                    $cashPercent = $total > 0 ? ($paymentMethods['cash'] / $total) * 100 : 0;
                    $transferPercent = $total > 0 ? ($paymentMethods['transfer'] / $total) * 100 : 0;
                    $cardPercent = $total > 0 ? ($paymentMethods['card'] / $total) * 100 : 0;
                @endphp
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-700 font-semibold">Tunai</span>
                            <span class="text-green-600 font-bold">{{ number_format($cashPercent, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: {{ $cashPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-700 font-semibold">Transfer</span>
                            <span class="text-blue-600 font-bold">{{ number_format($transferPercent, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $transferPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-700 font-semibold">Kartu Kredit</span>
                            <span class="text-purple-600 font-bold">{{ number_format($cardPercent, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-purple-500 h-3 rounded-full" style="width: {{ $cardPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">💰 Riwayat Transaksi Hari Ini</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Waktu</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Pelanggan</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Metode</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($transactionHistory as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-semibold text-gray-800">{{ $transaction->created_at->format('H:i:s') }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $transaction->user->name }}</td>
                            <td class="px-6 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $transaction->payment_method == 'cash' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $transaction->payment_method == 'transfer' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $transaction->payment_method == 'card' ? 'bg-purple-100 text-purple-700' : '' }}
                                ">
                                    {{ ucfirst($transaction->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-gray-800">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $transaction->payment_status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $transaction->payment_status_label }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-center text-gray-500">Tidak ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Print & Export -->
        <div class="mt-6 flex gap-3 justify-end">
            <button onclick="window.print()" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-medium">
                🖨️ Cetak Laporan
            </button>
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                📥 Export Excel
            </button>
        </div>
    </div>
</div>
@endsection
