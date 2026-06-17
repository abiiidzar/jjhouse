<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run()
    {
        // Categories
        $cat1 = BlogCategory::create(['name' => 'Fashion Tips', 'slug' => 'fashion-tips']);
        $cat2 = BlogCategory::create(['name' => 'Lookbook', 'slug' => 'lookbook']);

        $author = User::find(1); // admin

        // Posts
        $post1 = BlogPost::create([
            'author_id' => $author->id,
            'category_id' => $cat1->id,
            'title' => '5 Cara Memadukan Warna untuk Tampilan Casual',
            'slug' => '5-cara-memadukan-warna',
            'excerpt' => 'Tips memadukan warna agar terlihat stylish.',
            'content' => '<p>Konten lengkap tentang tips fashion...</p>',
            'featured_image' => 'https://placehold.co/800x400',
            'published_at' => now(),
            'is_published' => true,
        ]);

        $post2 = BlogPost::create([
            'author_id' => $author->id,
            'category_id' => $cat2->id,
            'title' => 'Lookbook Musim Panas 2025',
            'slug' => 'lookbook-musim-panas-2025',
            'excerpt' => 'Inspirasi gaya untuk musim panas.',
            'content' => '<p>Koleksi lookbook terbaru...</p>',
            'featured_image' => 'https://placehold.co/800x400',
            'published_at' => now(),
            'is_published' => true,
        ]);

        // Comments
        $user = User::find(2);
        BlogComment::create([
            'post_id' => $post1->id,
            'user_id' => $user->id,
            'content' => 'Artikelnya sangat membantu!',
            'is_approved' => true,
        ]);
    }
}
