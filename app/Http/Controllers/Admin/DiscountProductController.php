<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountProduct;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountProductController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index(Request $request)
    {
        $discounts = Discount::orderBy('name')->get();

        $discountProducts = DiscountProduct::with(['discount', 'product'])
            ->when($request->filled('discount_id'), function ($query) use ($request) {
                return $query->where('discount_id', $request->discount_id);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.discount-products.index', compact('discountProducts', 'discounts'));
    }

    public function create()
    {
        $discounts = Discount::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.discount-products.create', compact('discounts', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateDiscountProduct($request);

        DiscountProduct::create($validated);
        $this->discountService->clearCache();

        return redirect()->route('admin.discount-products.index')
            ->with('success', 'Produk diskon berhasil dibuat');
    }

    public function edit(DiscountProduct $discountProduct)
    {
        $discounts = Discount::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('admin.discount-products.edit', compact('discountProduct', 'discounts', 'products'));
    }

    public function update(Request $request, DiscountProduct $discountProduct)
    {
        $validated = $this->validateDiscountProduct($request, $discountProduct);

        $discountProduct->update($validated);
        $this->discountService->clearCache();

        return redirect()->route('admin.discount-products.index')
            ->with('success', 'Produk diskon berhasil diperbarui');
    }

    public function destroy(DiscountProduct $discountProduct)
    {
        $discountProduct->delete();
        $this->discountService->clearCache();

        return redirect()->route('admin.discount-products.index')
            ->with('success', 'Produk diskon berhasil dihapus');
    }

    private function validateDiscountProduct(Request $request, ?DiscountProduct $discountProduct = null): array
    {
        return $request->validate([
            'discount_id' => ['required', 'exists:discounts,id'],
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('discount_products')
                    ->where('discount_id', $request->discount_id)
                    ->ignore($discountProduct ? $discountProduct->id : null),
            ],
            'quantity_required' => ['nullable', 'integer', 'min:1'],
            'quantity_free' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
