<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\StockHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\StockAlert;
use App\Jobs\SendRestockNotification;



class ProductSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_size_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_size_id');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function decreaseStock($quantity, string $notes = 'Order checkout', ?int $orderId = null)
    {

    $before = $this->stock;

    $this->decrement('stock', $quantity);

    $this->refresh(); // 🔥 penting biar dapet value terbaru

    $after = $this->stock;

    StockHistory::createHistory($this, [
        'product_size_id' => $this->id,
        'user_id' => Auth::id(),
        'action' => 'sold',
        'quantity_before' => $before,
        'quantity_after' => $after,
        'notes' => $notes,
        'reference_type' => 'order',
        'reference_id' => $orderId,
    ]);
    }

    public function restoreStockFromOrder(int $quantity, int $orderId, string $reason): void
    {
        $before = $this->stock;
        $this->increment('stock', $quantity);
        $this->refresh();

        StockHistory::createHistory($this, [
            'user_id' => Auth::id(),
            'action' => 'restock',
            'quantity_before' => $before,
            'quantity_after' => $this->stock,
            'notes' => $reason,
            'reference_type' => 'order',
            'reference_id' => $orderId,
        ]);
    }

    public function syncProductStock()
    {
        // Update product stock dengan total stok semua sizes
        $totalStock = $this->product->sizes()->sum('stock');
        $this->product->update(['stock' => $totalStock]);
    }

    public function increaseStock($quantity)
    {
            $before = $this->stock;

    $this->increment('stock', $quantity);
    if ($this->stock > 0) {
        SendRestockNotification::dispatch($this);
    }

    $after = $this->stock;


    StockHistory::createHistory($this, [
        'user_id' => Auth::id(),
        'action' => 'restock',
        'quantity_before' => $before,
        'quantity_after' => $after,
        'notes' => 'Restock produk',
        'reference_type' => 'manual',
    ]);
    }
}
