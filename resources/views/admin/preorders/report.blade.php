@extends('layouts.admin')

@section('title', 'Preorder Dashboard')
@section('header', 'Preorder Dashboard')
@section('subheader', 'Ringkasan data pre-order')

@section('content')
    <div class="space-y-6">

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Total Preorders -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pre-orders</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">{{ $totalPreorder }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        📦
                    </div>
                </div>
            </div>

            <!-- Total DP -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-cyan-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total DP Collected</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">
                            Rp {{ number_format($totalDP, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 rounded-full flex items-center justify-center">
                        💰
                    </div>
                </div>
            </div>

            <!-- Remaining -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Remaining to Collect</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">
                            Rp {{ number_format($totalRemaining, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        ⏳
                    </div>
                </div>
            </div>

            <!-- Ready to Ship -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Ready to Ship</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-2">{{ $readyToShip }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        🚚
                    </div>
                </div>
            </div>

        </div>

        <!-- Monthly Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="mb-3">
                    <h3 class="text-lg font-semibold text-gray-800">This Month</h3>
                    <p class="text-sm text-gray-500">Statistik bulan berjalan</p>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $monthlyPreorder }}</h3>
                        <p class="text-sm text-gray-500">Pre-orders bulan ini</p>
                    </div>

                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center text-xl">
                        📊
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection