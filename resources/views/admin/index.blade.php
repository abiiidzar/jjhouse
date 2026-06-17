@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-600 mt-2">Semua transaksi yang dibayar</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Cost -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total HPP</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalCost ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-red-600 mt-2">Harga Pokok Penjualan</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Profit -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Keuntungan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-green-600 mt-2">
                        {{ $totalCost > 0 ? number_format(($totalProfit / $totalCost * 100), 1) : '0' }}% margin
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Monthly Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Penjualan Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($monthlySales ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-600 mt-2">{{ $ordersThisMonth ?? 0 }} pesanan</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 11h12M8 15h12M3 7h.01M3 11h.01M3 15h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Grafik Penjualan 6 Bulan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-1">Tren pendapatan total</p>
            </div>
            <canvas id="salesChart" height="300"></canvas>
        </div>

        <!-- Profit Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Grafik Keuntungan 6 Bulan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-1">Keuntungan bersih (Penjualan - HPP)</p>
            </div>
            <canvas id="profitChart" height="300"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Produk Terlaris</h3>
                <p class="text-xs text-gray-500 mt-1">Top 5 produk paling banyak terjual</p>
            </div>
            <canvas id="productsChart" height="250"></canvas>
        </div>

        <!-- Key Metrics -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Kunci</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded">
                    <span class="text-sm text-gray-700">Total Pesanan</span>
                    <span class="text-lg font-bold text-blue-600">{{ $totalOrders ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                    <span class="text-sm text-gray-700">Total Pelanggan</span>
                    <span class="text-lg font-bold text-green-600">{{ $totalUsers ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded">
                    <span class="text-sm text-gray-700">Pelanggan Baru (Bulan Ini)</span>
                    <span class="text-lg font-bold text-purple-600">{{ $newUsers ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded">
                    <span class="text-sm text-gray-700">Pre-order Aktif</span>
                    <span class="text-lg font-bold text-yellow-600">{{ $totalPreorderActive ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded">
                    <span class="text-sm text-gray-700">Total Produk</span>
                    <span class="text-lg font-bold text-indigo-600">{{ $totalProducts ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products Management -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📌 Kelola Produk Featured di Home</h3>
        <livewire:admin.manage-featured-products />
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Pesanan Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">No. Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $order->user->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">Rp {{ number_format($order->total_amount + ($order->shipping_cost ?? 0), 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-indigo-100 text-indigo-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $paymentColors = [
                                    'unpaid' => 'bg-red-100 text-red-800',
                                    'dp' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'refunded' => 'bg-orange-100 text-orange-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                ];
                                $paymentClass = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $paymentClass }}">
                                {{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlySalesData['labels'] ?? []) !!},
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($monthlySalesData['data'] ?? []) !!},
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    // Profit Chart
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    new Chart(profitCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyProfitData['labels'] ?? []) !!},
            datasets: [{
                label: 'Keuntungan',
                data: {!! json_encode($monthlyProfitData['data'] ?? []) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    // Products Chart
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($productSalesData['labels'] ?? []) !!},
            datasets: [{
                label: 'Jumlah Terjual',
                data: {!! json_encode($productSalesData['data'] ?? []) !!},
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6',
                    '#ec4899',
                    '#f59e0b',
                    '#10b981'
                ],
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false,
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush

@endsection
