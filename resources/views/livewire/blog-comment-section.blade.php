<div class="blog-comment-section">
    @if(session('comment_success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('comment_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('comment_error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('comment_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="comments-section mt-4">
        <h4>Comments</h4>

        <!-- Comment Form -->
        @if(Auth::check())
            <div class="comment-form mb-4">
                <form wire:submit="submitComment">
                    <div class="mb-3">
                        @if($replyToName)
                            <button type="button" class="btn btn-sm btn-outline-secondary mb-2" wire:click="cancelReply">
                                Cancel Reply to {{ $replyToName }}
                            </button>
                        @endif
                        <textarea
                            class="form-control @error('content') is-invalid @enderror"
                            wire:model="content"
                            rows="3"
                            placeholder="Write your comment..."
                        ></textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove>Submit Comment</span>
                        <span wire:loading>Submitting...</span>
                    </button>
                </form>
            </div>
        @else
            <div class="alert alert-info mb-4">
                <a href="{{ route('login') }}">Login</a> to post a comment.
            </div>
        @endif

        <!-- Comments List -->
        <div class="comments-list">
            @forelse($comments as $comment)
                <div class="comment mb-3 ps-3 border-start">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $comment->user->name }}</strong>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <p class="mt-1 mb-0">{{ $comment->content }}</p>

                    @if(Auth::check())
                        <button
                            type="button"
                            class="btn btn-link btn-sm p-0 mt-2"
                            wire:click="replyTo({{ $comment->id }}, '{{ $comment->user->name }}')"
                        >
                            Reply
                        </button>
                    @endif

                    <!-- Nested Replies -->
                    @if($comment->replies->count() > 0)
                        <div class="replies mt-2">
                            @foreach($comment->replies as $reply)
                                <div class="reply mb-2 ps-3 border-start">
                                    <strong>{{ $reply->user->name }}</strong>
                                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                    <p class="mt-1 mb-0">{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted">No comments yet. Be the first to comment!</p>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($comments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>
