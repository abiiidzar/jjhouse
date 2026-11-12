@extends('layouts.app')

@section('title', 'About Us - JJHOUSE')
@section('description', 'Learn more about JJHOUSE — streetwear brand based in Jakarta, designed in Bukittinggi.')

@section('content')
<section data-theme="dark" class="bg-black text-white pt-32 pb-20 px-6 sm:px-10">
    <div class="max-w-5xl mx-auto text-center">
        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-4">( About Us )</p>
        <h1 class="text-4xl md:text-6xl font-bold tracking-tighter mb-6">JJHOUSE</h1>
        <p class="text-gray-300 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
            Streetwear brand based in Jakarta, designed in Bukittinggi. Modern, functional clothing made for the bold and the brave.
        </p>
    </div>
</section>

<section data-theme="light" class="bg-white text-black py-16 sm:py-24 px-6 sm:px-10">
    <div class="max-w-5xl mx-auto space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">Our Story</p>
                <h2 class="text-2xl md:text-3xl font-semibold tracking-tight leading-snug">
                    Rooted in authenticity, shaped by street culture.
                </h2>
            </div>
            <div class="space-y-4 text-sm md:text-base text-gray-600 leading-relaxed">
                <p>
                    Born from a shared love of the game and rooted in a vision of authenticity and creativity expressed through what we wear, JJHOUSE blends minimalist silhouettes with durable everyday utility.
                </p>
                <p>
                    From small-batch seasonal drops to thoughtfully crafted essentials, every piece is designed in Bukittinggi and brought to life for our community across Indonesia.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-gray-200 pt-12">
            <div class="p-6 bg-gray-50">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Design</p>
                <h3 class="font-semibold mb-2">Functional Silhouettes</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Clean cuts and versatile layers built to move seamlessly between daily routines.
                </p>
            </div>
            <div class="p-6 bg-gray-50">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Craft</p>
                <h3 class="font-semibold mb-2">Small-Batch Focus</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Focused releases and curated pre-orders with attention to fabric, fit, and finish.
                </p>
            </div>
            <div class="p-6 bg-gray-50">
                <p class="text-xs uppercase tracking-[0.2em] text-gray-400 mb-2">Culture</p>
                <h3 class="font-semibold mb-2">Community Driven</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Inspired by the creatives, athletes, and friends who wear JJHOUSE every day.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <a href="{{ route('shop') }}"
               class="inline-flex items-center px-6 py-3 bg-black text-white text-xs uppercase tracking-widest hover:bg-gray-800 transition">
                Browse Shop
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center px-6 py-3 border border-black text-black text-xs uppercase tracking-widest hover:bg-black hover:text-white transition">
                Contact Us
            </a>
        </div>
    </div>
</section>
@endsection

