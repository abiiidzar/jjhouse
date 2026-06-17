<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\BlogPost;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured_home products first (set by admin from dashboard)
        $featuredCount = Setting::get('home_featured_products_count', 8);
        
        $products = Product::where('is_active', true)
            ->where('is_featured_home', true)
            ->with('primaryImage')
            ->latest()
            ->limit($featuredCount)
            ->get();

        // Fallback: if no featured_home products, show latest active products
        if ($products->isEmpty()) {
            $products = Product::where('is_active', true)
                ->with('primaryImage')
                ->latest()
                ->limit($featuredCount)
                ->get();
        }

        // Get latest blog posts
        $posts = BlogPost::where('is_published', true)
            ->with('author', 'category')
            ->latest()
            ->limit(3)
            ->get();

        // Get home page images from settings
        $homeImages = [
            'hero_image_1' => Setting::get('home_hero_image_1') ?? '/images/hero.jpg',
            'hero_image_2' => Setting::get('home_hero_image_2') ?? '/images/hero.jpg',
            'hero_image_3' => Setting::get('home_hero_image_3') ?? '/images/hero.jpg',
            'story_image' => Setting::get('home_story_image') ?? '/images/story.jpg',
            'editorial_image_1' => Setting::get('home_editorial_image_1') ?? '/images/editorial1.jpg',
            'editorial_image_2' => Setting::get('home_editorial_image_2') ?? '/images/editorial2.jpg',
            'editorial_image_3' => Setting::get('home_editorial_image_3') ?? '/images/editorial3.jpg',
        ];

        // Get home page visibility settings
        $homeSettings = [
            'show_story_section' => Setting::get('home_show_story_section', true),
            'show_featured_products' => Setting::get('home_show_featured_products', true),
            'show_editorial_grid' => Setting::get('home_show_editorial_grid', true),
        ];

        return view('home', compact('products', 'posts', 'homeImages', 'homeSettings'));
    }
}
