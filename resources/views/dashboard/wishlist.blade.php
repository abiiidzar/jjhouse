@extends('layouts.app')

@section('title', 'My Wishlist - JJHOUSE')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @include('dashboard.sidebar')

        <div class="lg:col-span-3">
            <h1 class="text-2xl font-bold mb-6">My Wishlist</h1>

            @php
                $wishlists = auth()->user()->wishlists()->with('product.primaryImage')->get();
            @endphp

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($wishlists as $wishlist)
                    <div class="group bg-white border border-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                        <a href="{{ route('product.show', $wishlist->product->slug) }}">
                            <div class="overflow-hidden">
                                <img src="{{ $wishlist->product->primaryImage->image_path ?? 'https://placehold.co/400x500' }}"
                                     alt="{{ $wishlist->product->name }}"
                                     class="w-full h-64 object-cover group-hover:scale-105 transition duration-500">
                            </div>
                        </a>
                        <div class="p-4">
                            <a href="{{ route('product.show', $wishlist->product->slug) }}">
                                <h3 class="font-medium hover:text-gray-600">{{ $wishlist->product->name }}</h3>
                            </a>
                            <p class="text-gray-800 font-semibold mt-1">Rp {{ number_format($wishlist->product->price, 0, ',', '.') }}</p>
                            <div class="flex gap-2 mt-3">
                                <a href="{{ route('product.show', $wishlist->product->slug) }}"
                                   class="flex-1 bg-black text-white text-center py-2 rounded-md text-sm hover:bg-gray-800 transition">
                                    View Product
                                </a>
                                <form action="{{ route('wishlist.remove', $wishlist->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 border border-gray-300 rounded-md hover:bg-gray-50 transition">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-50 rounded-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Your wishlist is empty</h3>
                    <p class="text-gray-500 mb-4">Save your favorite items here.</p>
                    <a href="{{ route('shop') }}" class="inline-block bg-black text-white px-6 py-2 rounded-md hover:bg-gray-800 transition">Start Shopping</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
