<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('is_published', true)
            ->whereNotNull('published_at')
            ->with('author', 'category')
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = BlogPost::where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $categories = \App\Models\BlogCategory::withCount('posts')->get();

        return view('blog.index', compact('posts', 'recentPosts', 'categories'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->with('author', 'category')
            ->firstOrFail();

        // Increment view count if you have that column
        // $post->increment('views');

        $relatedPosts = BlogPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function feed()
    {
        $posts = BlogPost::where('is_published', true)
            ->latest('published_at')
            ->take(20)
            ->get();

        return response()->view('blog.rss', compact('posts'))->header('Content-Type', 'text/xml');
    }

    public function storeComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:blog_posts,id',
            'content' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        BlogComment::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return redirect()->back()->with('success', 'Komentar Anda akan tampil setelah disetujui admin.');
    }
}
