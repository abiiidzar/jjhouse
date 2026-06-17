<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\PreOrderService;
use Illuminate\Http\Request;

class PreOrderController extends Controller
{
    protected $preOrderService;

    public function __construct(PreOrderService $preOrderService)
    {
        $this->preOrderService = $preOrderService;
    }

    public function index(Request $request)
    {
        $query = OrderItem::where('is_preorder', true)
            ->with(['order.user', 'product']);

        // Filter by status
        if ($request->status == 'waiting_dp') {
            $query->where('preorder_payment_type', 'dp')
                  ->whereNull('preorder_ready_at');
        } elseif ($request->status == 'waiting_pelunasan') {
            $query->where('preorder_payment_type', 'dp')
                  ->whereNotNull('preorder_ready_at')
                  ->whereRaw('remaining_payment > 0');
        } elseif ($request->status == 'ready_to_ship') {
            $query->whereNotNull('preorder_ready_at');
        } elseif ($request->status == 'process') {
            $query->whereNull('preorder_ready_at');
        }

        $preorderItems = $query->latest()->paginate(20);

        return view('admin.preorders.index', compact('preorderItems'));
    }

    public function markReady(OrderItem $orderItem)
    {
        if ($orderItem->preorder_ready_at) {
            return redirect()->back()->with('error', 'Pre-order already marked as ready.');
        }

        $this->preOrderService->markAsReady($orderItem);

        return redirect()->back()->with('success', 'Pre-order marked as ready and email notification sent.');
    }

    public function bulkMarkReady(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:order_items,id',
        ]);

        $count = 0;
        foreach ($request->items as $itemId) {
            $orderItem = OrderItem::find($itemId);
            if ($orderItem && !$orderItem->preorder_ready_at) {
                $this->preOrderService->markAsReady($orderItem);
                $count++;
            }
        }

        return redirect()->back()->with('success', $count . ' pre-order items marked as ready.');
    }

    public function report()
    {
        $totalPreorder = OrderItem::where('is_preorder', true)->count();
        $totalDP = OrderItem::where('is_preorder', true)
            ->where('preorder_payment_type', 'dp')
            ->sum('down_payment_amount');
        $totalRemaining = OrderItem::where('is_preorder', true)
            ->sum('remaining_payment');
        $readyToShip = OrderItem::where('is_preorder', true)
            ->whereNotNull('preorder_ready_at')
            ->count();

        $monthlyPreorder = OrderItem::where('is_preorder', true)
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('admin.preorders.report', compact(
            'totalPreorder', 'totalDP', 'totalRemaining',
            'readyToShip', 'monthlyPreorder'
        ));
    }
}
