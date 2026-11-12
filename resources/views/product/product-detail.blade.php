@extends('layouts.app')

@section('title', $product->name)

@section('content')

<!-- ================= PRODUCT DETAIL ================= -->
@php
    $galleryImages = $product->images
        ->sortByDesc('is_primary')
        ->values()
        ->map(fn ($img) => $img->image_path)
        ->all();
@endphp

<section data-theme="light" class="px-5 sm:px-10 pt-28 pb-20">

    <div class="grid md:grid-cols-2 gap-16 max-w-7xl mx-auto">

        <!-- 🔥 LEFT: IMAGE GALLERY -->
        <div
            x-data="{
                images: @js($galleryImages),
                activeIndex: 0,
                lightboxOpen: false,
                next() {
                    if (this.images.length > 1) {
                        this.activeIndex = (this.activeIndex + 1) % this.images.length;
                    }
                },
                prev() {
                    if (this.images.length > 1) {
                        this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
                    }
                }
            }"
            @keydown.escape.window="lightboxOpen = false"
            @keydown.arrow-right.window="if (lightboxOpen) next()"
            @keydown.arrow-left.window="if (lightboxOpen) prev()"
        >

            <div class="relative aspect-square bg-gray-100 overflow-hidden group">
                @if(count($galleryImages) > 0)
                    <img :src="images[activeIndex]"
                         src="{{ $galleryImages[0] }}"
                         alt="{{ $product->name }}"
                         fetchpriority="high"
                         decoding="async"
                         @click="lightboxOpen = true"
                         class="w-full h-full object-cover cursor-zoom-in transition duration-300">

                    @if(count($galleryImages) > 1)
                        <button type="button"
                                @click="prev()"
                                aria-label="Foto sebelumnya"
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/90 hover:bg-black hover:text-white text-black flex items-center justify-center shadow transition opacity-90 md:opacity-0 md:group-hover:opacity-100 focus:opacity-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <button type="button"
                                @click="next()"
                                aria-label="Foto berikutnya"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 bg-white/90 hover:bg-black hover:text-white text-black flex items-center justify-center shadow transition opacity-90 md:opacity-0 md:group-hover:opacity-100 focus:opacity-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <div class="absolute bottom-3 right-3 bg-black/75 text-white text-[11px] tracking-widest px-2.5 py-1">
                            <span x-text="(activeIndex + 1) + ' / ' + images.length">1 / {{ count($galleryImages) }}</span>
                        </div>
                    @endif
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-5xl">
                        <img src="{{ asset('images/asset.png') }}" alt="No Image" loading="lazy" decoding="async" class="w-6 h-6">
                    </div>
                @endif
            </div>

            @if(count($galleryImages) > 1)
                {{-- <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <button type="button"
                            @click="lightboxOpen = true"
                            class="inline-flex items-center gap-2 border border-black px-4 py-2 text-xs uppercase tracking-widest hover:bg-black hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Lihat Foto Lainnya ({{ count($galleryImages) }})
                    </button>

                    <button type="button"
                            @click="next()"
                            class="inline-flex items-center gap-1 text-xs uppercase tracking-widest text-gray-600 hover:text-black transition">
                        Foto Berikutnya &rarr;
                    </button>
                </div> --}}

                <div class="flex gap-3 mt-3 overflow-x-auto pb-1">
                    @foreach($galleryImages as $idx => $imgPath)
                        <button type="button"
                                @click="activeIndex = {{ $idx }}"
                                :class="activeIndex === {{ $idx }} ? 'border-black opacity-100' : 'border-transparent opacity-60 hover:opacity-100'"
                                class="w-20 h-20 flex-shrink-0 overflow-hidden bg-gray-100 border-2 transition"
                                aria-label="Lihat foto {{ $idx + 1 }}">
                            <img src="{{ $imgPath }}"
                                 alt="{{ $product->name }} - Foto {{ $idx + 1 }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>

                {{-- Fullscreen Gallery Lightbox --}}
                <div x-show="lightboxOpen"
                     x-cloak
                     x-transition.opacity
                     @click.self="lightboxOpen = false"
                     class="fixed inset-0 z-50 bg-black/90 flex flex-col items-center justify-center p-4 sm:p-8">

                    <div class="w-full max-w-5xl flex items-center justify-between text-white mb-4">
                        <span class="text-xs uppercase tracking-widest text-gray-300">
                            {{ $product->name }} &mdash; <span x-text="(activeIndex + 1) + ' / ' + images.length"></span>
                        </span>
                        <button type="button"
                                @click="lightboxOpen = false"
                                aria-label="Tutup galeri"
                                class="w-11 h-11 flex items-center justify-center border border-white/30 text-white hover:bg-white hover:text-black transition">
                            <x-icon name="x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="relative w-full max-w-5xl flex items-center justify-center flex-1 min-h-0">
                        <button type="button"
                                @click="prev()"
                                aria-label="Foto sebelumnya"
                                class="absolute left-0 sm:left-2 w-11 h-11 bg-white/15 hover:bg-white hover:text-black text-white flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <img :src="images[activeIndex]"
                             alt="{{ $product->name }}"
                             class="max-h-[72vh] max-w-full object-contain">

                        <button type="button"
                                @click="next()"
                                aria-label="Foto berikutnya"
                                class="absolute right-0 sm:right-2 w-11 h-11 bg-white/15 hover:bg-white hover:text-black text-white flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex gap-2 mt-4 overflow-x-auto max-w-full pb-1">
                        @foreach($galleryImages as $idx => $imgPath)
                            <button type="button"
                                    @click="activeIndex = {{ $idx }}"
                                    :class="activeIndex === {{ $idx }} ? 'border-white opacity-100' : 'border-transparent opacity-50 hover:opacity-100'"
                                    class="w-16 h-16 flex-shrink-0 overflow-hidden border-2 transition"
                                    aria-label="Pilih foto {{ $idx + 1 }}">
                                <img src="{{ $imgPath }}"
                                     alt="{{ $product->name }} - Foto {{ $idx + 1 }}"
                                     class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

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
                         alt="{{ $item->name }}"
                         loading="lazy"
                         decoding="async"
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
