<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSize;
use App\Models\PromoCode;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;


class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart;
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = 0;
        $preorderItems = [];
        $regularItems = [];

        foreach ($cart->items as $item) {
            $subtotal += $item->product->price * $item->quantity;
            if ($item->is_preorder) {
                $preorderItems[] = $item;
            } else {
                $regularItems[] = $item;
            }
        }

        $shippingData = session()->get('checkout');
        $shippingCost = $shippingData['shipping_cost'] ?? 0;
        $discount = session('applied_discount.discount_amount', 0);
        $total = $subtotal + $shippingCost - $discount;
        $cartItems = $cart->items;

        // Get store settings from cache
        $storeInfo = [
            'name' => cache('setting_store_name', config('app.name')),
            'email' => cache('setting_store_email', config('app.name') . '@store.com'),
            'phone' => cache('setting_store_phone', ''),
            'description' => cache('setting_store_description', ''),
        ];

        // Get user profile data
        $user = Auth::user();

        return view('checkout.index', compact('cart', 'cartItems', 'shippingData', 'subtotal', 'discount', 'total', 'shippingCost', 'preorderItems', 'regularItems', 'storeInfo', 'user'));
    }

    public function process(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'nullable|string',
            'shipping_address' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|in:midtrans,bank_transfer',
            'postal_code' => 'nullable|string',
            'province' => 'nullable|string',
            'city' => 'nullable|string',
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
            'phone' => $request->phone,
            'address' => $request->shipping_address,
            'city' => $finalCity,
            'province' => $finalProvince,
            'postal_code' => $finalPostal,
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
        $totalAmount = $subtotal + $shippingCost;

        // 5. Create Order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => $request->payment_method,
            'shipping_courier' => ($shippingData['courier'] ?? null) . ' ' . ($shippingData['service'] ?? ''),
            'shipping_address' => $request->shipping_address,
            'province' => $finalProvince,
            'city' => $finalCity,
            'postal_code' => $finalPostal,
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

            // Kurangi stok jika bukan preorder
            if (!$isPreorder && $item->product_size_id) {
                $productSize = ProductSize::find($item->product_size_id);
                if ($productSize) {
                    $productSize->decreaseStock($item->quantity);
                }
            }
        }

        // 7. Bersihkan Cart & Session
        $cart->items()->delete();
        session()->forget('checkout');

        // 8. Pembayaran Midtrans
        if ($request->payment_method == 'midtrans') {
            $midtrans = new MidtransService();
            $items = [];

            foreach ($order->items as $item) {
                $price = $item->preorder_payment_type == 'dp' ? $item->down_payment_amount / $item->quantity : $item->price;
                $items[] = [
                    'id' => (string) $item->product_id,
                    'price' => (int) $price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            }

            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            $snapToken = $midtrans->createTransaction($order, $items, $customerDetails);

            if ($snapToken) {
                return view('payment', compact('snapToken', 'order'));
            }
        }

        return redirect()->route('dashboard')->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }

    public function callback(Request $request)
    {
        try {
            $midtrans = new MidtransService();
            $notification = new Notification();

            $orderNumber = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($fraudStatus == 'accept') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                    Mail::to($order->user->email)
                        ->subject('Pembayaran Berhasil')
                        ->queue(new OrderStatusMail($order));
                }
            } elseif ($transactionStatus == 'pending') {
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                ]);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Callback Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
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
