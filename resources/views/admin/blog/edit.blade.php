@extends('layouts.admin')

@section('title', 'Edit Post')
@section('header', 'Edit Post')
@section('subheader', 'Update your blog post')

@section('content')
<form action="{{ route('admin.blog.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title *</label>
                        <input type="text" name="title" value="{{ $post->title }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Excerpt *</label>
                        <textarea name="excerpt" rows="3" required class="w-full border border-gray-300 rounded-md px-3 py-2">{{ $post->excerpt }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Content *</label>
                        <textarea name="content" id="editor" rows="15" required class="w-full border border-gray-300 rounded-md px-3 py-2">{!! $post->content !!}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold mb-4">Publishing</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Category</label>
                        <select name="category_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">Uncategorized</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $post->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Current Image</label>
                        @if($post->featured_image)
                        <img src="{{ $post->featured_image }}" class="w-full h-32 object-cover rounded mb-2">
                        @endif
                        <input type="file" name="featured_image" accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }}>
                            <span class="text-sm">Published</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Publish Date</label>
                        <input type="datetime-local" name="published_at" value="{{ $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '' }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold mb-4">SEO</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ $post->meta_title }}" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2">{{ $post->meta_description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800">Update Post</button>
                <a href="{{ route('admin.blog.posts') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Simple rich text editor
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('editor');
        if (editor) {
            editor.style.minHeight = '400px';
            editor.style.borderRadius = '0.375rem';
        }
    });
</script>
@endpush
@endsection
