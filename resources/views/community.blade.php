@extends('layouts.app')

@section('title', 'Community - JJHOUSE')
@section('description', 'Connect with the JJHOUSE community — streetwear culture, journal stories, and upcoming drops.')

@section('content')
<section data-theme="dark" class="bg-black text-white pt-32 pb-20 px-6 sm:px-10">
    <div class="max-w-5xl mx-auto text-center">
        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-4">( Community )</p>
        <h1 class="text-4xl md:text-6xl font-bold tracking-tighter mb-6">JOIN THE MOVEMENT</h1>
        <p class="text-gray-300 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
            Built around authenticity, creativity, and everyday streetwear culture across Jakarta, Bukittinggi, and beyond.
        </p>
    </div>
</section>

<section data-theme="light" class="bg-white text-black py-16 sm:py-24 px-6 sm:px-10">
    <div class="max-w-6xl mx-auto space-y-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="border border-gray-200 p-8 flex flex-col justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">01 / Journal</p>
                    <h2 class="text-xl font-semibold mb-3">Stories &amp; Style Guides</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Read editorial notes, behind-the-scenes lookbooks, and cultural stories from the JJHOUSE Journal.
                    </p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('blog.index') }}"
                       class="inline-flex items-center text-xs uppercase tracking-widest border border-black px-5 py-3 hover:bg-black hover:text-white transition">
                        Explore Journal
                    </a>
                </div>
            </div>

            <div class="border border-gray-200 p-8 flex flex-col justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">02 / Drops</p>
                    <h2 class="text-xl font-semibold mb-3">Latest Collections</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Discover limited seasonal pieces, essentials, and pre-order releases crafted for everyday movement.
                    </p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('shop') }}"
                       class="inline-flex items-center text-xs uppercase tracking-widest border border-black px-5 py-3 hover:bg-black hover:text-white transition">
                        Shop Collection
                    </a>
                </div>
            </div>

            <div class="border border-gray-200 p-8 flex flex-col justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">03 / Connect</p>
                    <h2 class="text-xl font-semibold mb-3">Collaborate With Us</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Have an event, creative project, or community collaboration in mind? Reach out directly to our team.
                    </p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('contact') }}"
                       class="inline-flex items-center text-xs uppercase tracking-widest border border-black px-5 py-3 hover:bg-black hover:text-white transition">
                        Get in Touch
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 p-8 sm:p-12 text-center max-w-3xl mx-auto">
            <h2 class="text-2xl font-semibold tracking-tight mb-3">Early Access &amp; Community Updates</h2>
            <p class="text-sm text-gray-600 mb-6">
                Subscribe to hear first about upcoming drops, limited pre-orders, and community gatherings.
            </p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                @csrf
                <input type="email"
                       name="email"
                       required
                       placeholder="Your email address"
                       class="flex-1 px-4 py-3 bg-white border border-gray-300 focus:border-black focus:outline-none text-sm">
                <button type="submit"
                        class="px-6 py-3 bg-black text-white text-xs uppercase tracking-widest font-medium hover:bg-gray-800 transition">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

