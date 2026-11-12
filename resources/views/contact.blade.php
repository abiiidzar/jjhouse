@extends('layouts.app')

@section('title', 'Contact - JJHOUSE')
@section('description', 'Get in touch with JJHOUSE for order support, product questions, or collaborations.')

@section('content')
<section data-theme="dark" class="bg-black text-white pt-32 pb-20 px-6 sm:px-10">
    <div class="max-w-5xl mx-auto text-center">
        <p class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-4">( Contact )</p>
        <h1 class="text-4xl md:text-6xl font-bold tracking-tighter mb-6">GET IN TOUCH</h1>
        <p class="text-gray-300 text-base md:text-lg max-w-2xl mx-auto leading-relaxed">
            Questions about orders, sizing, pre-orders, or collaborations? Reach out to the JJHOUSE team.
        </p>
    </div>
</section>

<section data-theme="light" class="bg-white text-black py-16 sm:py-24 px-6 sm:px-10">
    <div class="max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="border border-gray-200 p-8">
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">Location</p>
                <h2 class="text-lg font-semibold mb-2">Bukittinggi, Indonesia</h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Operations based in Bukittinggi. Designed in Bukittinggi.
                </p>
            </div>

            <div class="border border-gray-200 p-8">
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">Email</p>
                <h2 class="text-lg font-semibold mb-2">Customer &amp; Press</h2>
                <a href="mailto:hello@jjhouse.com" class="inline-flex items-center text-sm text-black underline underline-offset-4 hover:opacity-70 transition">
                    hello@jjhouse.com
                </a>
            </div>

            <div class="border border-gray-200 p-8">
                <p class="text-xs uppercase tracking-[0.25em] text-gray-400 mb-3">Phone / WhatsApp</p>
                <h2 class="text-lg font-semibold mb-2">Direct Support</h2>
                <a href="https://wa.me/6281372065205" class="inline-flex items-center text-sm text-black underline underline-offset-4 hover:opacity-70 transition">
                    +62 813 7206 5205
                </a>
            </div>
        </div>

        <div class="mt-12 border border-gray-200 p-8 sm:p-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 bg-gray-50">
            <div>
                <h2 class="text-lg font-semibold mb-1">Looking for an existing order?</h2>
                <p class="text-sm text-gray-600">
                    Sign in to view your order status, payment details, and tracking updates.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-black text-white text-xs uppercase tracking-widest hover:bg-gray-800 transition">
                        My Orders
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-6 py-3 bg-black text-white text-xs uppercase tracking-widest hover:bg-gray-800 transition">
                        Login
                    </a>
                @endauth
                <a href="{{ route('shop') }}"
                   class="inline-flex items-center px-6 py-3 border border-black text-black text-xs uppercase tracking-widest hover:bg-black hover:text-white transition">
                    Back to Shop
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

