<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\User;
use App\Mail\PreOrderReadyMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PreOrderService
{
    public function calculateEstimatedDate($estimatedDays)
    {
        if (!$estimatedDays) {
            return null;
        }
        return Carbon::now()->addDays($estimatedDays);
    }

    public function formatEstimatedDate($estimatedDays)
    {
        $date = $this->calculateEstimatedDate($estimatedDays);
        return $date ? $date->format('d F Y') : 'TBA';
    }

    public function sendReadyNotification(OrderItem $orderItem)
    {
        try {
            $user = $orderItem->order->user;
            $product = $orderItem->product;

            Mail::to($user->email)->send(new PreOrderReadyMail($orderItem, $user));

            Log::info('Pre-order notification sent', [
                'user_id' => $user->id,
                'order_item_id' => $orderItem->id,
                'product_name' => $product->name,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send pre-order notification: ' . $e->getMessage());
            return false;
        }
    }

    public function markAsReady(OrderItem $orderItem)
    {
        if ($orderItem->preorder_ready_at) {
            return false;
        }

        $orderItem->update([
            'preorder_ready_at' => Carbon::now(),
        ]);

        $this->sendReadyNotification($orderItem);

        return true;
    }

    public function bulkMarkAsReady($orderItemIds)
    {
        $count = 0;
        foreach ($orderItemIds as $id) {
            $orderItem = OrderItem::find($id);
            if ($orderItem && $this->markAsReady($orderItem)) {
                $count++;
            }
        }
        return $count;
    }

    public function getActivePreOrders()
    {
        return OrderItem::preorder()
            ->whereNull('preorder_ready_at')
            ->with(['order.user', 'product'])
            ->get();
    }

    public function getReadyToShipPreOrders()
    {
        return OrderItem::preorder()
            ->whereNotNull('preorder_ready_at')
            ->whereNull('preorder_shipped_at') // if you have this column
            ->with(['order.user', 'product'])
            ->get();
    }

    public function getPreOrderStatistics()
    {
        $totalPreorders = OrderItem::preorder()->count();
        $totalDP = OrderItem::preorder()
            ->where('preorder_payment_type', 'dp')
            ->sum('down_payment_amount');
        $totalRemaining = OrderItem::preorder()->sum('remaining_payment');
        $readyToShip = OrderItem::preorder()
            ->whereNotNull('preorder_ready_at')
            ->count();
        $waitingForPayment = OrderItem::preorder()
            ->where('preorder_payment_type', 'dp')
            ->whereRaw('remaining_payment > 0')
            ->count();

        return [
            'total' => $totalPreorders,
            'total_dp' => $totalDP,
            'total_remaining' => $totalRemaining,
            'ready_to_ship' => $readyToShip,
            'waiting_payment' => $waitingForPayment,
        ];
    }

    public function isPreorderActive($product)
    {
        if (!$product->is_preorder) {
            return false;
        }

        if (!$product->preorder_end_date) {
            return true;
        }

        return Carbon::now()->lessThan($product->preorder_end_date);
    }
}
