@extends('layouts.app')

@section('title', $post->title . ' - JJHOUSE')
@section('description', $post->meta_description ?? $post->excerpt)
@section('og_title', $post->title)
@section('og_description', $post->meta_description ?? $post->excerpt)
@section('og_image', $post->featured_image)

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-3 text-sm text-gray-500 mb-4">
            <span>{{ $post->published_at->format('M d, Y') }}</span>
            <span>•</span>
            <span>{{ $post->category->name ?? 'Uncategorized' }}</span>
            <span>•</span>
            <span>{{ $post->reading_time }}</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-bold mb-4">{{ $post->title }}</h1>
        <div class="flex items-center justify-center gap-2">
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm">
                {{ substr($post->author->name, 0, 1) }}
            </div>
            <span class="text-gray-600">By {{ $post->author->name }}</span>
        </div>
    </div>

    <!-- Featured Image -->
    @if($post->featured_image)
    <div class="mb-8 rounded-xl overflow-hidden">
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full">
    </div>
    @endif

    <!-- Content -->
    <div class="prose prose-lg max-w-none mb-12">
        {!! $post->content !!}
    </div>

    <!-- Share Buttons -->
    <div class="border-t border-b border-gray-200 py-6 my-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <span class="font-medium">Share this article</span>
            <div class="flex gap-3">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}"
                   target="_blank" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                   target="_blank" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.99h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.99C18.343 21.128 22 16.991 22 12z"/></svg>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
                   target="_blank" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451c.979 0 1.771-.773 1.771-1.729V1.729C24 .774 23.203 0 22.225 0z"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Related Posts -->
    @if($relatedPosts->count() > 0)
    <div class="mb-12">
        <h3 class="text-xl font-bold mb-6">Related Articles</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="group">
                <div class="overflow-hidden rounded-lg mb-3">
                    <img src="{{ $related->featured_image ?? 'https://placehold.co/400x300' }}" class="w-full h-40 object-cover group-hover:scale-105 transition">
                </div>
                <h4 class="font-medium group-hover:text-gray-600">{{ $related->title }}</h4>
                <p class="text-sm text-gray-500">{{ $related->published_at->format('M d, Y') }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Comments Section -->
    @livewire('blog-comment-section', ['post' => $post])
</div>
@endsection
