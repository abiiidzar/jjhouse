<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountProduct extends Model
{
    protected $fillable = [
        'discount_id',
        'product_id',
        'quantity_required',
        'quantity_free',
    ];

    protected $casts = [
        'quantity_required' => 'integer',
        'quantity_free' => 'integer',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
