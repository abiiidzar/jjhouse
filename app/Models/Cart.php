<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getSubtotalAttribute()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }

    public function getPreorderItemsAttribute()
    {
        return $this->items->where('is_preorder', true);
    }

    public function getRegularItemsAttribute()
    {
        return $this->items->where('is_preorder', false);
    }

    public function clear()
    {
        $this->items()->delete();
    }
}
