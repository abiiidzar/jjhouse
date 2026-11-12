@extends('layouts.admin')

@section('title', 'Stock Alert - ' . $stockAlert->product->name)

@section('header', 'Stock Management')
@section('subheader', 'Manage stock alerts')
@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Stock Alert Details</h1>
            <p class="text-gray-600 mt-2">{{ $stockAlert->product->name }} - Size: {{ $stockAlert->productSize->size }}</p>
        </div>
        <a href="{{ route('admin.stock-alerts.index') }}" class="text-blue-600 hover:text-blue-800">← Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Alert Info Card -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $stockAlert->product->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $stockAlert->productSize->size }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                        <p class="text-2xl font-bold {{ $stockAlert->current_stock == 0 ? 'text-red-600' : 'text-yellow-600' }}">
                            {{ $stockAlert->current_stock }} units
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Threshold</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $stockAlert->threshold }} units</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <div class="mt-2">
                            @if($stockAlert->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Active
                                </span>
                            @elseif($stockAlert->status === 'acknowledged')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    Acknowledged
                                </span>
                            @elseif($stockAlert->status === 'resolved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Resolved
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Triggered</label>
                        <p class="text-gray-900">{{ ($stockAlert->first_triggered_at ?? $stockAlert->created_at)?->format('M d, Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($stockAlert->status === 'active')
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Take Action</h3>

                <!-- Acknowledge Form -->
                <form method="POST" action="{{ route('admin.stock-alerts.acknowledge', $stockAlert->id) }}" class="mb-4">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Acknowledgment Notes (Optional)</label>
                        <textarea name="notes" rows="3" placeholder="e.g., Order placed, delivery expected on..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg transition">
                        Acknowledge Alert
                    </button>
                </form>

                <!-- Resolve Form -->
                <form method="POST" action="{{ route('admin.stock-alerts.resolve', $stockAlert->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Restocked Quantity (Optional)</label>
                        <input type="number" name="restock_quantity" min="0" placeholder="e.g., 20"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                        Resolve Alert (Restocked)
                    </button>
                </form>
            </div>
            @endif

            <!-- Acknowledgment Info -->
            @if($stockAlert->acknowledged_at)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="text-sm">
                    <strong>Acknowledged by {{ $stockAlert->acknowledgedBy->name }}</strong>
                    <p class="text-yellow-900 mt-1">{{ $stockAlert->acknowledged_at->format('M d, Y H:i') }}</p>
                    @if($stockAlert->acknowledgment_notes)
                    <p class="text-yellow-900 mt-2 italic">{{ $stockAlert->acknowledgment_notes }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Resolution Info -->
            @if($stockAlert->resolved_at)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="text-sm">
                    <strong>Resolved</strong>
                    <p class="text-green-900 mt-1">{{ $stockAlert->resolved_at->format('M d, Y H:i') }}</p>
                    @if($stockAlert->restock_quantity)
                    <p class="text-green-900 mt-2">Restocked: <strong>{{ $stockAlert->restock_quantity }} units</strong></p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Stock History -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <x-icon name="chart-bar" class="w-5 h-5 text-gray-700" />
                    <span>Stock History</span>
                </h3>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Date & Time</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Action</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-700">Before</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-700">After</th>
                            <th class="px-4 py-2 text-center font-medium text-gray-700">Changed</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($history as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $record->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $record->action === 'sold' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($record->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">{{ $record->quantity_before }}</td>
                            <td class="px-4 py-2 text-center">{{ $record->quantity_after }}</td>
                            <td class="px-4 py-2 text-center font-semibold
                                {{ $record->quantity_changed > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $record->quantity_changed > 0 ? '+' : '' }}{{ $record->quantity_changed }}
                            </td>
                            <td class="px-4 py-2">{{ $record->user->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No stock history available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($history->count() > 0)
                <div class="mt-4">
                    {{ $history->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h4 class="font-semibold text-gray-900 mb-4">Summary</h4>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ ucfirst($stockAlert->status) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Priority:</span>
                        <span class="font-medium text-gray-900 ml-2 inline-flex items-center gap-1.5">
                            @if($stockAlert->current_stock == 0)
                                <x-icon name="dot" class="w-3 h-3 text-red-500" />
                                <span>Critical</span>
                            @else
                                <x-icon name="dot" class="w-3 h-3 text-yellow-500" />
                                <span>High</span>
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Days Since Alert:</span>
                        <span class="font-medium text-gray-900 ml-2">
                            {{ ($stockAlert->first_triggered_at ?? $stockAlert->created_at)?->diffInDays(now()) ?? 0 }} days
                        </span>
                    </div>
                </div>
            </div>

            <!-- Product Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="font-semibold text-gray-900 mb-4">Product Actions</h4>
                <div class="space-y-2">
                    <a href="{{ route('admin.products.edit', $stockAlert->product->id) }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center transition">
                        Edit Product
                    </a>
                    <a href="{{ route('admin.stock-history.product', $stockAlert->product->id) }}"
                        class="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-center transition">
                        View History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
