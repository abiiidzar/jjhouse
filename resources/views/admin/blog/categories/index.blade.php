@extends('layouts.admin')

@section('title', 'Blog Categories')
@section('header', 'Blog Categories')
@section('subheader', 'Manage your blog categories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Add Category Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="font-semibold mb-4">Add New Category</h3>
        <form action="{{ route('admin.blog.categories.store') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <div>
                    <input type="text" name="name" placeholder="Category Name" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                </div>
                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800">Add Category</button>
            </div>
        </form>
    </div>

    <!-- Categories List -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-semibold">Categories</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($categories as $category)
            <div class="p-4 flex justify-between items-center">
                <div>
                    <p class="font-medium">{{ $category->name }}</p>
                    <p class="text-xs text-gray-400">{{ $category->posts_count }} posts</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button"
                            onclick="editCategory({{ $category->id }}, '{{ $category->name }}')"
                            title="Edit"
                            aria-label="Edit"
                            class="p-1.5 rounded-lg text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition inline-flex items-center justify-center">
                        <x-icon name="pencil-square" class="w-4 h-4" />
                        <span class="sr-only">Edit</span>
                    </button>
                    <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
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
            </div>
            @empty
            <div class="p-8 text-center text-gray-400">No categories yet</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="font-semibold mb-4">Edit Category</h3>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <input type="text" name="name" id="editName" class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4">
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gray-900 text-white px-4 py-2 rounded-md">Update</button>
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editCategory(id, name) {
        document.getElementById('editName').value = name;
        document.getElementById('editForm').action = `/admin/blog/categories/${id}`;
        document.getElementById('editModal').classList.add('flex');
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeModal() {
        document.getElementById('editModal').classList.remove('flex');
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
