<h2>Halo {{ $order->user->name }}</h2>

<p>Status pesanan kamu telah diperbarui:</p>

<p><strong>Order:</strong> {{ $order->order_number }}</p>
<p><strong>Status:</strong> {{ strtoupper($order->status) }}</p>

<a href="{{ route('dashboard.orders.show', $order->id) }}">
    Lihat Detail Pesanan
</a>

<p>Terima kasih telah berbelanja di JJHouse 🖤</p>
