<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'cost_price',
        'stock',
        'is_preorder',
        'preorder_end_date',
        'preorder_estimated_days',
        'is_active',
        'is_featured',
        'is_featured_home',
    ];

    protected $casts = [
        'preorder_end_date' => 'datetime',
        'is_preorder' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_featured_home' => 'boolean',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes()
    {
            return $this->hasMany(ProductSize::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_products')
            ->withPivot('quantity_required', 'quantity_free')
            ->withTimestamps();
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function stockAlerts()
    {
        return $this->hasMany(StockAlert::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedCostPriceAttribute()
    {
        if (!$this->cost_price) return '-';
        return 'Rp ' . number_format($this->cost_price, 0, ',', '.');
    }

    public function getProfitAttribute()
    {
        if (!$this->cost_price) return null;
        return $this->price - $this->cost_price;
    }

    public function getFormattedProfitAttribute()
    {
        if (!$this->profit) return '-';
        return 'Rp ' . number_format($this->profit, 0, ',', '.');
    }

    public function getProfitMarginPercentAttribute()
    {
        if (!$this->cost_price || $this->cost_price == 0) return 0;
        return ($this->profit / $this->cost_price) * 100;
    }

    public function getDiscountPercentageAttribute()
    {
        // If you have discount feature
        return null;
    }

    public function getIsPreorderActiveAttribute()
    {
        if (!$this->is_preorder) return false;
        if (!$this->preorder_end_date) return true;
        return now()->lessThan($this->preorder_end_date);
    }

    public function getPreorderStatusBadgeAttribute()
    {
        if (!$this->is_preorder) return null;
        if ($this->is_preorder_active) {
            return ['text' => 'PRE-ORDER', 'color' => 'yellow'];
        }
        return ['text' => 'PRE-ORDER CLOSED', 'color' => 'gray'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePreorder($query)
    {
        return $query->where('is_preorder', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_preorder', false);
    }
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }
    protected static function booted()
    {
        static::updating(function ($product) {

            if ($product->isDirty('price')) {

                $oldPrice = $product->getOriginal('price');
                $newPrice = $product->price;

                if ($newPrice < $oldPrice) {

                    \App\Jobs\SendPriceDrop::dispatch($product, $oldPrice);
                }
            }
        });
    }

}
