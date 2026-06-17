<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::where('is_active', '=', true)->with('primaryImage');

        // FILTER CATEGORY
        if (request('category')) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', '=', request('category'));
            });
        }

        // SORTING
        if (request('sort') == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif (request('sort') == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12);

        // optional (buat filter UI)
        $categories = Category::all();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', '=', $slug)
            ->where('is_active', '=', true)
            ->with('category', 'sizes', 'images', 'primaryImage')
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', '=', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', '=', true)
            ->with('primaryImage')
            ->take(4)
            ->get();

        return view('product.product-detail', compact('product', 'relatedProducts'));
    }

    public function quickView(int $id)
    {
        $product = Product::where('id', '=', $id)
            ->where('is_active', '=', true)
            ->with('sizes', 'images')
            ->firstOrFail();

        return view('partials.quick-view', compact('product'));
    }
}
