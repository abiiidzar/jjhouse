@extends('layouts.app')

@section('title', $product->name)

@section('content')

<!-- ================= PRODUCT DETAIL ================= -->
<section data-theme="light" class="px-10 py-20">

    <div class="grid md:grid-cols-2 gap-16 max-w-7xl mx-auto">

        <!-- 🔥 LEFT: IMAGE -->
        <div>

            <div class="aspect-square bg-gray-100 overflow-hidden">
                @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->image_path }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">
                        <img src="{{ asset('storage/home/asset.png') }}" alt="No Image" class="w-6 h-6">
                    </div>
                @endif
            </div>

        </div>


        <!-- 🔥 RIGHT: INFO -->
        <div class="flex flex-col justify-between">

            <div>

                <h1 class="text-3xl md:text-4xl font-semibold">
                    {{ $product->name }}
                </h1>

                <p class="text-gray-400 mt-2">
                    {{ $product->category->name ?? '' }}
                </p>

                <p class="text-xl mt-6">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </p>

                <!-- DESCRIPTION -->
                <p class="mt-6 text-gray-600 leading-relaxed">
                    {{ $product->description ?? 'No description available.' }}
                </p>

            </div>


            <!-- 🔥 ACTION -->
            <div class="mt-10">
                <livewire:add-to-cart-button :product="$product" />
            </div>

        </div>

    </div>

</section>


<!-- ================= RELATED ================= -->
<section class="px-10 pb-32">

    <h2 class="text-center text-xs tracking-[0.3em] uppercase mb-16">
        ( You May Also Like )
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-10">

        @foreach($relatedProducts as $item)

        <a href="{{ route('product.show', $item->slug) }}" class="group">

            <div class="aspect-square overflow-hidden bg-gray-100">

                @if($item->primaryImage)
                    <img src="{{ $item->primaryImage->image_path }}"
                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                @endif

            </div>

            <div class="mt-3 text-sm">
                <p>{{ $item->name }}</p>
                <p class="text-gray-400">
                    Rp {{ number_format($item->price, 0, ',', '.') }}
                </p>
            </div>

        </a>

        @endforeach

    </div>

</section>

@endsection
