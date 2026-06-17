@extends('layouts.admin')

@section('title', 'Stock History - ' . $product->name)
@section('header', 'Stock Management')
@section('subheader', 'Manage product stock history')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-gray-600 mt-2">Complete stock history for this product</p>
        </div>
        <a href="{{ route('admin.stock-history.index') }}" class="text-blue-600 hover:text-blue-800">← Back</a>
    </div>

    <!-- Product Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product SKU</label>
                <p class="text-lg font-semibold text-gray-900">{{ $product->id }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Total Stock</label>
                <p class="text-lg font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $product->stock }} units
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <p class="text-lg font-semibold text-gray-900">{{ $product->formatted_price }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Sizes</label>
                <p class="text-lg font-semibold text-gray-900">{{ $product->sizes->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Size Breakdown -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock by Size</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Size</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-700">Current Stock</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-700">Status</th>
                        <th class="px-4 py-2 text-center font-medium text-gray-700">Total Changes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($product->sizes as $size)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-900">{{ $size->size }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                {{ $size->stock == 0 ? 'bg-red-100 text-red-800' : ($size->stock < 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $size->stock }} units
                            </span>
                        </td>
                        <td class="px-4 py-2 text-center text-sm">
                            @if($size->stock == 0)
                                🔴 Out of Stock
                            @elseif($size->stock < 5)
                                🟡 Low Stock
                            @else
                                🟢 In Stock
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">{{ $size->stockHistories->count() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">📊 Complete History</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Before</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">After</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Changed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($histories as $history)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $history->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $history->productSize->size }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $history->action === 'sold' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($history->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                        {{ $history->quantity_before }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">
                        {{ $history->quantity_after }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold
                        {{ $history->quantity_changed > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $history->quantity_changed > 0 ? '+' : '' }}{{ $history->quantity_changed }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $history->user->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div class="max-w-xs truncate">{{ $history->notes ?? '-' }}</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center">
                        <div class="text-gray-500">
                            <div class="text-lg font-medium">No history found</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $histories->links() }}
    </div>

    <!-- Back to Product -->
    <div class="mt-6">
        <a href="{{ route('admin.products.edit', $product->id) }}"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
            ← Edit Product
        </a>
    </div>
</div>
@endsection
