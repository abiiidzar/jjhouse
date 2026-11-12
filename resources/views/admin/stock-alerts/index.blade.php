@extends('layouts.admin')

@section('title', 'Stock Alerts')
@section('header', 'Stock Management')
@section('subheader', 'Manage stock alerts')

@section('content')
<div class="container-fluid px-4 py-6">
    @if($stats)
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="text-red-600 text-2xl font-bold">{{ $stats['active'] }}</div>
            <div class="text-gray-600 text-sm font-medium">Active Alerts</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="text-yellow-600 text-2xl font-bold">{{ $stats['acknowledged'] }}</div>
            <div class="text-gray-600 text-sm font-medium">Acknowledged</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="text-green-600 text-2xl font-bold">{{ $stats['resolved'] }}</div>
            <div class="text-gray-600 text-sm font-medium">Resolved</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="text-blue-600 text-2xl font-bold">{{ $stats['total'] }}</div>
            <div class="text-gray-600 text-sm font-medium">Total Alerts</div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="acknowledged" {{ $status === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                    <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Product</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by product name..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <!-- Submit -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition inline-flex items-center justify-center gap-2">
                    <x-icon name="search" class="w-4 h-4" />
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Alerts Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Threshold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Triggered At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($alerts as $alert)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.products.show', $alert->product->id) }}"
                            class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ $alert->product->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $alert->productSize->size }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $alert->current_stock == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $alert->current_stock }} units
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $alert->threshold }} units</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($alert->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <x-icon name="dot" class="w-3 h-3 text-red-500" />
                                <span>Active</span>
                            </span>
                        @elseif($alert->status === 'acknowledged')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <x-icon name="dot" class="w-3 h-3 text-yellow-500" />
                                <span>Acknowledged</span>
                            </span>
                        @elseif($alert->status === 'resolved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <x-icon name="dot" class="w-3 h-3 text-green-500" />
                                <span>Resolved</span>
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ ($alert->first_triggered_at ?? $alert->created_at)?->format('M d, Y H:i') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.stock-alerts.show', $alert->id) }}"
                            class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            View →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center">
                        <div class="text-gray-500">
                            <div class="text-lg font-medium">No alerts found</div>
                            <p class="text-sm">Great! No low stock alerts at the moment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $alerts->links() }}
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="text-sm">
            <strong class="inline-flex items-center gap-1.5">
                <x-icon name="pin" class="w-4 h-4 text-blue-600" />
                <span>How it works:</span>
            </strong>
            <ul class="list-disc list-inside mt-2 text-blue-900">
                <li>Alerts are automatically created when product stock falls below the threshold (default: 5 units)</li>
                <li>You'll receive email notifications for new alerts</li>
                <li>Mark alerts as acknowledged when you see them, or resolved when stock is replenished</li>
                <li>View stock history to track all inventory changes</li>
            </ul>
        </div>
    </div>
</div>
@endsection
