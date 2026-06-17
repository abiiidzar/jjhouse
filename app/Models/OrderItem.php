<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_size_id',
        'quantity',
        'price',
        'is_preorder',
        'preorder_payment_type',
        'down_payment_amount',
        'remaining_payment',
        'preorder_ready_at',
    ];

    protected $casts = [
        'is_preorder' => 'boolean',
        'preorder_ready_at' => 'datetime',
        'price' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
        'remaining_payment' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getCostAttribute()
    {
        // Get cost from product at time of order
        $costPrice = $this->product?->cost_price ?? 0;
        return $costPrice * $this->quantity;
    }

    public function getProfitAttribute()
    {
        return $this->subtotal - $this->cost;
    }

    public function getPaymentTypeLabelAttribute()
    {
        if (!$this->is_preorder) return '-';
        return $this->preorder_payment_type == 'dp' ? 'DP 50%' : 'Full Payment';
    }

    public function getIsReadyToShipAttribute()
    {
        return $this->is_preorder && $this->preorder_ready_at !== null;
    }

    public function getRemainingPaymentFormattedAttribute()
    {
        return 'Rp ' . number_format($this->remaining_payment, 0, ',', '.');
    }

    public function scopePreorder($query)
    {
        return $query->where('is_preorder', true);
    }

    public function scopeReadyToShip($query)
    {
        return $query->where('is_preorder', true)->whereNotNull('preorder_ready_at');
    }
}
