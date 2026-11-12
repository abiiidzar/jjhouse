<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    private const ALLOWED_PERIODS = [
        'all',
        'today',
        '7_days',
        '3_months',
        '1_year',
        'month',
        'year',
    ];

    private const MONTH_NAMES = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Display the admin dashboard filtered by the selected time period.
     */
    public function index(Request $request)
    {
        $filter = $this->resolvePeriod($request);
        $startDate = $filter['startDate'];
        $endDate = $filter['endDate'];

        // Overall catalog & customer counts
        $totalUsers = User::count();
        $totalProducts = Product::count();

        // Period-filtered orders & users
        $ordersQuery = $this->applyDateRange(Order::query(), 'created_at', $startDate, $endDate);
        $totalOrders = (clone $ordersQuery)->count();

        $paidOrders = $this->applyDateRange(
            Order::query()->where('payment_status', 'paid'),
            'created_at',
            $startDate,
            $endDate
        )->get(['id', 'total_amount', 'created_at']);

        $paidOrdersCount = $paidOrders->count();
        $totalSales = (float) $paidOrders->sum('total_amount');
        $averageOrderValue = $paidOrdersCount > 0 ? $totalSales / $paidOrdersCount : 0;

        $paidOrderItems = $this->getPaidOrderItems($startDate, $endDate);

        $totalCost = 0;
        $totalProfit = 0;
        $totalItemsSold = 0;

        foreach ($paidOrderItems as $item) {
            $itemCost = ($item->product?->cost_price ?? 0) * $item->quantity;
            $totalCost += $itemCost;
            $totalProfit += $item->subtotal - $itemCost;
            $totalItemsSold += (int) $item->quantity;
        }

        $totalPreorderActiveQuery = OrderItem::query()
            ->where('is_preorder', true)
            ->whereNull('preorder_ready_at');

        if ($startDate && $endDate) {
            $totalPreorderActiveQuery->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        $totalPreorderActive = $totalPreorderActiveQuery->count();

        $newUsers = $this->applyDateRange(User::query(), 'created_at', $startDate, $endDate)->count();

        $recentOrders = $this->applyDateRange(
            Order::with('user'),
            'created_at',
            $startDate,
            $endDate
        )->latest()->take(10)->get();

        // Build charts matching the selected period
        $chartSeries = $this->buildChartSeries($filter);
        $monthlySalesData = $chartSeries['sales'];
        $monthlyProfitData = $chartSeries['profit'];
        $combinedChartData = $chartSeries['combined'];
        $productSalesData = $this->getProductSalesData($paidOrderItems);

        // Featured products
        $featuredProducts = Product::where('is_featured', true)->take(5)->get();
        $featuredHomeProducts = Product::where('is_featured_home', true)->get();

        // Backward-compatible aliases + filter metadata
        $monthlySales = $totalSales;
        $monthlyCost = $totalCost;
        $monthlyProfit = $totalProfit;
        $ordersThisMonth = $totalOrders;

        $period = $filter['period'];
        $selectedMonth = $filter['month'];
        $selectedYear = $filter['year'];
        $periodLabel = $filter['label'];
        $chartRangeLabel = $filter['chartRangeLabel'];
        $availableYears = $this->getAvailableYears($selectedYear);
        $monthNames = self::MONTH_NAMES;

        return view('admin.index', compact(
            'totalSales',
            'totalCost',
            'totalProfit',
            'monthlySales',
            'monthlyCost',
            'monthlyProfit',
            'paidOrdersCount',
            'averageOrderValue',
            'totalItemsSold',
            'totalPreorderActive',
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'recentOrders',
            'monthlySalesData',
            'monthlyProfitData',
            'combinedChartData',
            'productSalesData',
            'featuredProducts',
            'featuredHomeProducts',
            'newUsers',
            'ordersThisMonth',
            'period',
            'selectedMonth',
            'selectedYear',
            'periodLabel',
            'chartRangeLabel',
            'availableYears',
            'monthNames'
        ));
    }

    /**
     * Resolve query parameters into a normalized period definition and date bounds.
     *
     * @return array{
     *     period: string,
     *     month: int,
     *     year: int,
     *     startDate: ?Carbon,
     *     endDate: ?Carbon,
     *     label: string,
     *     chartRangeLabel: string,
     * }
     */
    private function resolvePeriod(Request $request): array
    {
        $period = (string) $request->query('period', '7_days');
        if (!in_array($period, self::ALLOWED_PERIODS, true)) {
            $period = '7_days';
        }

        $now = now();
        $month = max(1, min(12, (int) $request->query('month', $now->month)));
        $yearInput = (int) $request->query('year', $now->year);
        $year = ($yearInput >= 2000 && $yearInput <= 2100) ? $yearInput : $now->year;

        return match ($period) {
            'today' => [
                'period' => 'today',
                'month' => $month,
                'year' => $year,
                'startDate' => $now->copy()->startOfDay(),
                'endDate' => $now->copy()->endOfDay(),
                'label' => 'Hari Ini (' . $now->format('d M Y') . ')',
                'chartRangeLabel' => 'Per Jam Hari Ini',
            ],
            '7_days' => [
                'period' => '7_days',
                'month' => $month,
                'year' => $year,
                'startDate' => $now->copy()->subDays(6)->startOfDay(),
                'endDate' => $now->copy()->endOfDay(),
                'label' => '7 Hari Terakhir',
                'chartRangeLabel' => '7 Hari Terakhir',
            ],
            '3_months' => [
                'period' => '3_months',
                'month' => $month,
                'year' => $year,
                'startDate' => $now->copy()->subMonthsNoOverflow(3)->startOfDay(),
                'endDate' => $now->copy()->endOfDay(),
                'label' => '3 Bulan Terakhir',
                'chartRangeLabel' => '3 Bulan Terakhir',
            ],
            '1_year' => [
                'period' => '1_year',
                'month' => $month,
                'year' => $year,
                'startDate' => $now->copy()->subMonthsNoOverflow(11)->startOfMonth(),
                'endDate' => $now->copy()->endOfDay(),
                'label' => '1 Tahun Terakhir',
                'chartRangeLabel' => '12 Bulan Terakhir',
            ],
            'month' => [
                'period' => 'month',
                'month' => $month,
                'year' => $year,
                'startDate' => Carbon::create($year, $month, 1)->startOfMonth(),
                'endDate' => Carbon::create($year, $month, 1)->endOfMonth(),
                'label' => self::MONTH_NAMES[$month] . ' ' . $year,
                'chartRangeLabel' => 'Harian - ' . self::MONTH_NAMES[$month] . ' ' . $year,
            ],
            'year' => [
                'period' => 'year',
                'month' => $month,
                'year' => $year,
                'startDate' => Carbon::create($year, 1, 1)->startOfYear(),
                'endDate' => Carbon::create($year, 12, 31)->endOfYear(),
                'label' => 'Tahun ' . $year,
                'chartRangeLabel' => 'Bulanan - Tahun ' . $year,
            ],
            default => [
                'period' => 'all',
                'month' => $month,
                'year' => $year,
                'startDate' => null,
                'endDate' => null,
                'label' => 'Semua Waktu',
                'chartRangeLabel' => '6 Bulan Terakhir',
            ],
        };
    }

    private function applyDateRange($query, string $column, ?Carbon $startDate, ?Carbon $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween($column, [$startDate, $endDate]);
        }

        return $query;
    }

    private function getPaidOrderItems(?Carbon $startDate, ?Carbon $endDate): Collection
    {
        return OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('payment_status', 'paid');
            $this->applyDateRange($q, 'created_at', $startDate, $endDate);
        })->with(['product', 'order'])->get();
    }

    /**
     * Build time-bucketed sales, cost, profit, orders, items sold, and new users chart series
     * using Carbon buckets so aggregation behaves identically across SQLite and MySQL.
     *
     * @param  array<string, mixed>  $filter
     */
    private function buildChartSeries(array $filter): array
    {
        $buckets = $this->createTimeBuckets($filter);
        $salesByKey = array_fill_keys(array_keys($buckets), 0);
        $costByKey = array_fill_keys(array_keys($buckets), 0);
        $profitByKey = array_fill_keys(array_keys($buckets), 0);
        $ordersByKey = array_fill_keys(array_keys($buckets), 0);
        $itemsByKey = array_fill_keys(array_keys($buckets), 0);
        $newUsersByKey = array_fill_keys(array_keys($buckets), 0);

        $chartStart = $filter['startDate'] ?? now()->copy()->subMonthsNoOverflow(5)->startOfMonth();
        $chartEnd = $filter['endDate'] ?? now()->copy()->endOfMonth();

        $orders = Order::whereBetween('created_at', [$chartStart, $chartEnd])
            ->get(['id', 'total_amount', 'payment_status', 'created_at']);

        foreach ($orders as $order) {
            $key = $this->resolveBucketKey($order->created_at, $filter['period']);
            if (!array_key_exists($key, $ordersByKey)) {
                continue;
            }
            $ordersByKey[$key]++;
            if ($order->payment_status === 'paid') {
                $salesByKey[$key] += (int) round((float) $order->total_amount);
            }
        }

        $orderItems = $this->getPaidOrderItems($chartStart, $chartEnd);
        foreach ($orderItems as $item) {
            if (!$item->order) {
                continue;
            }
            $key = $this->resolveBucketKey($item->order->created_at, $filter['period']);
            if (!array_key_exists($key, $profitByKey)) {
                continue;
            }
            $itemCost = ($item->product?->cost_price ?? 0) * $item->quantity;
            $costByKey[$key] += (int) round((float) $itemCost);
            $profitByKey[$key] += (int) round((float) ($item->subtotal - $itemCost));
            $itemsByKey[$key] += (int) $item->quantity;
        }

        $users = User::whereBetween('created_at', [$chartStart, $chartEnd])
            ->get(['id', 'created_at']);

        foreach ($users as $user) {
            $key = $this->resolveBucketKey($user->created_at, $filter['period']);
            if (array_key_exists($key, $newUsersByKey)) {
                $newUsersByKey[$key]++;
            }
        }

        $labels = array_values($buckets);

        return [
            'sales' => [
                'labels' => $labels,
                'data' => array_values($salesByKey),
            ],
            'profit' => [
                'labels' => $labels,
                'data' => array_values($profitByKey),
            ],
            'combined' => [
                'labels' => $labels,
                'sales' => array_values($salesByKey),
                'cost' => array_values($costByKey),
                'profit' => array_values($profitByKey),
                'orders' => array_values($ordersByKey),
                'items' => array_values($itemsByKey),
                'newUsers' => array_values($newUsersByKey),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filter
     * @return array<string, string>
     */
    private function createTimeBuckets(array $filter): array
    {
        $buckets = [];
        $period = $filter['period'];

        if ($period === 'today') {
            for ($hour = 0; $hour < 24; $hour++) {
                $key = sprintf('%02d:00', $hour);
                $buckets[$key] = $key;
            }

            return $buckets;
        }

        if ($period === '7_days' || $period === 'month') {
            $cursor = $filter['startDate']->copy()->startOfDay();
            $end = $filter['endDate']->copy()->startOfDay();

            while ($cursor->lte($end)) {
                $buckets[$cursor->format('Y-m-d')] = $cursor->format('d M');
                $cursor->addDay();
            }

            return $buckets;
        }

        if ($period === '3_months') {
            $cursor = $filter['startDate']->copy()->startOfWeek();
            $end = $filter['endDate']->copy()->startOfWeek();

            while ($cursor->lte($end)) {
                $buckets[$cursor->format('Y-m-d')] = $cursor->format('d M');
                $cursor->addWeek();
            }

            return $buckets;
        }

        if ($period === '1_year' || $period === 'year') {
            $cursor = $filter['startDate']->copy()->startOfMonth();
            $end = $filter['endDate']->copy()->startOfMonth();

            while ($cursor->lte($end)) {
                $buckets[$cursor->format('Y-m')] = $period === 'year'
                    ? $cursor->format('M')
                    : $cursor->format('M Y');
                $cursor->addMonthNoOverflow();
            }

            return $buckets;
        }

        // Default 'all': last 6 months chart
        $cursor = now()->copy()->subMonthsNoOverflow(5)->startOfMonth();
        $end = now()->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $buckets[$cursor->format('Y-m')] = $cursor->format('M Y');
            $cursor->addMonthNoOverflow();
        }

        return $buckets;
    }

    private function resolveBucketKey(Carbon $timestamp, string $period): string
    {
        return match ($period) {
            'today' => $timestamp->format('H:00'),
            '7_days', 'month' => $timestamp->format('Y-m-d'),
            '3_months' => $timestamp->copy()->startOfWeek()->format('Y-m-d'),
            default => $timestamp->format('Y-m'),
        };
    }

    private function getProductSalesData(Collection $paidOrderItems): array
    {
        $totals = [];
        $names = [];

        foreach ($paidOrderItems as $item) {
            if (!$item->product_id) {
                continue;
            }
            $productId = $item->product_id;
            $totals[$productId] = ($totals[$productId] ?? 0) + (int) $item->quantity;
            $names[$productId] = $item->product?->name ?? ('Produk #' . $productId);
        }

        arsort($totals);
        $topTotals = array_slice($totals, 0, 5, true);

        $labels = [];
        $data = [];
        foreach ($topTotals as $productId => $qty) {
            $labels[] = $names[$productId];
            $data[] = $qty;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * @return list<int>
     */
    private function getAvailableYears(int $selectedYear): array
    {
        $currentYear = now()->year;
        $minOrderDate = Order::min('created_at');
        $maxOrderDate = Order::max('created_at');

        $minYear = min(
            $currentYear - 4,
            $selectedYear,
            $minOrderDate ? Carbon::parse($minOrderDate)->year : $currentYear
        );
        $maxYear = max(
            $currentYear,
            $selectedYear,
            $maxOrderDate ? Carbon::parse($maxOrderDate)->year : $currentYear
        );

        return range($maxYear, $minYear);
    }
}

