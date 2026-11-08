<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Di ShopController atau controller produk Anda
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter kategori
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
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
