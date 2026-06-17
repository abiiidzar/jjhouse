<h2>Halo {{ $cart->user->name }}</h2>

<p>Kamu meninggalkan beberapa item di keranjang:</p>

<ul>
@foreach($cart->items as $item)
    <li>{{ $item->product->name }} ({{ $item->quantity }})</li>
@endforeach
</ul>

<a href="{{ route('cart') }}">
    Lanjutkan Belanja
</a>

<p>Jangan sampai kehabisan ya 👀</p>
