<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Log;



class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = 0;
        $preorderItems = collect();
        $regularItems = collect();
        $cartItems = $cart->items()->with('product.primaryImage', 'size')->get();
        foreach ($cartItems as $item) {
            $subtotal += $item->product->price * $item->quantity;
            if ($item->is_preorder) {
                $preorderItems[] = $item;
            } else {
                $regularItems[] = $item;
            }
        }

        $shippingData = session()->get('checkout', []);
        $shippingCost = $shippingData['shipping_cost'] ?? 0;
        $discount = session('applied_discount.discount_amount', 0);
        $total = $subtotal + $shippingCost - $discount;

        // Get store settings from cache
        $storeInfo = [
            'name' => cache('setting_store_name', config('app.name')),
            'email' => cache('setting_store_email', config('app.name') . '@store.com'),
            'phone' => cache('setting_store_phone', ''),
            'description' => cache('setting_store_description', ''),
        ];

        // Get user profile data
        $user = Auth::user();

        return view('checkout.index', compact('cart', 'cartItems', 'shippingData', 
        'subtotal', 'discount', 'total', 'shippingCost', 'preorderItems', 
        'regularItems', 'storeInfo', 'user'));
    }

    public function process(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'nullable|string',
            'shipping_address' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|in:midtrans,bank_transfer,gopay,qris',
            'postal_code' => 'nullable|string',
            'province' => 'nullable|string',
            'city' => 'nullable|string',           
            'shipping_courier' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric',
            'preorder_payment_type' => 'nullable|in:full,dp',
        ]);

        $user = Auth::user();
        $cart = $user->cart;
    
        // 2. Ambil data shipping dari session (diisi oleh Livewire)
        $shippingData = session()->get('checkout', []);

        // PRIORITAS: Ambil alamat dari session (hasil input Livewire),
        // jika kosong baru ambil dari request.
        $finalCity = $shippingData['city'] ?? $request->city;
        $finalProvince = $shippingData['province'] ?? $request->province;
        $finalPostal = $shippingData['postal_code'] ?? $request->postal_code;

        // 3. Update info user (Menyimpan alamat terakhir yang digunakan ke profile)
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->shipping_address,
            'city' => $shippingData['city'] ?? null,
            'province' => $shippingData['province'] ?? null,
            'postal_code' => $shippingData['postal_code'] ?? null,
        ];

        if ($request->name) {
            $updateData['name'] = $request->name;
        }

        $user->update($updateData);

        // 4. Hitung Subtotal
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += $item->product->price * $item->quantity;
        }

        $shippingCost = $shippingData['shipping_cost'] ?? 0;
        // $totalAmount = $subtotal + $shippingCost;
        $totalAmount = $subtotal;
        $orderNumber = 'JJY-' . date('Ymd') . '-' . strtoupper(uniqid());
        // 5. Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'payment_attempt_id' => $orderNumber,
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => $request->payment_method,
            'shipping_courier' => $shippingData['courier'] ?? null,
            'shipping_address' => $request->shipping_address,
            'province' => $shippingData['province'] ?? null,
            'city' => $shippingData['city'] ?? null,
            'postal_code' => $shippingData['postal_code'] ?? null,
        ]);

        // 6. Create Order Items & Manage Stock
        foreach ($cart->items as $item) {
            $isPreorder = $item->is_preorder;
            $preorderPaymentType = null;
            $downPaymentAmount = null;
            $remainingPayment = null;

            if ($isPreorder && $request->preorder_payment_type == 'dp') {
                $preorderPaymentType = 'dp';
                $downPaymentAmount = ($item->product->price * $item->quantity) * 0.5;
                $remainingPayment = ($item->product->price * $item->quantity) * 0.5;
            } else {
                $preorderPaymentType = 'full';
                $downPaymentAmount = $item->product->price * $item->quantity;
                $remainingPayment = 0;
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_size_id' => $item->product_size_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'is_preorder' => $isPreorder,
                'preorder_payment_type' => $preorderPaymentType,
                'down_payment_amount' => $downPaymentAmount,
                'remaining_payment' => $remainingPayment,
            ]);

        }
        try {
            $order->reserveStock();
        } catch (\RuntimeException $exception) {
            $order->delete();

            return redirect()->route('checkout')->with('error', $exception->getMessage());
        }

        // 7. Bersihkan Cart & Session
        $cart->items()->delete();
        session()->forget('checkout');

        // 8. Pembayaran Midtrans
        // 🔥 PROSES PEMBAYARAN DENGAN MIDTRANS
        if (in_array($request->payment_method, ['midtrans', 'bank_transfer', 'gopay', 'qris'], true)) {
            $midtrans = new MidtransService();
            
            // Prepare items for Midtrans
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
            
            // Tambahkan shipping sebagai item
            if ($order->shipping_cost > 0) {
                $items[] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
            }

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            // 🔥 Dapatkan Snap Token dari Midtrans
            $paymentOrder = clone $order;
            $paymentOrder->order_number = $order->payment_attempt_id;
            $snapToken = $midtrans->createTransaction($paymentOrder, $items, $customerDetails, $order->payment_method);

            if ($snapToken) {
                // 🔥 Arahkan ke halaman payment dengan Snap Token
                return view('payment.index', [
                    'snapToken' => $snapToken,
                    'order' => $order,
                    'paymentAttemptId' => $order->payment_attempt_id,
                    'canChangePaymentMethod' => true,
                ]);
            } else {
                // Jika Midtrans gagal, tetap simpan order
                Log::error('Midtrans transaction failed for order: ' . $order->order_number);
                return redirect()->route('dashboard')
                    ->with('warning', 'Pesanan berhasil dibuat, namun pembayaran gagal diproses. Silakan coba lagi.');
            }
        }

        return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }

    public function callback(Request $request, MidtransService $midtrans)
    {
         Log::info('Midtrans Callback Received:', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw' => file_get_contents('php://input')
        ]);
        try {
            $result = $midtrans->handleNotification();

            if (!$result) {
                \Log::error('Invalid Midtrans notification received');
                return response()->json(['message' => 'Invalid notification'], 400);
            }

            $order = Order::where('payment_attempt_id', $result['order_id'])->first()
                ?? Order::where('order_number', $result['order_id'])->first();

            if (!$order && preg_match('/^(.*)-(?:PAY|RETRY|LUNAS)-[A-Za-z0-9]+$/', $result['order_id'], $matches)) {
                $order = Order::where('order_number', $matches[1])->first();
            }

            if (!$order) {
                \Log::error('Order not found for notification', ['order_id' => $result['order_id']]);
                return response()->json(['message' => 'Order not found'], 404);
            }

             Log::info('Order found for callback:', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'current_status' => $order->status,
                'current_payment_status' => $order->payment_status,
            ]);

            // Verify signature for security
            $isValid = $midtrans->verifySignature(
                $result['order_id'],
                $result['status_code'],
                $result['gross_amount'],
                $result['signature_key'] ?? ''
            );

            if (!$isValid) {
                \Log::error('Invalid signature for order', ['order_id' => $result['order_id']]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            if ($order->payment_attempt_id && $order->payment_attempt_id !== $result['order_id']) {
                Log::info('Ignoring notification for a replaced payment attempt', [
                    'order_id' => $order->id,
                    'notification_attempt_id' => $result['order_id'],
                    'active_attempt_id' => $order->payment_attempt_id,
                ]);

                return response()->json(['message' => 'OK']);
            }


            $transactionStatus = $result['transaction_status'];
            $fraudStatus = $result['fraud_status'];

            Log::info('Processing transaction status:', [
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);
            // Update order status based on transaction
            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                $order->reserveStock();
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
                Log::info('Order marked as PAID', ['order_id' => $order->id]);

                \Log::info('Order paid successfully', ['order_id' => $order->id]);
            } elseif ($transactionStatus == 'pending') {
                $order->reserveStock();
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                \Log::info('Order pending payment', ['order_id' => $order->id]);
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                if ($order->payment_status !== 'paid') {
                    $order->releaseStock('Midtrans payment '.$transactionStatus);
                }
                $order->update([
                    'payment_status' => 'failed',
                ]);
                \Log::info('Payment attempt failed or was cancelled', ['order_id' => $order->id]);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            \Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing callback'], 500);
        }
    }

    public function validateDiscount(Request $request)
    {
        $code = $request->input('code');

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Kode diskon tidak boleh kosong'
            ]);
        }

        $promoCode = PromoCode::where('code', strtoupper($code))->first();

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Kode diskon tidak valid'
            ]);
        }

        if (!$promoCode->isValid()) {
            if ($promoCode->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode diskon sudah kadaluarsa'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Kode diskon tidak dapat digunakan'
            ]);
        }

        $discount = $promoCode->discount;

        // Calculate discount amount
        $subtotal = 0;
        $cart = Auth::user()->cart;
        if ($cart) {
            foreach ($cart->items as $item) {
                $subtotal += $item->product->price * $item->quantity;
            }
        }

        // Check minimum purchase
        if ($discount->minimum_purchase && $subtotal < $discount->minimum_purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Belanja minimum Rp ' . number_format($discount->minimum_purchase, 0, ',', '.') . ' diperlukan'
            ]);
        }

        $discountAmount = 0;
        if ($discount->discount_type === 'percentage') {
            $discountAmount = ($subtotal * $discount->discount_value) / 100;
        } elseif ($discount->discount_type === 'fixed') {
            $discountAmount = $discount->discount_value;
        }

        // Apply maximum discount limit if exists
        if ($discount->maximum_discount && $discountAmount > $discount->maximum_discount) {
            $discountAmount = $discount->maximum_discount;
        }

        // Save discount to session
        session()->put('applied_discount', [
            'promo_code_id' => $promoCode->id,
            'code' => $code,
            'discount_amount' => $discountAmount,
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
        ]);

        // Increment used count
        $promoCode->increment('used_count');

        return response()->json([
            'success' => true,
            'message' => 'Kode diskon berhasil diterapkan',
            'discount' => (int)$discountAmount,
            'total' => $subtotal - $discountAmount
        ]);
    }
}
