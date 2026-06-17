<?php

namespace App\Http\Livewire;

use App\Models\BlogComment;
use Livewire\Component;
use Livewire\WithPagination;

class BlogCommentSection extends Component
{
    use WithPagination;

    public $post;
    public $content;
    public $parentId = null;

    protected $rules = [
        'content' => 'required|min:3',
    ];

    public function submitComment()
    {
        $this->validate();

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        BlogComment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->parentId,
            'content' => $this->content,
            'is_approved' => false, // perlu moderasi admin
        ]);

        $this->content = '';
        $this->parentId = null;
        session()->flash('comment_success', 'Komentar Anda akan tampil setelah disetujui.');
        $this->resetPage();
    }

    public function replyTo($commentId)
    {
        $this->parentId = $commentId;
    }

    public function render()
    {
        $comments = BlogComment::where('post_id', $this->post->id)
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->with('user', 'replies.user')
            ->latest()
            ->paginate(10);

        return view('livewire.blog-comment-section', [
            'comments' => $comments,
        ]);
    }
}
