@extends('layouts.kasir')

@section('title', 'Add Transaksi Kasir')
@section('header', 'Add New Transaksi')
@section('subheader', 'Create a new transaksi for your store')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- 🔥 LEFT: LIST PESANAN -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <x-icon name="credit-card" class="w-5 h-5 text-gray-700" />
            <span>Pesanan Belum Dibayar</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Pelanggan</th>
                        <th class="px-4 py-2 text-left">Total</th>
                        <th class="px-4 py-2 text-left">Waktu</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">

                    @forelse($orders ?? \App\Models\Order::where('payment_status','unpaid')->latest()->get() as $order)

                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-2 font-semibold">
                            #{{ $order->id }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $order->user->name }}
                        </td>

                        <td class="px-4 py-2 font-semibold text-green-600">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $order->created_at->format('H:i') }}
                        </td>

                        <td class="px-4 py-2">
                            <a href="{{ route('kasir.orders.show', $order) }}"
                               class="text-blue-600 hover:underline text-sm">
                                Proses
                            </a>
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            Tidak ada pesanan
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>


    <!-- 🔥 RIGHT: PANEL CEPAT -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <x-icon name="bolt" class="w-5 h-5 text-yellow-500" />
            <span>Aksi Cepat</span>
        </h2>

        <div class="space-y-3">

            <a href="{{ route('kasir.orders.index') }}"
               class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <p class="font-semibold text-blue-600">Semua Pesanan</p>
                <p class="text-xs text-gray-500">Lihat semua transaksi</p>
            </a>

            <a href="#"
               class="block p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <p class="font-semibold text-green-600">Input Manual</p>
                <p class="text-xs text-gray-500">Tambah transaksi manual</p>
            </a>

            <a href="#"
               class="block p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                <p class="font-semibold text-yellow-600">Scan Barcode</p>
                <p class="text-xs text-gray-500">Scan produk cepat</p>
            </a>

        </div>

        <!-- INFO -->
        <div class="mt-6 bg-gray-50 p-4 rounded-lg text-sm text-gray-600 flex items-start gap-2">
            <x-icon name="light-bulb" class="w-4 h-4 text-yellow-600 mt-0.5 shrink-0" />
            <span>Klik <b>Proses</b> untuk menyelesaikan pembayaran.</span>
        </div>

    </div>

</div>

@endsection
