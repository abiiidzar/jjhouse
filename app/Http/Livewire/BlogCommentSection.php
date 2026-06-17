<?php

namespace App\Http\Livewire;

use App\Models\BlogComment;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class BlogCommentSection extends Component
{
    use WithPagination;

    public $post;
    public $content;
    public $parentId = null;
    public $replyToName = null;

    protected $rules = [
        'content' => 'required|min:3',
    ];

    public function mount($post)
    {
        $this->post = $post;
    }

    public function submitComment()
    {
        $this->validate();

        if (!Auth::check()) {
            session()->flash('comment_error', 'Silakan login terlebih dahulu');
            return;
        }

        BlogComment::create([
            'post_id' => $this->post->id,
            'user_id' => Auth::user()->id,
            'parent_id' => $this->parentId,
            'content' => $this->content,
            'is_approved' => false,
        ]);

        $this->content = '';
        $this->parentId = null;
        $this->replyToName = null;
        session()->flash('comment_success', 'Komentar Anda akan tampil setelah disetujui.');
        $this->resetPage();
    }

    public function replyTo($commentId, $userName)
    {
        $this->parentId = $commentId;
        $this->replyToName = $userName;
        $this->content = '@' . $userName . ' ';
    }

    public function cancelReply()
    {
        $this->parentId = null;
        $this->replyToName = null;
        $this->content = '';
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
