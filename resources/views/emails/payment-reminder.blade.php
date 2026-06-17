<h2>Halo {{ $order->user->name }}</h2>

<p>Kamu memiliki pesanan yang belum dibayar:</p>

<p><strong>Order:</strong> {{ $order->order_number }}</p>
<p><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>

<a href="{{ route('dashboard.orders.show', $order->id) }}">
    Bayar Sekarang
</a>

<p>Segera lakukan pembayaran agar pesanan diproses 🙏</p>
