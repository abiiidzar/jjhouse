@extends('layouts.app')

@section('content')

<section class="px-10 py-32 text-center">

    <h1 class="text-3xl uppercase tracking-widest mb-6">
        Payment Status
    </h1>

    <p class="text-gray-500">
        Your payment is being processed...
    </p>

    <a href="{{ route('home') }}"
       class="inline-block mt-10 border px-6 py-3 uppercase text-xs tracking-widest hover:bg-black hover:text-white transition">
        Back to Home
    </a>

</section>

@endsection