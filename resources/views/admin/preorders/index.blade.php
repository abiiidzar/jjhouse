@extends('layouts.admin')

@section('title', 'Pre-orders')
@section('header', 'Pre-order Management')
@section('subheader', 'Manage all pre-order items')

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-gray-500 text-sm">Total Pre-orders</p>
            <p class="text-2xl font-bold">{{ $totalPreorder ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-gray-500 text-sm">DP Payments</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalDP ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-gray-500 text-sm">Remaining Payments</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalRemaining ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-gray-500 text-sm">Ready to Ship</p>
            <p class="text-2xl font-bold">{{ $readyToShip ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-3">
        <a href="{{ route('admin.preorders.index') }}" class="px-3 py-1 text-sm rounded-full {{ !request('status') ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">All</a>
        <a href="{{ route('admin.preorders.index', ['status' => 'process']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'process' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">Process</a>
        <a href="{{ route('admin.preorders.index', ['status' => 'ready_to_ship']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'ready_to_ship' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">Ready to Ship</a>
        <a href="{{ route('admin.preorders.index', ['status' => 'waiting_pelunasan']) }}" class="px-3 py-1 text-sm rounded-full {{ request('status') == 'waiting_pelunasan' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">Waiting Payment</a>
    </div>

    <!-- Pre-order Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <form action="{{ route('admin.preorders.bulk-ready') }}" method="POST">
                @csrf
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-10"><input type="checkbox" id="select-all"></th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($preorderItems as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3"><input type="checkbox" name="items[]" value="{{ $item->id }}" class="item-checkbox"></td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-sm">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ $item->quantity }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $item->order->user->name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->preorder_payment_type == 'dp' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $item->preorder_payment_type == 'dp' ? 'DP 50%' : 'Full Payment' }}
                                </span>
                             </td>
                            <td class="px-4 py-3 text-sm">Rp {{ number_format($item->down_payment_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">Rp {{ number_format($item->remaining_payment, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->preorder_ready_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->preorder_ready_at ? 'Ready to Ship' : 'Processing' }}
                                </span>
                             </td>
                            <td class="px-4 py-3">
                                @if(!$item->preorder_ready_at)
                                <form action="{{ route('admin.preorders.ready', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Mark Ready</button>
                                </form>
                                @endif
                             </td>
                         </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">No pre-order items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($preorderItems->count() > 0)
                <div class="p-4 border-t border-gray-100">
                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-800">Bulk Mark Ready</button>
                </div>
                @endif
            </form>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $preorderItems->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('select-all')?.addEventListener('change', function(e) {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = e.target.checked);
    });
</script>
@endpush
@endsection
