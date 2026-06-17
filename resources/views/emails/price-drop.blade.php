<h2>🔥 Harga Turun!</h2>

<p>Produk favorit kamu sekarang lebih murah:</p>

<p>
<strong>{{ $product->name }}</strong><br>
<s>Rp {{ number_format($oldPrice, 0, ',', '.') }}</s><br>
<b>Rp {{ number_format($newPrice, 0, ',', '.') }}</b>
</p>

<a href="{{ route('product.show', $product->slug) }}">
    Beli Sekarang
</a>

<p>Jangan sampai kehabisan ya 👀</p>
