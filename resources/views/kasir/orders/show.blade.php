@extends('layouts.kasir')

@section('title', 'Add Product')
@section('header', 'Add New Orders')
@section('subheader', 'Create a new orders for your store')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('kasir.orders.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">← Kembali</a>
                    <h1 class="text-2xl font-bold text-gray-800">Proses Pembayaran #{{ $order->id }}</h1>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        @if ($message = Session::get('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ $message }}
            </div>
        @endif

        <!-- Informasi Pelanggan -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Informasi Pelanggan</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 text-sm">Nama</p>
                    <p class="text-lg font-semibold">{{ $order->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">No. Telepon</p>
                    <p class="text-lg font-semibold">{{ $order->user->phone }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Email</p>
                    <p class="text-lg font-semibold">{{ $order->user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Alamat</p>
                    <p class="text-lg font-semibold">{{ $order->user->address }}</p>
                </div>
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Detail Pesanan</h2>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Produk</th>
                        <th class="px-4 py-2 text-center">Qty</th>
                        <th class="px-4 py-2 text-right">Harga</th>
                        <th class="px-4 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-600">{{ $item->size ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t mt-6 pt-6">
                <div class="text-right space-y-2">
                    @if($order->shipping_cost)
                    <div class="flex justify-end gap-20">
                        <span class="text-gray-600">Ongkir:</span>
                        <span class="font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-end gap-20 text-xl">
                        <span class="font-bold text-gray-900">TOTAL:</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pembayaran -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Pembayaran</h2>
            <form action="{{ route('kasir.orders.payment', $order) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <select name="payment_method" id="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Metode --</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="card">Kartu Kredit</option>
                    </select>
                    @error('payment_method')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-600">Rp</span>
                        <input type="number" name="payment_amount" id="payment_amount" value="{{ $order->total_amount }}" required
                               min="{{ $order->total_amount }}" step="1000"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-right font-semibold">
                    </div>
                    {{-- <div class="mb-4">
                        <label class="text-sm">Kembalian</label>
                        <input type="text" id="change"
                            class="w-full border px-3 py-2 rounded bg-gray-100"
                            readonly>
                    </div> --}}
                    @error('payment_amount')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Catatan pembayaran..."></textarea>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-green-800">
                        <strong>Catatan:</strong> Pastikan pelanggan telah membayar sebelum mengklik tombol "Proses Pembayaran"
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium inline-flex items-center gap-2">
                        <x-icon name="check" class="w-4 h-4" />
                        <span>Proses Pembayaran</span>
                    </button>
                    <a href="{{ route('kasir.orders.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
