<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $search = $request->query('search');

        $query = StockAlert::with(['product', 'productSize', 'acknowledgedBy']);

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search
        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $alerts = $query->recent()->paginate(20);

        $stats = [
            'active' => StockAlert::active()->count(),
            'acknowledged' => StockAlert::acknowledged()->count(),
            'resolved' => StockAlert::resolved()->count(),
            'total' => StockAlert::count(),
        ];

        return view('admin.stock-alerts.index', compact('alerts', 'status', 'search', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAlert $stockAlert)
    {
        $stockAlert->load(['product', 'productSize', 'acknowledgedBy']);

        // Get stock history for this product size
        $history = $stockAlert->productSize
            ->stockHistories()
            ->recent()
            ->paginate(15);

        return view('admin.stock-alerts.show', compact('stockAlert', 'history'));
    }

    /**
     * Acknowledge the alert
     */
    public function acknowledge(Request $request, StockAlert $stockAlert)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $stockAlert->acknowledge(auth()->user(), $validated['notes'] ?? null);

        return redirect()->back()->with('success', 'Alert acknowledged successfully.');
    }

    /**
     * Resolve the alert (mark stock issue as resolved)
     */
    public function resolve(Request $request, StockAlert $stockAlert)
    {
        $validated = $request->validate([
            'restock_quantity' => 'nullable|integer|min:0',
        ]);

        $stockAlert->resolve($validated['restock_quantity'] ?? null);

        return redirect()->back()->with('success', 'Alert resolved successfully.');
    }

    /**
     * Bulk acknowledge alerts
     */
    public function bulkAcknowledge(Request $request)
    {
        $ids = $request->input('ids', []);
        $notes = $request->input('notes');

        StockAlert::whereIn('id', $ids)
            ->active()
            ->each(function ($alert) use ($notes) {
                $alert->acknowledge(auth()->user(), $notes);
            });

        return redirect()->back()->with('success', count($ids) . ' alerts acknowledged.');
    }

    /**
     * Manually check all products and create/update alerts.
     */
    public function checkAllProducts()
    {
        $productSizes = ProductSize::all();
        $observer = new \App\Observers\StockAlertObserver();

        foreach ($productSizes as $productSize) {
            $observer->saved($productSize);
        }

        return redirect()->route('admin.stock-alerts.index')->with('success', 'Stock levels for all products have been checked.');
    }

    /**
     * Bulk resolve alerts
     */
    public function bulkResolve(Request $request)
    {
        $ids = $request->input('ids', []);

        StockAlert::whereIn('id', $ids)
            ->active()
            ->each(function ($alert) {
                $alert->resolve();
            });

        return redirect()->back()->with('success', count($ids) . ' alerts resolved.');
    }
}
