@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Transaksi')
@section('subheader', 'Lihat riwayat transaksi yang telah selesai')

@section('content')

<div class="p-6">

    <!-- HEADER -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-2xl font-semibold text-gray-800">
            Riwayat Transaksi
        </h1>

        <!-- FILTER -->
        <form method="GET" class="flex gap-2">
            <input type="date" name="from" value="{{ request('from') }}"
                   class="border px-3 py-2 text-sm rounded-lg">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="border px-3 py-2 text-sm rounded-lg">

            <button class="px-4 py-2 bg-black text-white text-sm rounded-lg">
                Filter
            </button>
        </form>
    </div>


    <!-- TABLE -->
    <div class="bg-white border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Order ID</th>
                        <th class="px-6 py-4 text-left">Customer</th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-center">Total</th>
                        <th class="px-6 py-4 text-center">Metode</th>
                        <th class="px-6 py-4 text-center">Status Pembayaran</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">

                        <!-- ORDER ID -->
                        <td class="px-6 py-4 font-medium text-gray-800">
                            #{{ $order->id }}
                        </td>

                        <!-- CUSTOMER -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $order->user->name ?? 'Guest' }}
                        </td>

                        <!-- DATE -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </td>

                        <!-- TOTAL -->
                        <td class="px-6 py-4 text-center font-semibold">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <!-- PAYMENT METHOD -->
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ ucfirst($order->payment_method ?? 'N/A') }}
                            </span>
                        </td>

                        <!-- PAYMENT STATUS -->
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $order->payment_status == 'unpaid' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $order->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            ">
                                {{ $order->payment_status_label }}
                            </span>
                        </td>

                        <!-- ACTION -->
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('kasir.orders.show', $order) }}"
                               class="text-blue-600 hover:underline text-sm">
                                Detail
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            Tidak ada transaksi
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- PAGINATION -->
    <div class="mt-6">
        {{ $orders->links() }}
    </div>

</div>

@endsection
