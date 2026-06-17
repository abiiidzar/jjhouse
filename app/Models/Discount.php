<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'applicable_to',
        'is_active',
        'starts_at',
        'ends_at',
        'priority',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
    ];

    const TYPES = [
        'percentage' => 'Persentase (%)',
        'fixed' => 'Fixed (Rp)',
        'bogo' => 'Buy One Get One',
        'volume' => 'Volume Discount',
        'flash' => 'Flash Sale',
    ];

    const APPLICABLE = [
        'all' => 'Semua Produk',
        'categories' => 'Kategori Tertentu',
        'products' => 'Produk Tertentu',
    ];

    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_products')
            ->withPivot('quantity_required', 'quantity_free')
            ->withTimestamps();
    }

    public function discountProducts()
    {
        return $this->hasMany(DiscountProduct::class);
    }

    public function isActive()
    {
        return $this->is_active && now()->isBetween($this->starts_at, $this->ends_at ?? now()->addYears(100));
    }

    public function isExpired()
    {
        return $this->ends_at && now()->isAfter($this->ends_at);
    }
}
