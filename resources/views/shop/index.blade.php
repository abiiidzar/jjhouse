@extends('layouts.app')

@section('title', 'Shop')

@section('content')

<!-- ================= HERO / HEADER ================= -->
<section data-theme="light" class="bg-white px-10 py-20 text-center uppercase tracking-widest">
    <h1 class="text-4xl md:text-6xl font-semibold tracking-tight">
        SHOP
    </h1>
    <p class="text-neutral-500 mt-3 text-sm tracking-widest uppercase">
        Explore Our Collection
    </p>
</section>


<!-- ================= FILTER BAR ================= -->
<section data-theme="light" class="bg-white px-10 pb-10">
    <div class="flex flex-wrap items-center justify-between gap-4 text-sm">

        <!-- LEFT -->
        <div class="flex gap-6 uppercase tracking-widest text-neutral-500">
            <a href="{{ route('shop') }}" class="hover:text-black relative inline-block text-l tracking-widest uppercase pb-1
              after:content-[''] after:absolute after:left-0 after:bottom-0
              after:w-full after:h-[0.5px] after:bg-black
              after:scale-x-0 after:origin-left
              after:transition-transform after:duration-300
              hover:after:scale-x-100 {{ request('category') ? 'text-black' : '' }}">All</a>

            @foreach($categories ?? [] as $category)
                <a href="{{ route('shop', ['category' => $category->slug]) }}"
                   class="hover:text-black relative inline-block text-l tracking-widest uppercase pb-1
              after:content-[''] after:absolute after:left-0 after:bottom-0
              after:w-full after:h-[0.5px] after:bg-black
              after:scale-x-0 after:origin-left
              after:transition-transform after:duration-300
              hover:after:scale-x-100 {{ request('category') == $category->slug ? 'text-black' : '' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>

        <!-- RIGHT -->
        <form method="GET">
            <select name="sort"
                    onchange="this.form.submit()"
                    class="border-none bg-white text-black focus:outline-none uppercase tracking-widest">
                <option value="latest">Latest</option>
                <option value="price_asc">Price ↑</option>
                <option value="price_desc">Price ↓</option>
            </select>
        </form>

    </div>
</section>


<!-- ================= PRODUCTS ================= -->
<section data-theme="light" class="bg-white px-10 pb-32">

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">

        @forelse($products as $product)

        <a href="{{ route('product.show', $product->slug) }}" class="group">

            <!-- IMAGE -->
            <div class="aspect-square overflow-hidden bg-gray-100">

                @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->image_path }}"
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-3xl">
                        <span>
                            <img src="{{ asset('storage/home/asset.png') }}" alt="No Image" class="w-30 h-30 object-cover transition duration-700 group-hover:scale-105">
                        </span>
                    </div>
                @endif

            </div>

            <!-- INFO -->
            <div class="mt-3 text-sm">

                <p class="group-hover:opacity-70 transition">
                    {{ $product->name }}
                </p>

                <p class="text-gray-400">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

            </div>

        </a>

        @empty

        <div class="col-span-full text-center text-gray-400 py-20">
            No products found.
        </div>

        @endforelse

    </div>

</section>


<!-- ================= PAGINATION ================= -->
@if($products->hasPages())
<section data-theme="light" class="bg-white pb-20 text-center">
    {{ $products->links('vendor.pagination.tailwind') }}
</section>
@endif

<span id="slideNum" class="hidden">01</span>
@endsection
