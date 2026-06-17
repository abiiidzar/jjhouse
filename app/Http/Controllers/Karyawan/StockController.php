<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of stock.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(15);
        return view('karyawan.stock.index', compact('products'));
    }

    /**
     * Show the form for editing stock.
     */
    public function edit(Product $stock)
    {
        return view('karyawan.stock.edit', compact('stock'));
    }

    /**
     * Update the stock in storage.
     */
    public function update(Request $request, Product $stock)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldStock = $stock->stock;
        $stock->update(['stock' => $validated['stock']]);

        // TODO: Log stock changes for audit trail

        return redirect()->route('karyawan.stock.index')
            ->with('success', "Stok produk {$stock->name} diperbarui dari {$oldStock} menjadi {$validated['stock']}");
    }
}
