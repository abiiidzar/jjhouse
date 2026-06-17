@extends('layouts.admin')

@section('title', 'Discount Product')
@section('header', 'Discount Product Management')
@section('subheader', 'Manage discount products')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Produk dalam Diskon</h1>
        <a href="{{ route('admin.discount-products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Tambah Produk Diskon
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.discount-products.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="discount_id" class="block text-sm font-medium text-gray-900 mb-1">Filter Diskon</label>
                <select name="discount_id" id="discount_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Semua Diskon --</option>
                    @foreach($discounts as $discount)
                        <option value="{{ $discount->id }}" {{ request('discount_id') == $discount->id ? 'selected' : '' }}>
                            {{ $discount->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Diskon</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Produk</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Jenis</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Qty Diperlukan</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Qty Gratis</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discountProducts as $discountProduct)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $discountProduct->discount->name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium">{{ $discountProduct->product->name }}</div>
                            <div class="text-xs text-gray-500">SKU: {{ $discountProduct->product->sku ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $discountProduct->discount->discount_type === 'volume' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $discountProduct->discount->discount_type === 'bogo' ? 'bg-pink-100 text-pink-800' : '' }}
                            ">
                                {{ ucfirst($discountProduct->discount->discount_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-center">
                            {{ $discountProduct->quantity_required ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-center">
                            {{ $discountProduct->quantity_free ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.discount-products.edit', $discountProduct) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                Edit
                            </a>
                            <form action="{{ route('admin.discount-products.destroy', $discountProduct) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-900 font-medium">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data produk diskon
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $discountProducts->links() }}
    </div>
</div>
@endsection
