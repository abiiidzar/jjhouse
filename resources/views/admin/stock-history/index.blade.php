@extends('layouts.admin')

@section('title', 'Stock History')
@section('header', 'Stock Management')
@section('subheader', 'Manage home page stock history')

@section('content')
<div class="container-fluid px-4 py-6">

    @if($stats)
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="text-blue-600 text-2xl font-bold">{{ $stats['total'] }}</div>
            <div class="text-gray-600 text-sm">Total Changes</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="text-green-600 text-2xl font-bold">{{ $stats['today'] }}</div>
            <div class="text-gray-600 text-sm">Today</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
            <div class="text-purple-600 text-2xl font-bold">{{ $stats['this_week'] }}</div>
            <div class="text-gray-600 text-sm">This Week</div>
        </div>
    </div>
    @endif

    <!-- FILTER -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

            <select name="product_id" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Products</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ $product_id == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
                @endforeach
            </select>

            <select name="action" class="w-full px-3 py-2 border rounded-lg">
                <option value="">All Actions</option>
                <option value="sold">Sold</option>
                <option value="restock">Restock</option>
            </select>

            <input type="text" name="search" value="{{ $search }}" placeholder="Search..."
                class="w-full px-3 py-2 border rounded-lg">

            <input type="date" name="from_date" value="{{ $from_date }}"
                class="w-full px-3 py-2 border rounded-lg">

            <input type="date" name="to_date" value="{{ $to_date }}"
                class="w-full px-3 py-2 border rounded-lg">

            <button class="bg-blue-600 text-white rounded-lg px-4 py-2">
                Filter
            </button>

        </form>
    </div>

    <!-- ================= TABLE DESKTOP ================= -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px] border-separate border-spacing-y-2">

                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs">Date</th>
                        <th class="px-4 py-3 text-left text-xs">Product</th>
                        <th class="px-4 py-3 text-left text-xs">Size</th>
                        <th class="px-4 py-3 text-left text-xs">Action</th>
                        <th class="px-4 py-3 text-center text-xs">Before</th>
                        <th class="px-4 py-3 text-center text-xs">After</th>
                        <th class="px-4 py-3 text-center text-xs">Changed</th>
                        <th class="px-4 py-3 text-left text-xs">By</th>
                        <th class="px-4 py-3 text-left text-xs">Notes</th>
                    </tr>
                </thead>

                <tbody >
                    @forelse($histories as $history)
                    <tr class="text-sm odd:bg-white even:bg-gray-50 hover:bg-gray-100">

                        <td class="px-4 py-3">
                            {{ $history->created_at->format('d M Y H:i') }}
                        </td>

                        <td class="px-4 py-3 max-w-[180px] truncate">
                            <a href="{{ route('admin.stock-history.product', $history->productSize?->product?->id ?? 0) }}"
                               class="text-blue-600 hover:underline">
                                {{ $history->productSize?->product?->name ?? '-' }}
                            </a>
                        </td>

                        <td class="px-4 py-3">{{ $history->productSize->size ?? '-' }}</td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded
                                {{ $history->action === 'sold' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($history->action) }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">{{ $history->quantity_before }}</td>
                        <td class="px-4 py-3 text-center">{{ $history->quantity_after }}</td>

                        <td class="px-4 py-3 text-center font-semibold
                            {{ $history->quantity_changed > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $history->quantity_changed > 0 ? '+' : '' }}{{ $history->quantity_changed }}
                        </td>

                        <td class="px-4 py-3">{{ $history->user->name ?? '-' }}</td>

                        <td class="px-4 py-3 max-w-[200px] truncate">
                            {{ $history->notes ?? '-' }}
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-6 text-gray-500">
                            No history found
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- ================= MOBILE CARD ================= -->
        <div class="md:hidden divide-y">
            @forelse($histories as $history)
            <div class="p-4 text-sm space-y-2">

                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span>{{ $history->created_at->format('d M Y H:i') }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-500">Product</span>
                    <span class="truncate max-w-[140px]">
                        {{ $history->productSize?->product?->name ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Size</span>
                    <span>{{ $history->productSize->size ?? '-' }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Action</span>
                    <span class="text-xs px-2 py-1 rounded
                        {{ $history->action === 'sold' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ ucfirst($history->action) }}
                    </span>
                </div>

                <div class="grid grid-cols-3 text-center pt-2">
                    <div>
                        <div class="text-xs text-gray-400">Before</div>
                        {{ $history->quantity_before }}
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">After</div>
                        {{ $history->quantity_after }}
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Changed</div>
                        <span class="{{ $history->quantity_changed > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $history->quantity_changed }}
                        </span>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <span>By</span>
                    <span>{{ $history->user->name ?? '-' }}</span>
                </div>

                <div>
                    <div class="text-xs text-gray-400">Notes</div>
                    <div class="truncate">{{ $history->notes ?? '-' }}</div>
                </div>

            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                No history found
            </div>
            @endforelse
        </div>

    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $histories->links() }}
    </div>

</div>
@endsection
