<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class BlogController extends Controller
{
    
    public function index()
    {
        $posts = BlogPost::with('author', 'category')->latest()->paginate(15);
        return view('admin.blog.posts', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }

    public function createPost()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'required',
            'excerpt' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $featuredImage = null;
        if ($request->hasFile('featured_image')) {
            $featuredImage = $request->file('featured_image')->store('blog', 'public');
            $featuredImage = '/storage/' . $featuredImage;
        }

        $post = BlogPost::create([
            'author_id' => Auth::user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . uniqid(),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $featuredImage,
            'published_at' => $request->published_at ?? ($request->is_published ? now() : null),
            'is_published' => $request->is_published ?? false,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()->route('admin.blog.posts')->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();
        return view('admin.blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'required',
            'excerpt' => 'required',
            'featured_image' => 'nullable|image|max:2048',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            $featuredImage = $request->file('featured_image')->store('blog', 'public');
            $post->featured_image = '/storage/' . $featuredImage;
        }

        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'is_published' => $request->is_published ?? false,
            'published_at' => $request->published_at ?? ($request->is_published ? now() : null),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return redirect()->route('admin.blog.posts')->with('success', 'Post updated successfully.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }

    // Categories
    public function categories()
    {
        $categories = BlogCategory::withCount('posts')->get();
        return view('admin.blog.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:blog_categories'
        ]);

        BlogCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $request->validate([
            'name' => 'required|string|unique:blog_categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(BlogCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    // Comments moderation
    public function comments()
    {
        $comments = BlogComment::with('user', 'post')->latest()->paginate(20);
        return view('admin.blog.comments', compact('comments'));
    }

    public function approveComment(BlogComment $comment)
    {
        $comment->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Comment approved successfully.');
    }

    public function deleteComment(BlogComment $comment)
    {
        $comment->delete();
        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
