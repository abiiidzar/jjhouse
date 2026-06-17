<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductSize;

class OrderController extends Controller
{
    /**
     * Display a listing of unpaid orders.
     */
    public function index()
{
    $orders = Order::query()
        ->where(function ($q) {
            $q->where('payment_status', '=', 'unpaid')
              ->orWhere('payment_status', '=', 'pending');
        })
        ->with('user', 'items.product')
        ->latest()
        ->paginate(15);

    return view('kasir.orders.index', compact('orders'));
}


public function history()
{
    $orders = Order::query()
        ->where(function ($q) {
            $q->where('payment_status', '=', 'unpaid')
            ->orWhere('payment_status', '=', 'pending');
        })
        ->with(['user', 'items.product'])
        ->latest()
        ->paginate(15);

    return view('kasir.history.index', compact('orders'));
}


public function processPayment(Request $request, Order $order)
{
    $validated = $request->validate([
        'payment_method' => 'required|in:transfer,cash,card',
        'payment_amount' => 'required|numeric|min:' . $order->total_amount,
    ]);

    if ($order->payment_status === 'paid') {
        return back()->with('error', 'Pesanan sudah dibayar');
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($order, $validated) {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'payment_method' => $validated['payment_method'],
            'paid_amount' => $validated['payment_amount'],
        ]);
    });

    return redirect()->route('kasir.orders.index')
        ->with('success', "Pembayaran #{$order->id} berhasil");
}
public function dashboard()
{
    $today = today();

    $unpaid = Order::where('payment_status', 'unpaid')->count();

    $todayOrders = Order::whereDate('created_at', $today)->count();

    $todayRevenue = Order::whereDate('created_at', $today)
        ->where('payment_status', 'paid')
        ->sum('total_amount');

    $paidOrders = Order::whereDate('created_at', $today)
        ->where('payment_status', 'paid')
        ->count();

    $avgTransaction = $paidOrders > 0 ? $todayRevenue / $paidOrders : 0;

    $latestUnpaid = Order::with('user')
        ->where('payment_status', 'unpaid')
        ->latest()
        ->limit(10)
        ->get();

    return view('kasir.dashboard', [
        'unpaid' => Order::where('payment_status','unpaid')->count(),
        'todayOrders' => Order::whereDate('created_at',$today)->count(),
        'todayRevenue' => Order::whereDate('created_at',$today)->where('payment_status','paid')->sum('total_amount'),

        'cashTotal' => Order::whereDate('created_at',$today)->where('payment_method','cash')->sum('total_amount'),
        'transferTotal' => Order::whereDate('created_at',$today)->where('payment_method','transfer')->sum('total_amount'),

        'latestUnpaid' => Order::with('user')->where('payment_status','unpaid')->latest()->limit(10)->get()
    ]);
}

public function show(Order $order)
{
    return view('kasir.orders.show', compact('order'));
}

public function dailyReport()
{
    $today = today();
    
    $dailyStats = [
        'totalOrders' => Order::whereDate('created_at', $today)->count(),
        'paidOrders' => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->count(),
        'unpaidOrders' => Order::whereDate('created_at', $today)->where('payment_status', 'unpaid')->count(),
        'totalRevenue' => Order::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_amount'),
        'cashRevenue' => Order::whereDate('created_at', $today)->where('payment_method', 'cash')->where('payment_status', 'paid')->sum('total_amount'),
        'transferRevenue' => Order::whereDate('created_at', $today)->where('payment_method', 'transfer')->where('payment_status', 'paid')->sum('total_amount'),
        'cardRevenue' => Order::whereDate('created_at', $today)->where('payment_method', 'card')->where('payment_status', 'paid')->sum('total_amount'),
    ];
    
    $transactions = Order::whereDate('created_at', $today)
        ->with('user')
        ->latest()
        ->get();

    return view('kasir.reports.daily', compact('dailyStats', 'transactions'));
}

public function weeklyReport()
{
    $startOfWeek = now()->startOfWeek()->toDateString();
    $endOfWeek = now()->endOfWeek()->toDateString();
    
    $weeklyData = [];
    for ($i = 0; $i < 7; $i++) {
        $date = now()->startOfWeek()->addDays($i);
        $weeklyData[] = [
            'date' => $date->toDateString(),
            'day' => $date->format('l'),
            'orders' => Order::whereDate('created_at', $date)->count(),
            'revenue' => Order::whereDate('created_at', $date)->where('payment_status', 'paid')->sum('total_amount'),
        ];
    }
    
    $totalRevenue = Order::whereBetween('created_at', [$startOfWeek . ' 00:00:00', $endOfWeek . ' 23:59:59'])
        ->where('payment_status', 'paid')
        ->sum('total_amount');

    return view('kasir.reports.weekly', compact('weeklyData', 'totalRevenue'));
}

public function cashflow()
{
    $today = today();
    
    $paymentMethods = [
        'cash' => Order::whereDate('created_at', $today)->where('payment_method', 'cash')->where('payment_status', 'paid')->sum('total_amount'),
        'transfer' => Order::whereDate('created_at', $today)->where('payment_method', 'transfer')->where('payment_status', 'paid')->sum('total_amount'),
        'card' => Order::whereDate('created_at', $today)->where('payment_method', 'card')->where('payment_status', 'paid')->sum('total_amount'),
    ];
    
    $transactionHistory = Order::whereDate('created_at', $today)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('kasir.reports.cashflow', compact('paymentMethods', 'transactionHistory'));
}

 
}
