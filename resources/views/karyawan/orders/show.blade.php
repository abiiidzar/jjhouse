@extends('layouts.karyawan')

@section('title', 'Order #' . $order->order_number)
@section('header', 'Order Detail' . $order->id)
@section('subheader', 'Order #' . $order->order_number)

@section('content')
<div class="space-y-6">
    @if ($message = Session::get('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
        <span>{{ $message }}</span>
        <button onclick="this.parentElement.style.display='none'" class="text-green-700 font-bold">×</button>
    </div>
    @endif

    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Customer Info -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">👤 Pelanggan</h3>
                <p class="text-lg font-semibold text-gray-900">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-600">{{ $order->user->phone }}</p>
                <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
            </div>

            <!-- Shipping Address -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">📍 Alamat Pengiriman</h3>
                <p class="text-sm text-gray-900">{{ $order->shipping_address }}</p>
                <p class="text-sm text-gray-600 mt-2">
                    Kurir: <strong>{{ ucfirst(str_replace('_', ' ', $order->shipping_courier ?? 'N/A')) }}</strong>
                </p>
                <p class="text-sm text-gray-600">
                    Ongkir: <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                </p>
            </div>

            <!-- Order Status -->
            <div>
                <h3 class="text-sm font-semibold text-gray-600 mb-2">📦 Status Pesanan</h3>
                <div class="mb-3">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $order->status === 'processing' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $order->status === 'ready_to_ship' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $order->status === 'delivered' ? 'bg-purple-100 text-purple-800' : '' }}
                    ">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <p class="text-xs text-gray-600">Pesanan sejak: {{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Picking List / Item Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">📋 Daftar Item yang Harus Dikemas</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Produk</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Ukuran</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Harga</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Subtotal</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">✓</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-600">SKU: {{ $item->product->id }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item->productSize)
                            <span class="px-2 py-1 bg-gray-100 rounded text-sm font-medium">{{ $item->productSize->size }}</span>
                            @else
                            <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center font-semibold">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox" class="w-4 h-4 rounded">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Total -->
        <div class="border-t mt-4 pt-4 flex justify-end">
            <div class="w-full md:w-1/3 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">Rp {{ number_format($order->total_amount - $order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Ongkos Kirim:</span>
                    <span class="font-medium">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold pt-2 border-t">
                    <span>Total:</span>
                    <span class="text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">🔄 Update Status Pesanan</h3>

        <form action="{{ route('karyawan.orders.updateStatus', $order) }}" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                <select name="status" id="status" required class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Status --</option>
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>🟡 Pending (Baru)</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>🟠 Processing (Dikemas)</option>
                    <option value="ready_to_ship" {{ $order->status === 'ready_to_ship' ? 'selected' : '' }}>🟢 Ready to Ship (Siap Dikirim)</option>
                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>🔵 Shipped (Dikirim)</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered (Diterima)</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled (Dibatalkan)</option>
                </select>
                @error('status')
                <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Catatan untuk pesanan ini..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ route('karyawan.orders.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium">
                    ← Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Status Timeline -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">📈 Timeline Status</h3>
        <div class="space-y-3">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ $order->status !== 'pending' ? 'bg-green-100' : 'bg-yellow-100' }}">
                        <span class="text-sm font-bold {{ $order->status !== 'pending' ? 'text-green-600' : 'text-yellow-600' }}">✓</span>
                    </div>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Pending - Pesanan Baru</p>
                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ $order->status !== 'pending' ? 'bg-green-100' : 'bg-gray-100' }}">
                        <span class="text-sm font-bold {{ $order->status !== 'pending' ? 'text-green-600' : 'text-gray-600' }}">{{ $order->status !== 'pending' ? '✓' : '○' }}</span>
                    </div>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Processing - Sedang Dikemas</p>
                    <p class="text-sm text-gray-600">{{ $order->status !== 'pending' ? 'Sedang dalam proses' : 'Menunggu...' }}</p>
                </div>
            </div>

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full {{ in_array($order->status, ['ready_to_ship', 'shipped', 'delivered']) ? 'bg-green-100' : 'bg-gray-100' }}">
                        <span class="text-sm font-bold {{ in_array($order->status, ['ready_to_ship', 'shipped', 'delivered']) ? 'text-green-600' : 'text-gray-600' }}">{{ in_array($order->status, ['ready_to_ship', 'shipped', 'delivered']) ? '✓' : '○' }}</span>
                    </div>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Ready to Ship - Siap Dikirim</p>
                    <p class="text-sm text-gray-600">{{ in_array($order->status, ['ready_to_ship', 'shipped', 'delivered']) ? 'Siap di-pickup kurir' : 'Menunggu...' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Instruction -->
    <div class="bg-blue-50 border-l-4 border-blue-400 rounded p-4">
        <p class="font-semibold text-blue-900">📌 Alur Kerja Pemesanan</p>
        <ol class="text-sm text-blue-800 mt-2 space-y-1 list-decimal list-inside">
            <li>Pesanan masuk dengan status <strong>Pending</strong></li>
            <li>Kemas produk dan ubah status menjadi <strong>Processing</strong></li>
            <li>Setelah dikemas, ubah status menjadi <strong>Ready to Ship</strong></li>
            <li>Kurir mengambil paket (status menjadi <strong>Shipped</strong>)</li>
            <li>Pelanggan menerima paket (status menjadi <strong>Delivered</strong>)</li>
        </ol>
    </div>
</div>
@endsection
                    @error('status')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
