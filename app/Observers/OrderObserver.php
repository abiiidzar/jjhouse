<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * Kirim email ketika order status berubah
     */
    public function updated(Order $order): void
    {
        // Cek apakah status berubah
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Jangan kirim email jika status = pending (itu dari create event payment)
            if ($newStatus !== 'pending' && $oldStatus !== $newStatus) {
                try {
                    Mail::to($order->user->email)
                        ->queue(new OrderStatusMail($order));
                } catch (\Exception $e) {
                    \Log::error('Failed to send order status email', [
                        'order_id' => $order->id,
                        'status' => $newStatus,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
