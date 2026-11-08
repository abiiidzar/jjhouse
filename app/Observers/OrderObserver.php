<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\OrderNotificationService;
use Illuminate\Support\Facades\DB;

class OrderObserver
{
    /**
     * Handle a committed order update.
     *
     * Eloquent's original values are only available while the updated event is
     * being handled. Capture the changes now, then defer notification delivery
     * until the surrounding database transaction has committed.
     */
    public function updated(Order $order): void
    {
        $changes = $order->getChanges();
        $shippingFields = [
            'tracking_number',
            'shipping_courier',
            'shipping_service',
            'shipping_note',
        ];

        $statusChanged = array_key_exists('status', $changes);
        $paymentStatusChanged = array_key_exists('payment_status', $changes);
        $shippingDetailsChanged = collect($shippingFields)
            ->contains(fn (string $field): bool => array_key_exists($field, $changes));

        if (! $statusChanged && ! $paymentStatusChanged && ! $shippingDetailsChanged) {
            return;
        }

        $snapshot = [
            'order_id' => (int) $order->getKey(),
            'user_id' => (int) $order->user_id,
            'order_number' => (string) $order->order_number,
            'customer_name' => (string) ($order->user?->name ?? 'Pelanggan'),
            'total_amount' => $order->grand_total,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'previous_payment_status' => $paymentStatusChanged ? $order->getOriginal('payment_status') : null,
            'payment_status_changed' => $paymentStatusChanged,
            'status' => $order->status,
            'previous_status' => $statusChanged ? $order->getOriginal('status') : null,
            'status_changed' => $statusChanged,
            'tracking_number' => $order->tracking_number,
            'shipping_courier' => $order->shipping_courier,
            'shipping_service' => $order->shipping_service,
            'shipping_note' => $order->shipping_note,
            'shipping_details_changed' => $shippingDetailsChanged,
        ];

        DB::afterCommit(function () use ($snapshot): void {
            app(OrderNotificationService::class)->handleCommittedOrderUpdate($snapshot);
        });
    }
}
