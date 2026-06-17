<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockHistory;
use App\Models\Product;
use Illuminate\Http\Request;

class StockHistoryController extends Controller
{
    /**
     * Display stock history logs
     */
    public function index(Request $request)
    {
        $product_id = $request->query('product_id');
        $action = $request->query('action');
        $search = $request->query('search');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        $query = StockHistory::with(['productSize.product', 'user']);

        // Filter by product
        if ($product_id) {
            $query->whereHas('productSize', function ($q) use ($product_id) {
            $q->where('product_id', $product_id);
                    });
        }

        // Filter by action
        if ($action) {
            $query->where('action', $action);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('productSize.product', function ($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }

        $histories = $query->recent()->paginate(30);
        $products = Product::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total' => StockHistory::count(),
            'today' => StockHistory::whereDate('created_at', today())->count(),
            'this_week' => StockHistory::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.stock-history.index', compact('histories', 'products', 'stats', 'product_id', 'action', 'search', 'from_date', 'to_date'));
    }

    /**
     * View product-specific history
     */
    public function product(Product $product)
    {
        $histories = StockHistory::with(['productSize.product', 'user'])
            ->whereHas('productSize', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->latest()
            ->paginate(30);

        return view('admin.stock-history.product', compact('histories', 'product'));
    }

    /**
     * Export stock history to CSV
     */
    public function export(Request $request)
    {
        $product_id = $request->query('product_id');
        $action = $request->query('action');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');

        $query = StockHistory::with(['productSize.product', 'user']);

        if ($product_id) {
            $query->whereHas('productSize', function ($q) use ($product_id) {
                $q->where('product_id', $product_id);
            });
        }
        if ($action) $query->where('action', $action);
        if ($from_date) $query->whereDate('created_at', '>=', $from_date);
        if ($to_date) $query->whereDate('created_at', '<=', $to_date);

        $histories = $query->recent()->get();

        $filename = 'stock-history-' . now()->format('Y-m-d-His') . '.csv';

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        // CSV headers
        fputcsv($handle, [
            'Date', 'Time', 'Product', 'Size', 'Action', 'Before', 'After', 'Changed', 'By', 'Notes'
        ]);

        // CSV data
        foreach ($histories as $history) {
            fputcsv($handle, [
                $history->created_at->format('Y-m-d'),
                $history->created_at->format('H:i:s'),
                $history->product->name ?? '-',
                $history->productSize->size ?? '-',
                $history->action,
                $history->quantity_before,
                $history->quantity_after,
                $history->quantity_changed > 0 ? '+' . $history->quantity_changed : $history->quantity_changed,
                $history->user->name ?? '-',
                $history->notes ?? '-',
            ]);
        }

        fclose($handle);
        exit;
    }
}
