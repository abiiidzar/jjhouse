<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Category;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discounts = Discount::with('promoCodes')
            ->orderBy('priority', 'desc')
            ->paginate(15);

        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $products = Product::all();
        $types = Discount::TYPES;
        $applicableTo = Discount::APPLICABLE;

        return view('admin.discounts.create', compact('categories', 'products', 'types', 'applicableTo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed,bogo,volume,flash',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|in:all,categories,products',
            'is_active' => 'boolean',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'priority' => 'integer|min:0',
            'product_ids' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $discount = Discount::create($validated);

        // Attach products if applicable_to is 'products'
        if ($validated['applicable_to'] === 'products' && $request->has('product_ids')) {
            $discount->products()->sync($request->input('product_ids'));
        }

        $this->discountService->clearCache();

        return redirect()->route('admin.discounts.show', $discount)
            ->with('success', 'Diskon berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        $discount->load('promoCodes', 'discountProducts.product');

        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        $categories = Category::all();
        $products = Product::all();
        $types = Discount::TYPES;
        $applicableTo = Discount::APPLICABLE;
        $selectedProducts = $discount->products()->pluck('product_id')->toArray();

        return view('admin.discounts.edit', compact('discount', 'categories', 'products', 'types', 'applicableTo', 'selectedProducts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed,bogo,volume,flash',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|in:all,categories,products',
            'is_active' => 'boolean',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'priority' => 'integer|min:0',
            'product_ids' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $discount->update($validated);

        // Sync products if applicable_to is 'products'
        if ($validated['applicable_to'] === 'products') {
            $discount->products()->sync($request->input('product_ids', []));
        } else {
            $discount->products()->detach();
        }

        $this->discountService->clearCache();

        return redirect()->route('admin.discounts.show', $discount)
            ->with('success', 'Diskon berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();
        $this->discountService->clearCache();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Diskon berhasil dihapus');
    }

    /**
     * Toggle discount status
     */
    public function toggleStatus(Discount $discount)
    {
        $discount->update(['is_active' => !$discount->is_active]);
        $this->discountService->clearCache();

        $status = $discount->is_active ? 'aktif' : 'tidak aktif';

        return back()->with('success', "Diskon sekarang $status");
    }
}
