<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\NewPaidOrderNotification;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderNotificationService
{
    /**
     * Dispatch the notifications that apply to one committed order update.
     *
     * The observer passes a scalar snapshot so queued notifications still describe
     * the exact update that caused them, even if the order changes again later.
     *
     * @param  array<string, mixed>  $order
     */
    public function handleCommittedOrderUpdate(array $order): void
    {
        if (($order['payment_status_changed'] ?? false) && ($order['payment_status'] ?? null) === 'paid') {
            $this->notifyStaffOfPaidOrder($order);
        }

        if ($this->shouldNotifyCustomer($order)) {
            $this->notifyCustomerOfOrderUpdate($order);
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function notifyStaffOfPaidOrder(array $order): void
    {
        try {
            $staff = User::query()
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'karyawan']);
                })
                ->get();

            if ($staff->isNotEmpty()) {
                Notification::send($staff, new NewPaidOrderNotification($order));
            }
        } catch (\Throwable $exception) {
            Log::error('Unable to notify staff about a paid order.', [
                'order_id' => $order['order_id'] ?? null,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function notifyCustomerOfOrderUpdate(array $order): void
    {
        try {
            $customer = User::find($order['user_id']);

            if ($customer !== null) {
                $customer->notify(new OrderStatusChangedNotification($order));
            }
        } catch (\Throwable $exception) {
            Log::error('Unable to notify customer about an order update.', [
                'order_id' => $order['order_id'] ?? null,
                'user_id' => $order['user_id'] ?? null,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function shouldNotifyCustomer(array $order): bool
    {
        $status = $order['status'] ?? 'pending';

        if (($order['status_changed'] ?? false) && $status !== 'pending') {
            return true;
        }

        return ($order['shipping_details_changed'] ?? false) && $status !== 'pending';
    }
}
