<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
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
        $promoCodes = PromoCode::with('discount')
            ->latest()
            ->paginate(15);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $discounts = Discount::where('is_active', true)->get();

        return view('admin.promo-codes.create', compact('discounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'code' => 'required|string|unique:promo_codes,code',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(PromoCode $promoCode)
    {
        $promoCode->load('discount');

        return view('admin.promo-codes.show', compact('promoCode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromoCode $promoCode)
    {
        $discounts = Discount::where('is_active', true)
            ->orWhere('id', $promoCode->discount_id)
            ->get();

        return view('admin.promo-codes.edit', compact('promoCode', 'discounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromoCode $promoCode)
    {
        $validated = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'code' => 'required|string|unique:promo_codes,code,' . $promoCode->id,
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $promoCode->update($validated);

        return redirect()->route('admin.promo-codes.show', $promoCode)
            ->with('success', 'Kode promo berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Kode promo berhasil dihapus');
    }

    /**
     * Toggle promo code status
     */
    public function toggleStatus(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);

        $status = $promoCode->is_active ? 'aktif' : 'tidak aktif';

        return back()->with('success', "Kode promo sekarang $status");
    }
}
