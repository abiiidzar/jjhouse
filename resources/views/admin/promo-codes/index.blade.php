@extends('layouts.admin')

@section('title', 'Promo Code')
@section('header', 'Promo Code Management')
@section('subheader', 'Manage promo codes')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Kode Promo</h1>
        <a href="{{ route('admin.promo-codes.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            Tambah Kode Promo
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full border-separate border-spacing-0">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                        Kode
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                        Diskon
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                        Periode Berlaku
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                        Penggunaan
                    </th>

                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">
                        Status
                    </th>

                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($promoCodes as $promoCode)

                <tr class="hover:bg-gray-50 transition duration-200">

                    <!-- KODE -->
                    <td class="px-6 py-5">
                        <div class="font-mono font-bold text-blue-600 tracking-wide">
                            {{ strtoupper($promoCode->code) }}
                        </div>
                    </td>

                    <!-- DISKON -->
                    <td class="px-6 py-5">
                        <div class="space-y-1">
                            <div class="text-sm font-medium text-gray-800">
                                {{ $promoCode->discount->name }}
                            </div>

                            <div class="text-xs text-gray-500">
                                @if($promoCode->discount->discount_type === 'percentage')
                                    {{ $promoCode->discount->discount_value }}%
                                @else
                                    Rp {{ number_format($promoCode->discount->discount_value, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- PERIODE -->
                    <td class="px-6 py-5">
                        <div class="space-y-1 text-sm text-gray-600">

                            <div>
                                <span class="text-gray-400">Dari:</span>
                                {{ $promoCode->valid_from->format('d M Y') }}
                            </div>

                            @if($promoCode->valid_until)
                                <div>
                                    <span class="text-gray-400">Sampai:</span>
                                    {{ $promoCode->valid_until->format('d M Y') }}
                                </div>
                            @else
                                <div class="text-red-500 text-xs font-medium">
                                    Tidak ada batas
                                </div>
                            @endif

                        </div>
                    </td>

                    <!-- PENGGUNAAN -->
                    <td class="px-6 py-5 min-w-[180px]">

                        @if($promoCode->usage_limit)

                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="font-medium text-gray-700">
                                    {{ $promoCode->used_count }}
                                </span>

                                <span class="text-gray-400">
                                    / {{ $promoCode->usage_limit }}
                                </span>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div
                                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ min(100, ($promoCode->used_count / $promoCode->usage_limit) * 100) }}%">
                                </div>
                            </div>

                        @else

                            <div class="text-sm text-gray-700">
                                {{ $promoCode->used_count }} kali
                                <span class="text-gray-400">(Unlimited)</span>
                            </div>

                        @endif

                    </td>

                    <!-- STATUS -->
                    <td class="px-6 py-5">

                        @if($promoCode->is_active)

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Aktif
                            </span>

                        @else

                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                Tidak Aktif
                            </span>

                        @endif

                    </td>

                    <!-- AKSI -->
                    <td class="px-6 py-5">
                        <div class="flex items-center justify-center gap-2">

                            <!-- DETAIL -->
                            <a href="{{ route('admin.promo-codes.show', $promoCode) }}"
                            title="Detail"
                            class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-600 hover:bg-gray-100 transition">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </a>

                            <!-- EDIT -->
                            <a href="{{ route('admin.promo-codes.edit', $promoCode) }}"
                            title="Edit"
                            class="w-10 h-10 flex items-center justify-center rounded-xl text-blue-600 hover:bg-blue-50 transition">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            <!-- TOGGLE -->
                            <form action="{{ route('admin.promo-codes.toggle-status', $promoCode) }}"
                                method="POST">
                                @csrf

                                <button type="submit"
                                        title="{{ $promoCode->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl
                                        {{ $promoCode->is_active
                                            ? 'text-yellow-600 hover:bg-yellow-50'
                                            : 'text-green-600 hover:bg-green-50' }}
                                        transition">

                                    @if($promoCode->is_active)

                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
                                        </svg>

                                    @else

                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"/>
                                        </svg>

                                    @endif

                                </button>
                            </form>

                            <!-- DELETE -->
                            <form action="{{ route('admin.promo-codes.destroy', $promoCode) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        title="Hapus"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl text-red-600 hover:bg-red-50 transition">

                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"/>
                                    </svg>

                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        Tidak ada data kode promo
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $promoCodes->links() }}
    </div>
</div>
@endsection
