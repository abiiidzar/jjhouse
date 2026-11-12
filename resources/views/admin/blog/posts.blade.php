@extends('layouts.admin')

@section('title', 'Blog Posts')
@section('header', 'Blog Management')
@section('subheader', 'Manage your blog posts')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="flex gap-3">
            <a href="{{ route('admin.blog.posts.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 transition">+ New Post</a>
        </div>
        <div class="relative">
            <input type="text" placeholder="Search posts..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-64">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($posts as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden">
                                @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="font-medium">{{ $post->title }}</p>
                                <p class="text-xs text-gray-400">{{ Str::limit($post->excerpt, 60) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $post->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $post->author->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.blog.posts.edit', $post) }}"
                               title="Edit"
                               aria-label="Edit"
                               class="p-1.5 rounded-lg text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition inline-flex items-center justify-center">
                                <x-icon name="pencil-square" class="w-4 h-4" />
                                <span class="sr-only">Edit</span>
                            </a>
                            <form action="{{ route('admin.blog.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        title="Delete"
                                        aria-label="Delete"
                                        class="p-1.5 rounded-lg text-red-600 hover:text-red-900 hover:bg-red-50 transition inline-flex items-center justify-center">
                                    <x-icon name="trash" class="w-4 h-4" />
                                    <span class="sr-only">Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No posts found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
