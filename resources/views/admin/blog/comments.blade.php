@extends('layouts.admin')

@section('title', 'Blog Comments')
@section('header', 'Comment Moderation')
@section('subheader', 'Approve or delete blog comments')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($comments as $comment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="text-sm">{{ Str::limit($comment->content, 80) }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ Str::limit($comment->post->title, 30) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $comment->user->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $comment->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5">
                            @if(!$comment->is_approved)
                            <form action="{{ route('admin.blog.comments.approve', $comment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        title="Approve"
                                        aria-label="Approve"
                                        class="p-1.5 rounded-lg text-green-600 hover:text-green-900 hover:bg-green-50 transition inline-flex items-center justify-center">
                                    <x-icon name="check-circle" class="w-4 h-4" />
                                    <span class="sr-only">Approve</span>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.blog.comments.delete', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?')">
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
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No comments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $comments->links() }}
    </div>
</div>
@endsection
