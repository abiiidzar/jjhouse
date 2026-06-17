<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the karyawan dashboard.
     */
    public function index()
    {
        // Total stock
        $totalStock = Product::sum('stock');

        // Order counts by status
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $readyToShipOrders = Order::where('status', 'ready_to_ship')->count();

        // Low stock products (less than 5 units)
        $lowStockProducts = Product::where('stock', '<', 5)
            ->where('stock', '>', 0)
            ->with('category')
            ->orderBy('stock', 'asc')
            ->get();
        
        $lowStockCount = $lowStockProducts->count();

        // Pending orders list for display
        $pendingOrdersList = Order::whereIn('status', ['pending', 'processing'])
            ->with('user', 'items')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return view('karyawan.dashboard', compact(
            'totalStock',
            'pendingOrders',
            'processingOrders',
            'readyToShipOrders',
            'lowStockProducts',
            'lowStockCount',
            'pendingOrdersList'
        ));
    }
}
