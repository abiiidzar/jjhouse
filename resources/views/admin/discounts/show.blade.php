@extends('layouts.admin')

@section('title', 'Detail Discount')
@section('header', 'Discount Management')
@section('subheader', 'Detail discount')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $discount->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $discount->description ?: 'Tidak ada deskripsi' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.discounts.edit', $discount) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('admin.discounts.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Diskon</h2>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Tipe</dt>
                    <dd class="font-medium text-gray-900">{{ \App\Models\Discount::TYPES[$discount->discount_type] ?? ucfirst($discount->discount_type) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nilai</dt>
                    <dd class="font-medium text-gray-900">
                        @if($discount->discount_type === 'percentage')
                            {{ $discount->discount_value }}%
                        @else
                            Rp {{ number_format($discount->discount_value, 0, ',', '.') }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Minimum Pembelian</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $discount->minimum_purchase ? 'Rp ' . number_format($discount->minimum_purchase, 0, ',', '.') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Maksimum Diskon</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $discount->maximum_discount ? 'Rp ' . number_format($discount->maximum_discount, 0, ',', '.') : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Berlaku Untuk</dt>
                    <dd class="font-medium text-gray-900">{{ \App\Models\Discount::APPLICABLE[$discount->applicable_to] ?? ucfirst($discount->applicable_to) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Prioritas</dt>
                    <dd class="font-medium text-gray-900">{{ $discount->priority }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Mulai</dt>
                    <dd class="font-medium text-gray-900">{{ $discount->starts_at->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Selesai</dt>
                    <dd class="font-medium text-gray-900">{{ $discount->ends_at ? $discount->ends_at->format('d M Y H:i') : 'Tidak ada batas akhir' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
            <div class="mb-4">
                @if($discount->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Tidak Aktif</span>
                @endif
            </div>
            <form action="{{ route('admin.discounts.toggle-status', $discount) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded">
                    Ubah Status
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Kode Promo</h2>
            <div class="space-y-3">
                @forelse($discount->promoCodes as $promoCode)
                    <div class="flex items-center justify-between border-b pb-3">
                        <span class="font-mono font-bold text-blue-600">{{ $promoCode->code }}</span>
                        <span class="text-sm text-gray-500">{{ $promoCode->used_count }} digunakan</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada kode promo.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Produk Diskon</h2>
            <div class="space-y-3">
                @forelse($discount->discountProducts as $discountProduct)
                    <div class="flex items-center justify-between border-b pb-3">
                        <span class="font-medium text-gray-900">{{ $discountProduct->product->name }}</span>
                        <span class="text-sm text-gray-500">
                            Qty: {{ $discountProduct->quantity_required ?? '-' }} / Gratis: {{ $discountProduct->quantity_free ?? '-' }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada produk diskon.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
