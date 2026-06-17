<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'discount_id',
        'code',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if (!now()->isBetween($this->valid_from, $this->valid_until ?? now()->addYears(100))) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return $this->discount->isActive();
    }

    public function isExpired()
    {
        return $this->valid_until && now()->isAfter($this->valid_until);
    }

    public function canBeUsed()
    {
        return $this->isValid() && !$this->isExpired();
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}
