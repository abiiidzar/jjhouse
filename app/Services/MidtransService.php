<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected $merchantId;
    protected $clientKey;
    protected $serverKey;
    protected $isProduction;

    public function __construct()
    {
        $this->merchantId = config('services.midtrans.merchant_id');
        $this->clientKey = config('services.midtrans.client_key');
        $this->serverKey = config('services.midtrans.server_key');
        $this->isProduction = config('services.midtrans.is_production', false);

        // Set Midtrans Configuration
        Config::$serverKey = $this->serverKey;
        Config::$clientKey = $this->clientKey;
        Config::$isProduction = $this->isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction($order, $items, $customerDetails)
    {
        try {
            $orderId = preg_replace('/[^a-zA-Z0-9\-_~.]/', '-', $order->order_number);
            // Log untuk debugging
            Log::info('Midtrans Transaction Started', [
                'order_id' => $orderId,
                'original_order_id' => $order->order_number,
                'merchant_id' => $this->merchantId,
                'is_production' => $this->isProduction,
                'gross_amount' => $order->grand_total,
            ]);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
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
                    'finish' => route('dashboard'),
                ],
            ];

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);

            Log::info('Midtrans Snap Token Generated', [
                'order_id' => $orderId,
                'has_token' => !empty($snapToken),
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Create Transaction Error: ' . $e->getMessage());
            \Log::error('Midtrans Error Details:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    public function handleNotification()
    {
        try {
            $rawInput = file_get_contents('php://input');
            Log::info('Midtrans Raw Notification:', ['body' => $rawInput]);
            $notification = new Notification();

            $result = [
                'order_id' => $notification->order_id,
                'transaction_status' => $notification->transaction_status,
                'fraud_status' => $notification->fraud_status,
                'payment_type' => $notification->payment_type,
                'gross_amount' => $notification->gross_amount,
                'transaction_time' => $notification->transaction_time,
                'status_code' => $notification->status_code,
                'signature_key' => $notification->signature_key ?? null,
            ];

            Log::info('Midtrans Notification Received', $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('Midtrans Handle Notification Error: ' . $e->getMessage());
            Log::error('Midtrans Notification Error Trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Verifikasi signature key untuk keamanan
     */
    public function verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)
    {
        try {
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
            Log::info('Signature Verification:', [
                'expected' => $expectedSignature,
                'received' => $signatureKey,
                'match' => $expectedSignature === $signatureKey
            ]);
            return $expectedSignature === $signatureKey;
        } catch (\Exception $e) {
            Log::error('Signature verification failed: ' . $e->getMessage());
            return false;
        }

    }

    /**
     * Cek status transaksi Midtrans secara manual
     * Digunakan sebagai fallback ketika webhook tidak sampai
     * (terutama saat aplikasi berjalan di localhost)
     */
    public function checkTransactionStatus($orderId)
    {
        try {
            $orderId = preg_replace('/[^a-zA-Z0-9\-_~.]/', '-', $orderId);
            
            Log::info('Checking Midtrans transaction status', [
                'order_id' => $orderId,
                'is_production' => $this->isProduction,
            ]);

            $apiUrl = $this->isProduction 
                ? 'https://api.midtrans.com/v2/' . $orderId . '/status'
                : 'https://api.sandbox.midtrans.com/v2/' . $orderId . '/status';

            $response = \Illuminate\Support\Facades\Http::withBasicAuth($this->serverKey, '')
                ->timeout(15)
                ->get($apiUrl);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Midtrans status check response', [
                    'order_id' => $orderId,
                    'status' => $result['transaction_status'] ?? 'unknown',
                    'fraud_status' => $result['fraud_status'] ?? null,
                ]);
                return $result;
            }

            Log::error('Midtrans status check failed', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Midtrans status check exception: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
            return null;
        }
    }
}
