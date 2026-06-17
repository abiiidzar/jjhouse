<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        // 🔍 SEARCH
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔽 SORTING
        switch ($request->sort) {
            case 'category':
                $query->join('categories', 'products.category_id', '=', 'categories.id')
                    ->orderBy('categories.name', 'asc')
                    ->select('products.*');
                break;

            case 'stock':
                $query->orderBy('stock', 'desc');
                break;

            case 'latest':
            $query->latest();
            break;

            default:
                $query->orderBy('name', 'asc'); // default tetap terbaru
                break;
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_preorder' => 'boolean',
            'preorder_end_date' => 'nullable|date|after:now',
            'preorder_estimated_days' => 'nullable|integer|min:1',
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'string',
            'stock_per_size' => 'required|array',
            'primary_image' => 'required|image|max:2048',
        ]);

        $totalStock = array_sum($request->stock_per_size);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $totalStock,
            'is_preorder' => $request->is_preorder ?? false,
            'preorder_end_date' => $request->preorder_end_date,
            'preorder_estimated_days' => $request->preorder_estimated_days,
            'is_active' => $request->is_active ?? true,
            'is_featured_home' => $request->is_featured_home ?? false,
        ]);

        // Save sizes
        foreach ($request->sizes as $index => $size) {
            ProductSize::create([
                'product_id' => $product->id,
                'size' => $size,
                'stock' => $request->stock_per_size[$index] ?? 0,
            ]);
        }

        // Save primary image
        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => '/storage/' . $path,
                'is_primary' => true,
            ]);
        }

        // Save additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => '/storage/' . $path,
                    'is_primary' => false,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'sizes', 'category']);
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'                      => 'required|string|max:255',
            'category_id'               => 'required|exists:categories,id',
            'description'               => 'required',
            'price'                     => 'required|numeric|min:0',
            'cost_price'                => 'nullable|numeric|min:0',
            'is_preorder'               => 'boolean',
            'preorder_end_date'         => 'nullable|date',
            'preorder_estimated_days'   => 'nullable|integer|min:1',
            'is_active'                 => 'boolean',
            'primary_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'additional_images.*'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'is_preorder' => $request->is_preorder ?? false,
            'preorder_end_date' => $request->preorder_end_date,
            'preorder_estimated_days' => $request->preorder_estimated_days,
            'is_active' => $request->is_active ?? true,
            'is_featured_home' => $request->is_featured_home ?? false,
        ]);

        // Update sizes if provided
        if ($request->has('sizes')) {
            $product->sizes()->delete();
            $totalStock = 0;
            foreach ($request->sizes as $index => $size) {
                $stock = $request->stock_per_size[$index] ?? 0;
                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'stock' => $stock,
                ]);
                $totalStock += $stock;
            }
            $product->update(['stock' => $totalStock]);
        }

        // Replace primary image (delete old primary first)
        if ($request->hasFile('primary_image')) {
            // Delete old primary image file from disk
            $oldPrimary = $product->primaryImage;
            if ($oldPrimary) {
                $oldPath = ltrim(str_replace('/storage/', '', $oldPrimary->image_path), '/');
                Storage::disk('public')->delete($oldPath);
                $oldPrimary->delete();
            }

            $filename = uniqid() . '.' . $request->file('primary_image')->getClientOriginalExtension();
            $request->file('primary_image')->storeAs('products', $filename, 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => '/storage/products/' . $filename,
                'is_primary'  => true,
            ]);
        }

        // Upload additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $img) {
                $filename = uniqid() . '.' . $img->getClientOriginalExtension();
                $img->storeAs('products', $filename, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => '/storage/products/' . $filename,
                    'is_primary'  => false,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return redirect()->back()->with('success', 'Product status updated.');
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::findOrFail($imageId);

        // Remove physical file from storage
        $relativePath = ltrim(str_replace('/storage/', '', $image->image_path), '/');
        Storage::disk('public')->delete($relativePath);

        $image->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Gambar berhasil dihapus.');
    }
}
