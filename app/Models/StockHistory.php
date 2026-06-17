<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_size_id',
        'user_id',
        'quantity_before',
        'quantity_after',
        'quantity_changed',
        'action',
        'notes',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
        'quantity_changed' => 'integer',
    ];

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Recent first
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope: For specific product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->whereHas('product', fn($q) => $q->where('id', $productId));
    }

    /**
     * Create history record
     */
    public static function createHistory(ProductSize $productSize, array $data)
    {
        return self::create([
            'product_size_id' => $productSize->id,
            'user_id' => $data['user_id'] ?? 1,

            // 🔥 WAJIB DIISI EXPLICIT
            'quantity_before' => $data['quantity_before'],
            'quantity_after' => $data['quantity_after'],
            'quantity_changed' => $data['quantity_after'] - $data['quantity_before'],

            'action' => $data['action'] ?? 'update',
            'notes' => $data['notes'] ?? null,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
        ]);
    }
}

