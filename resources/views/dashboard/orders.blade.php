@extends('layouts.app2')

@section('title', 'My Orders - JJHOUSE')

@section('content')
<div data-theme="light" class="container mx-auto px-4 pt-24 pb-8 md:pt-28 md:pb-12">
    <div data-sidebar-layout data-customer-sidebar-layout data-customer-account-panel data-sidebar-key="customer-dashboard" data-sidebar-desktop-breakpoint="1024" data-sidebar-lock-scroll class="relative flex flex-col items-start rounded-xl border border-gray-100 bg-white shadow-sm lg:flex-row lg:items-stretch">
        <div data-sidebar-overlay aria-hidden="true"></div>
        @include('dashboard.sidebar')

        <div class="w-full min-w-0 flex-1 p-4 sm:p-6 lg:w-auto lg:p-8">
            <div class="mb-6 flex items-center gap-3">
                <x-dashboard-sidebar-toggle />
                <h1 class="text-2xl font-bold">My Orders</h1>
            </div>

            @php
                $orderTabs = [
                    'all' => 'All',
                    'pending' => 'Awaiting payment',
                    'paid' => 'Paid',
                    'processing' => 'Processing',
                    'shipping' => 'In delivery',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <nav aria-label="Filter orders by status" class="mb-6 -mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
                <div class="flex min-w-max border-b border-gray-200">
                    @foreach($orderTabs as $status => $label)
                        <a href="{{ route('dashboard', ['status' => $status]) }}"
                           @if($activeStatus === $status) aria-current="page" @endif
                           class="inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-4 py-3 text-sm transition
                               {{ $activeStatus === $status
                                   ? 'border-black font-semibold text-black'
                                   : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-black' }}
                               focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-black">
                            {{ $label }}
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs tabular-nums text-gray-600">
                                {{ $tabCounts[$status] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </nav>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                    <div class="bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="p-4 bg-gray-50 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <span class="text-sm text-gray-500">Order #</span>
                                <span class="font-mono font-medium">{{ $order->order_number }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Date</span>
                                <span class="text-sm ml-2">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Total</span>
                                <span class="font-semibold ml-2">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs rounded-full
                                    @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'processing') bg-indigo-100 text-indigo-800
                                    @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'ready_to_ship') bg-teal-100 text-teal-800
                                    @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <a href="{{ route('dashboard.order', $order) }}" class="text-sm text-gray-600 hover:text-black underline">View Details →</a>
                        </div>

                        <div class="p-4">
                            <div class="flex gap-4 overflow-x-auto">
                                @foreach($order->items->take(3) as $item)
                                <div class="flex items-center gap-3 bg-gray-50 px-3 py-2 rounded-lg">
                                    <div class="w-12 h-12 bg-gray-200 rounded overflow-hidden">
                                        @if($item->product->primaryImage)
                                            <img src="{{ $item->product->primaryImage->image_path }}"
                                                 alt="{{ $item->product->name }}"
                                                 width="48"
                                                 height="48"
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-100"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                <div class="flex items-center justify-center w-16 h-16 bg-gray-50 rounded-lg text-sm text-gray-500">
                                    +{{ $order->items->count() - 3 }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @if($activeStatus === 'all')
                        <h3 class="text-lg font-medium mb-2">No orders yet</h3>
                        <p class="text-gray-500 mb-4">You haven't placed any orders yet.</p>
                        <a href="{{ route('shop') }}" class="inline-block bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">Start Shopping</a>
                    @else
                        <h3 class="text-lg font-medium mb-2">No {{ strtolower($orderTabs[$activeStatus]) }} orders</h3>
                        <p class="text-gray-500 mb-4">There are no orders in this status.</p>
                        <a href="{{ route('dashboard') }}" class="inline-block border border-gray-300 px-6 py-2 rounded-md hover:border-black transition">View all orders</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
</div>
@endsection
