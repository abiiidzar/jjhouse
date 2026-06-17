@extends('layouts.app')

@section('content')

    <!-- ================= HERO ================= -->
    {{-- <section class="relative h-screen text-white" x-data="{ cartOpen: false }">

        <img src="{{ $homeImages['hero_image'] }}"
             class="absolute inset-0 w-full h-full object-cover">

        <div class="absolute inset-0 bg-black/40"></div>



        <!-- TEXT -->
        <div class="absolute bottom-20 left-10">
            <a href="{{ route('shop') }}" class="text-5xl md:text-7xl font-semibold">SHOP NOW</h1>
            <p class="text-gray-300 text-xl mt-2">
                EXPLORE OUR FIRST COLLECTION
            </p>
        </div>

        <!-- DECOR -->
        <div class="absolute left-10 bottom-50 opacity-60 text-2xl">01</div>
        <div class="absolute left-1/2 bottom-1/2 opacity-50 text-2xl">02</div>
        <div class="absolute right-10 top-1/2 opacity-60 text-2xl">03</div>
        <div class="absolute right-10 bottom-20 opacity-60 text-2xl">Scroll</div>

    </section> --}}
    <section class="relative h-screen overflow-hidden text-white">

        <!-- SLIDER -->
        <div data-theme="dark" id="heroSlider" class="relative w-full h-full">

            @foreach([
                $homeImages['hero_image_1'],
                $homeImages['hero_image_2'],
                $homeImages['hero_image_3'],
            ] as $index => $img)

            <div class="hero-slide absolute inset-0"
                style="clip-path: polygon(0 100%, 100% 100%, 100% 100%, 0 100%);
                        z-index: {{ 10 - $index }}">

                <img src="{{ $img }}" class="w-full h-full object-cover">

                <div class="absolute inset-0 bg-black/40"></div>

                <div class="absolute bottom-20 left-10">

                    <!-- 🔥 LINK KE SHOP -->

                    <a href="{{ route('shop') }}"
                    class="
                    text-5xl md:text-7xl font-semibold
                    hover:opacity-80 transition ">
                        SHOP NOW
                    </a>

                    <p class="text-gray-300 text-xl mt-2">
                        EXPLORE OUR FIRST COLLECTION
                    </p>

                </div>

            </div>

            @endforeach

        </div>

        <!-- NAV -->
        <div class="nav-btn absolute left-10 bottom-45 text-2xl cursor-pointer z-50"
            data-index="0"
            data-image="{{ $homeImages["hero_image_1"] }}">
            01
        </div>

        <div class="nav-btn absolute left-2/6 top-1/2 text-2xl cursor-pointer z-50"
            data-index="1"
            data-image="{{ $homeImages["hero_image_2"] }}">
            02
        </div>

        <div class="nav-btn absolute left-4/6 top-1/2 text-2xl cursor-pointer z-50"
            data-index="2"
            data-image="{{ $homeImages["hero_image_3"] }}">
            03
        </div>
        <div class="scroll-btn absolute right-5 top-1/2 text-2xl cursor-pointer z-50 "
            >
            Scroll
        </div>

        <!-- SLIDE NUMBER -->
        <div class="absolute bottom-10 right-10 text-white tracking-widest">
            <span id="slideNum">01</span>
        </div>

        <!-- PREVIEW IMAGE -->
        <div id="preview"
            class="pointer-events-none fixed top-0 left-0 w-64 h-80 opacity-0 z-50">
            <img id="previewImg"
                class="w-full h-full object-cover rounded-lg shadow-xl">
        </div>

    </section>


    <!-- ================= STORY ================= -->
    <section data-theme="light" class="bg-white text-black py-32 px-10">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Image on the left -->
                <div>
                    <img src="{{ $homeImages['story_image'] }}"
                         alt="Story Image"
                         class="w-full rounded-lg shadow-lg object-cover h-[500px]">
                </div>

                <!-- Text on the right -->
                <div>
                    <p class="text-3xl md:text-5xl leading-tight">
                        Born from a shared love of the game, rooted in a vision of authenticity
                        and creativity expressed through what we wear. For us, it's about the good
                        times, on and off the course.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ================= FEATURED ================= -->
    <section data-theme="light" class="bg-white py-32 px-10 text-black">

        <div class="text-center mb-20 text-xs tracking-[0.3em] uppercase">
            ( Featured Products )
        </div>

        <div class="overflow-hidden relative group">

            <div class="flex gap-10 animate-marquee-navbar marquee w-max">

                @foreach($products as $product)
                    <div class="min-w-[250px]">

                        <a href="{{ route('product.show', $product->slug) }}" class="block group/item">

                            <div class="relative overflow-hidden">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_path }}"
                                        class="w-[300px] h-[300px] object-cover transition duration-700 group-hover/item:scale-105">
                                @else
                                    <div class="w-full h-[300px] bg-gray-100 flex items-center justify-center text-gray-300">
                                         <img src="{{ asset('storage/home/asset.png') }}" alt="No Image" class="w-6 h-6">
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 text-sm">
                                <p>{{ $product->name }}</p>
                                <p class="text-gray-400">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>

                        </a>

                    </div>
                @endforeach

                {{-- DUPLICATE (BIAR LOOP HALUS) --}}
                @foreach($products as $product)
                    <div class="min-w-[250px]">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_path }}" class="w-full h-[300px] object-cover">
                            @else
                                <div class="w-full h-[300px] bg-gray-100 flex items-center justify-center text-gray-300">
                                    <img src="{{ asset('storage/home/asset.png') }}" alt="No Image" class="w-6 h-6">
                                </div>
                            @endif
                        </a>
                    </div>
                @endforeach

            </div>

        </div>

        <div class="text-center mt-20">
            <a href="{{ route('shop') }}"
               class="relative inline-block text-xs tracking-widest uppercase pb-1
              after:content-[''] after:absolute after:left-0 after:bottom-0
              after:w-full after:h-[1px] after:bg-black
              after:scale-x-0 after:origin-left
              after:transition-transform after:duration-300
              hover:after:scale-x-100">
                View All Products
            </a>
        </div>

    </section>


    <!-- ================= EDITORIAL GRID ================= -->
    <section data-theme="light" class="bg-white px-10 py-20">

        <div class="grid md:grid-cols-3 gap-6">

            <!-- LEFT BIG -->
            <div>
                <img src="{{ $homeImages['editorial_image_1'] }}" class="w-full h-[500px] object-cover">
                <p class="mt-3 text-xs uppercase">Our Products</p>
            </div>

            <!-- MIDDLE -->
            <div>
                <img src="{{ $homeImages['editorial_image_2'] }}" class="w-full h-[500px] object-cover">
                <p class="mt-3 text-xs uppercase flex justify-between">
                    About Us <span>Read More</span>
                </p>
            </div>

            <!-- RIGHT -->
            <div>
                <img src="{{ $homeImages['editorial_image_3'] }}" class="w-full h-[500px] object-cover">
                <p class="mt-3 text-xs uppercase">Community</p>
            </div>

        </div>

    </section>


@endsection
