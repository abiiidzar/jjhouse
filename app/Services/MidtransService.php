<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MidtransService
{
    public function __construct()
    {
        // Try to get from cache settings first, then fallback to config
        $serverKey = Cache::get('payment_midtrans_server_key') ?? config('services.midtrans.server_key');
        $clientKey = Cache::get('payment_midtrans_client_key') ?? config('services.midtrans.client_key');
        $environment = Cache::get('payment_midtrans_environment') ?? config('services.midtrans.environment', 'sandbox');
        
        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = ($environment === 'production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction($order, $items, $customerDetails)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) ($order->total_amount + ($order->shipping_cost ?? 0)),
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $customerDetails['first_name'] ?? 'Customer',
                    'email' => $customerDetails['email'] ?? '',
                    'phone' => $customerDetails['phone'] ?? '',
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bank_transfer',
                    'gopay',
                    'shopeepay',
                    'qris',
                ],
                'callbacks' => [
                    'finish' => route('payment.callback'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Create Transaction Error: ' . $e->getMessage());
            return null;
        }
    }

    public function createPaymentLink($order, $items, $customerDetails)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) ($order->total_amount + ($order->shipping_cost ?? 0)),
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $customerDetails['first_name'] ?? 'Customer',
                    'email' => $customerDetails['email'] ?? '',
                    'phone' => $customerDetails['phone'] ?? '',
                ],
            ];

            $paymentLink = Snap::createTransaction($params);
            return $paymentLink->redirect_url;
        } catch (\Exception $e) {
            Log::error('Midtrans Payment Link Error: ' . $e->getMessage());
            return null;
        }
    }

    public function handleNotification()
    {
        try {
            $notification = new Notification();

            return [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'fraud_status' => $notification->fraud_status,
                'payment_type' => $notification->payment_type,
                'gross_amount' => $notification->gross_amount,
                'transaction_time' => $notification->transaction_time,
                'status_code' => $notification->status_code,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Handle Notification Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getStatus($orderId)
    {
        try {
            $status = Snap::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error('Midtrans Get Status Error: ' . $e->getMessage());
            return null;
        }
    }

    public function cancelTransaction($orderId)
    {
        try {
            $cancel = Snap::cancel($orderId);
            return $cancel;
        } catch (\Exception $e) {
            Log::error('Midtrans Cancel Error: ' . $e->getMessage());
            return null;
        }
    }
}
