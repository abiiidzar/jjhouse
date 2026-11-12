@extends('layouts.admin')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Period Filter Toolbar -->
    @php
        $quickPeriods = [
            'all' => 'Semua',
            'today' => 'Hari Ini',
            '7_days' => '7 Hari',
            '3_months' => '3 Bulan',
            '1_year' => '1 Tahun',
        ];
    @endphp
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-5 border border-gray-100">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-700 mr-1">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Periode:</span>
                </span>
                @foreach($quickPeriods as $key => $label)
                    <a href="{{ route('admin.dashboard', ['period' => $key]) }}"
                       class="px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition {{ ($period ?? 'all') === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Per Bulan Filter -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="period" value="month">
                    <label for="dashboard-month" class="text-xs font-medium text-gray-600">Per Bulan:</label>
                    <select id="dashboard-month" name="month" onchange="this.form.submit()"
                            class="text-xs sm:text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 pl-2.5 pr-7 {{ ($period ?? '') === 'month' ? 'border-blue-500 bg-blue-50 text-blue-800 font-medium' : 'bg-white text-gray-700' }}">
                        @foreach(($monthNames ?? []) as $num => $name)
                            <option value="{{ $num }}" {{ (int) ($selectedMonth ?? now()->month) === (int) $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" aria-label="Tahun untuk filter per bulan" onchange="this.form.submit()"
                            class="text-xs sm:text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 pl-2.5 pr-7 {{ ($period ?? '') === 'month' ? 'border-blue-500 bg-blue-50 text-blue-800 font-medium' : 'bg-white text-gray-700' }}">
                        @foreach(($availableYears ?? [now()->year]) as $yr)
                            <option value="{{ $yr }}" {{ (int) ($selectedYear ?? now()->year) === (int) $yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ ($period ?? '') === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Lihat
                    </button>
                </form>

                <!-- Per Tahun Filter -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="period" value="year">
                    <label for="dashboard-year" class="text-xs font-medium text-gray-600">Per Tahun:</label>
                    <select id="dashboard-year" name="year" onchange="this.form.submit()"
                            class="text-xs sm:text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-1.5 pl-2.5 pr-7 {{ ($period ?? '') === 'year' ? 'border-blue-500 bg-blue-50 text-blue-800 font-medium' : 'bg-white text-gray-700' }}">
                        @foreach(($availableYears ?? [now()->year]) as $yr)
                            <option value="{{ $yr }}" {{ (int) ($selectedYear ?? now()->year) === (int) $yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition {{ ($period ?? '') === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Lihat
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-500">
            <div>
                Menampilkan data untuk periode:
                <span class="font-semibold text-gray-800">{{ $periodLabel ?? 'Semua Waktu' }}</span>
            </div>
            @if(($period ?? 'all') !== 'all')
                <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Reset ke Semua Waktu
                </a>
            @endif
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-600 mt-2">{{ $paidOrdersCount ?? 0 }} transaksi dibayar ({{ $periodLabel ?? 'Semua Waktu' }})</p>
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
                    <p class="text-xs text-red-600 mt-2">Harga Pokok Penjualan ({{ $periodLabel ?? 'Semua Waktu' }})</p>
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

        <!-- Period Orders & Average Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Rata-rata Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($averageOrderValue ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-purple-600 mt-2">{{ $totalOrders ?? 0 }} pesanan · {{ $totalItemsSold ?? 0 }} item</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 11h12M8 15h12M3 7h.01M3 11h.01M3 15h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Overview Chart (Row 1) -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Grafik Performa &amp; Ringkasan ({{ $chartRangeLabel ?? '6 Bulan Terakhir' }})</h3>
                <p class="text-xs text-gray-500 mt-1">Gabungan Penjualan, HPP, Keuntungan, Pesanan, Item Terjual &amp; Pelanggan Baru — {{ $periodLabel ?? 'Semua Waktu' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-1.5" id="overviewChartModeGroup">
                <button type="button" data-chart-mode="all"
                        class="chart-mode-btn px-3 py-1 rounded-lg text-xs font-medium bg-blue-600 text-white transition">
                    Semua
                </button>
                <button type="button" data-chart-mode="finance"
                        class="chart-mode-btn px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Keuangan
                </button>
                <button type="button" data-chart-mode="activity"
                        class="chart-mode-btn px-3 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    Aktivitas
                </button>
            </div>
        </div>
        <div class="relative h-80 sm:h-96">
            <canvas id="combinedOverviewChart"></canvas>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Produk Terlaris</h3>
                <p class="text-xs text-gray-500 mt-1">Top 5 produk terjual — {{ $periodLabel ?? 'Semua Waktu' }}</p>
            </div>
            <canvas id="productsChart" height="150"></canvas>
        </div>

        <!-- Key Metrics -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Ringkasan Kunci</h3>
                <span class="text-xs text-gray-500">{{ $periodLabel ?? 'Semua Waktu' }}</span>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded">
                    <span class="text-sm text-gray-700">Total Pesanan ({{ $periodLabel ?? 'Semua Waktu' }})</span>
                    <span class="text-lg font-bold text-blue-600">{{ $totalOrders ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-purple-50 rounded">
                    <span class="text-sm text-gray-700">Pelanggan Baru ({{ $periodLabel ?? 'Semua Waktu' }})</span>
                    <span class="text-lg font-bold text-purple-600">{{ $newUsers ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded">
                    <span class="text-sm text-gray-700">Item Terjual ({{ $periodLabel ?? 'Semua Waktu' }})</span>
                    <span class="text-lg font-bold text-emerald-600">{{ $totalItemsSold ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded">
                    <span class="text-sm text-gray-700">Pre-order Aktif ({{ $periodLabel ?? 'Semua Waktu' }})</span>
                    <span class="text-lg font-bold text-yellow-600">{{ $totalPreorderActive ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded">
                    <span class="text-sm text-gray-700">Total Pelanggan Terdaftar</span>
                    <span class="text-lg font-bold text-green-600">{{ $totalUsers ?? 0 }}</span>
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
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <x-icon name="pin" class="w-5 h-5 text-blue-600" />
            <span>Kelola Produk Featured di Home</span>
        </h3>
        <livewire:admin.manage-featured-products />
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Pesanan Terbaru ({{ $periodLabel ?? 'Semua Waktu' }})</h3>
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
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $order->user?->name ?? 'Guest' }}</td>
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
    const formatRupiah = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    // Combined Overview Chart (Penjualan, HPP, Keuntungan, Pesanan, Item Terjual, Pelanggan Baru)
    const combinedData = @json($combinedChartData ?? []);
    const overviewCtx = document.getElementById('combinedOverviewChart').getContext('2d');
    const combinedOverviewChart = new Chart(overviewCtx, {
        type: 'bar',
        data: {
            labels: combinedData.labels || [],
            datasets: [
                {
                    type: 'line',
                    label: 'Penjualan',
                    data: combinedData.sales || [],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    yAxisID: 'y',
                    order: 1,
                    category: 'finance',
                    unitType: 'currency',
                },
                {
                    type: 'line',
                    label: 'HPP',
                    data: combinedData.cost || [],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.08)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.35,
                    pointRadius: 3.5,
                    pointBackgroundColor: '#ef4444',
                    yAxisID: 'y',
                    order: 2,
                    category: 'finance',
                    unitType: 'currency',
                },
                {
                    type: 'line',
                    label: 'Keuntungan',
                    data: combinedData.profit || [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.14)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    yAxisID: 'y',
                    order: 3,
                    category: 'finance',
                    unitType: 'currency',
                },
                {
                    type: 'bar',
                    label: 'Pesanan',
                    data: combinedData.orders || [],
                    backgroundColor: 'rgba(139, 92, 246, 0.45)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1',
                    order: 4,
                    category: 'activity',
                    unitType: 'count',
                    unitSuffix: 'pesanan',
                },
                {
                    type: 'bar',
                    label: 'Item Terjual',
                    data: combinedData.items || [],
                    backgroundColor: 'rgba(245, 158, 11, 0.45)',
                    borderColor: '#f59e0b',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1',
                    order: 5,
                    category: 'activity',
                    unitType: 'count',
                    unitSuffix: 'item',
                },
                {
                    type: 'line',
                    label: 'Pelanggan Baru',
                    data: combinedData.newUsers || [],
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.15)',
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 3.5,
                    pointBackgroundColor: '#06b6d4',
                    yAxisID: 'y1',
                    order: 6,
                    category: 'activity',
                    unitType: 'count',
                    unitSuffix: 'pelanggan',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const ds = context.dataset;
                            const val = context.parsed.y ?? 0;
                            if (ds.unitType === 'currency') {
                                return ds.label + ': ' + formatRupiah(val);
                            }
                            return ds.label + ': ' + new Intl.NumberFormat('id-ID').format(val) + (ds.unitSuffix ? ' ' + ds.unitSuffix : '');
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nominal (Rp)',
                    },
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Jumlah (Pesanan / Item / Pelanggan)',
                    },
                    ticks: {
                        precision: 0,
                    }
                }
            }
        }
    });

    document.querySelectorAll('.chart-mode-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.chartMode;

            document.querySelectorAll('.chart-mode-btn').forEach((other) => {
                const active = other === btn;
                other.className = 'chart-mode-btn px-3 py-1 rounded-lg text-xs font-medium transition ' +
                    (active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200');
            });

            combinedOverviewChart.data.datasets.forEach((ds, index) => {
                const visible = mode === 'all' || ds.category === mode;
                combinedOverviewChart.setDatasetVisibility(index, visible);
            });

            combinedOverviewChart.options.scales.y.display = mode !== 'activity';
            combinedOverviewChart.options.scales.y1.display = mode !== 'finance';
            combinedOverviewChart.update();
        });
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
