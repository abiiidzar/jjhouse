<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_size_id',
        'product_id',
        'threshold',
        'current_stock',
        'status',
        'first_triggered_at',
        'acknowledged_at',
        'acknowledged_by',
        'acknowledgment_notes',
        'resolved_at',
        'restock_quantity',
    ];

    protected $casts = [
        'threshold' => 'integer',
        'current_stock' => 'integer',
        'restock_quantity' => 'integer',
        'first_triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope: Active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Acknowledged alerts
     */
    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    /**
     * Scope: Resolved alerts
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope: Recent alerts
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('first_triggered_at');
    }

    /**
     * Acknowledge the alert
     */
    public function acknowledge(User $user, $notes = null)
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'acknowledged_by' => $user->id,
            'acknowledgment_notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Resolve the alert
     */
    public function resolve($restockQuantity = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'restock_quantity' => $restockQuantity,
        ]);

        return $this;
    }

    /**
     * Create or update alert for product size
     */
    public static function checkAndCreateAlert(ProductSize $productSize, $threshold = 5)
    {
        if ($productSize->stock < $threshold) {
            // Check if active alert exists
            $existingAlert = self::where('product_size_id', $productSize->id)
                ->where('status', 'active')
                ->first();

            if ($existingAlert) {
                // Update current stock
                $existingAlert->update([
                    'current_stock' => $productSize->stock,
                ]);
                return $existingAlert;
            }

            // Create new alert
            return self::create([
                'product_size_id' => $productSize->id,
                'product_id' => $productSize->product_id,
                'threshold' => $threshold,
                'current_stock' => $productSize->stock,
                'status' => 'active',
                'first_triggered_at' => now(),
            ]);
        }

        return null;
    }
}

