@extends('layouts.app')

@section('title', 'Journal - JJHOUSE')
@section('description', 'Read the latest articles about fashion, streetwear culture, and JJHOUSE updates.')

@section('content')
<!-- Hero Section -->
<section data-theme="dark" class="bg-black text-white pt-32 pb-20">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold tracking-tighter mb-4">THE JOURNAL</h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">Stories, style guides, and cultural commentary from the JJHOUSE universe.</p>
        <div class="mt-6">
            <a href="{{ route('blog.feed') }}" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-gray-400 hover:text-white transition">
                RSS Feed
            </a>
        </div>
    </div>
</section>

<div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="space-y-12">
                @forelse($posts as $post)
                <article class="group">
                    <a href="{{ route('blog.show', $post->slug) }}">
                        <div class="overflow-hidden rounded-lg mb-4">
                            <img src="{{ $post->featured_image ?? 'https://placehold.co/800x500' }}"
                                 alt="{{ $post->title }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-64 md:h-80 object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $post->category->name ?? 'Uncategorized' }}</span>
                                <span>•</span>
                                <span>{{ $post->reading_time }}</span>
                            </div>
                            <h2 class="text-2xl font-bold group-hover:text-gray-600 transition">{{ $post->title }}</h2>
                            <p class="text-gray-600">{{ $post->excerpt }}</p>
                            <div class="flex items-center gap-2 text-sm font-medium mt-3">
                                <span>Read more</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </article>
                @empty
                <div class="text-center py-12">
                    <p class="text-gray-500">No posts found yet.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Search -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="font-bold text-lg mb-4">Search</h3>
                <form action="{{ route('blog.index') }}" method="GET" class="relative">
                    <input type="search" name="search" placeholder="Search articles..."
                           class="w-full px-4 py-3 border border-gray-200 focus:border-black focus:outline-none rounded-lg">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Categories -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="font-bold text-lg mb-4">Categories</h3>
                <ul class="space-y-2">
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('blog.index', ['category' => $cat->slug]) }}"
                           class="flex justify-between items-center text-gray-600 hover:text-black transition">
                            <span>{{ $cat->name }}</span>
                            <span class="text-sm text-gray-400">{{ $cat->posts_count }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Recent Posts -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="font-bold text-lg mb-4">Recent Posts</h3>
                <div class="space-y-4">
                    @foreach($recentPosts as $recent)
                    <a href="{{ route('blog.show', $recent->slug) }}" class="flex gap-3 group">
                        <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                            <img src="{{ $recent->featured_image ?? 'https://placehold.co/100x100' }}" alt="{{ $recent->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-medium text-sm group-hover:text-gray-600 line-clamp-2">{{ $recent->title }}</h4>
                            <p class="text-xs text-gray-400 mt-1">{{ $recent->published_at->format('M d, Y') }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Newsletter -->
            <div class="bg-black text-white p-6 rounded-lg">
                <h3 class="font-bold text-lg mb-2">Subscribe</h3>
                <p class="text-gray-400 text-sm mb-4">Get the latest drops and stories straight to your inbox.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="email" name="email" placeholder="Your email address"
                           class="w-full px-4 py-2 bg-white/10 border border-white/20 focus:border-white focus:outline-none text-white placeholder-gray-400 rounded">
                    <button type="submit" class="w-full bg-white text-black py-2 font-medium hover:bg-gray-200 transition rounded">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
