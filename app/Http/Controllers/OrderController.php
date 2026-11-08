<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // 🔥 Jika user kembali dari payment dengan status success,
        // cek status transaksi Midtrans secara manual (fallback webhook)
        if ($request->has('payment') && $request->payment == 'success') {
            $this->syncPendingPayments();
        }

        $orders = Auth::user()->orders()
            ->with('items.product.primaryImage', 'items.size')
            ->latest()
            ->paginate(10);
        return view('dashboard.orders', compact('orders'));
    }

    /**
     * 🔥 Sinkronisasi status pembayaran dengan Midtrans
     * Cek semua order yang masih unpaid/pending dan update statusnya
     * berdasarkan status transaksi di Midtrans
     */
    private function syncPendingPayments()
    {
        try {
            $orders = Auth::user()->orders()
                ->whereIn('payment_status', ['unpaid', 'pending'])
                ->where('status', '!=', 'cancelled')
                ->get();

            if ($orders->isEmpty()) {
                return;
            }

            $midtrans = new MidtransService();

            foreach ($orders as $order) {
                // Cek status transaksi di Midtrans
                $transaction = $midtrans->checkTransactionStatus($order->order_number);

                if (!$transaction) {
                    Log::warning('Could not check transaction status', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                    ]);
                    continue;
                }

                $transactionStatus = $transaction['transaction_status'] ?? null;
                $fraudStatus = $transaction['fraud_status'] ?? null;

                Log::info('Syncing payment status', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_status' => $transactionStatus,
                    'fraud_status' => $fraudStatus,
                ]);

                // Update order berdasarkan status transaksi
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    if ($fraudStatus == 'accept' || $fraudStatus == null) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);
                        Log::info('Order marked as PAID via manual sync', ['order_id' => $order->id]);
                    }
                } elseif ($transactionStatus == 'pending') {
                    $order->update([
                        'payment_status' => 'pending',
                        'status' => 'pending',
                    ]);
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'failed',
                    ]);
                    Log::info('Order cancelled via manual sync', ['order_id' => $order->id]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Payment sync error: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id != Auth::user()->id && !Auth::user()->email == 'admin@jjhouse.com') {
            abort(403);
        }
        $order->load('items.product.primaryImage', 'items.size');
        return view('dashboard.order-detail', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($order->status == 'pending' || $order->payment_status == 'unpaid') {
            $order->update([
                'status' => 'cancelled', 
                'payment_status' => 'failed'
            ]);
            
            Log::info('Order cancelled by user', ['order_id' => $order->id, 'user_id' => auth()->id()]);
            
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }

     public function retryPayment(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }
        
        // Cek apakah order masih bisa dibayar
        if ($order->payment_status == 'paid') {
            return redirect()->back()->with('error', 'Pesanan ini sudah lunas.');
        }
        
        if ($order->status == 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan ini sudah dibatalkan.');
        }
        
        try {
            $midtrans = new MidtransService();
            
            // Prepare items
            $items = [];
            foreach ($order->items as $item) {
                $price = $item->preorder_payment_type == 'dp' 
                    ? $item->down_payment_amount / $item->quantity 
                    : $item->price;
                
                $items[] = [
                    'id' => (string) $item->product_id,
                    'price' => (int) $price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            }
            
            // Add shipping
            if ($order->shipping_cost > 0) {
                $items[] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
            }
            
            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ];
            
            // Generate new order number for retry (with suffix)
            $retryOrder = clone $order;
            $retryOrder->order_number = $order->order_number . '-RETRY-' . date('YmdHis');
            
            $snapToken = $midtrans->createTransaction($retryOrder, $items, $customerDetails);
            
            if ($snapToken) {
                Log::info('Retry payment initiated', ['order_id' => $order->id]);
                return view('payment.index', compact('snapToken', 'order'));
            } else {
                Log::error('Retry payment failed', ['order_id' => $order->id]);
                return redirect()->back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
            }
            
        } catch (\Exception $e) {
            Log::error('Retry payment exception: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function completePayment(Request $request, Order $order, OrderItem $orderItem)
    {
        if ($order->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($orderItem->order_id != $order->id || $orderItem->remaining_payment <= 0) {
            abort(400);
        }

        // Create Midtrans payment for remaining amount
         try {
            $midtrans = new MidtransService();
            
            $items = [[
                'id' => (string) $orderItem->product_id,
                'price' => (int) $orderItem->remaining_payment,
                'quantity' => 1,
                'name' => 'Pelunasan Pre-order - ' . $orderItem->product->name,
            ]];
            
            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ];
            
            $tempOrder = clone $order;
            $tempOrder->order_number = $order->order_number . '-LUNAS-' . $orderItem->id;
            $tempOrder->total_amount = $orderItem->remaining_payment;
            
            $snapToken = $midtrans->createTransaction($tempOrder, $items, $customerDetails);
            
            if ($snapToken) {
                return view('payment.index', compact('snapToken', 'order'));
            }
            
            return redirect()->back()->with('error', 'Gagal memproses pembayaran.');
            
        } catch (\Exception $e) {
            Log::error('Complete payment exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function invoice(Order $order)
    {
        if ($order->user_id != Auth::user()->id && !Auth::user()->email == 'admin@jjhouse.com') {
            abort(403);
        }

        $order->load('items.product', 'user');
        return view('dashboard.invoice', compact('order'));
    }
}
