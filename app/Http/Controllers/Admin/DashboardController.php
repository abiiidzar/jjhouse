<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic statistics
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalPreorderActive = OrderItem::where('is_preorder', true)
            ->whereNull('preorder_ready_at')
            ->count();

        // Sales calculations
        $paidOrders = Order::where('payment_status', 'paid');
        $totalSales = $paidOrders->sum('total_amount');
        $monthlySales = $paidOrders->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Cost and Profit calculations
        $totalCost = 0;
        $totalProfit = 0;
        $monthlyCost = 0;
        $monthlyProfit = 0;

        // Calculate total cost and profit from order items
        $orderItems = OrderItem::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->with('product')->get();

        foreach ($orderItems as $item) {
            $itemCost = ($item->product?->cost_price ?? 0) * $item->quantity;
            $itemProfit = $item->subtotal - $itemCost;
            $totalCost += $itemCost;
            $totalProfit += $itemProfit;

            // Check if order is from this month
            if ($item->order->created_at->month == now()->month) {
                $monthlyCost += $itemCost;
                $monthlyProfit += $itemProfit;
            }
        }

        // Get recent orders
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Monthly sales data for chart (last 6 months)
        $monthlySalesData = $this->getMonthlySalesData();
        $monthlyProfitData = $this->getMonthlyProfitData();
        $productSalesData = $this->getProductSalesData();

        // Featured products
        $featuredProducts = Product::where('is_featured', true)->take(5)->get();
        $featuredHomeProducts = Product::where('is_featured_home', true)->get();

        // New users this month
        $newUsers = User::whereMonth('created_at', now()->month)->count();

        // Orders this month
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)->count();

        return view('admin.index', compact(
            'totalSales',
            'totalCost',
            'totalProfit',
            'monthlySales',
            'monthlyCost',
            'monthlyProfit',
            'totalPreorderActive',
            'totalOrders',
            'totalUsers',
            'totalProducts',
            'recentOrders',
            'monthlySalesData',
            'monthlyProfitData',
            'productSalesData',
            'featuredProducts',
            'featuredHomeProducts',
            'newUsers',
            'ordersThisMonth'
        ));
    }

    private function getMonthlySalesData()
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M');
            $sales = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
            $data[] = (int) $sales;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMonthlyProfitData()
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M');

            $profit = 0;
            $orderItems = OrderItem::whereHas('order', function ($q) use ($month) {
                $q->where('payment_status', 'paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month);
            })->with('product')->get();

            foreach ($orderItems as $item) {
                $itemCost = ($item->product?->cost_price ?? 0) * $item->quantity;
                $profit += $item->subtotal - $itemCost;
            }

            $data[] = (int) $profit;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getProductSalesData()
    {
        $data = [];
        $labels = [];

        // Get top 5 best selling products
        $products = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        foreach ($products as $product) {
            $labels[] = $product->name;
            $data[] = $product->order_items_count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}

