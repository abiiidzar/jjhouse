<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'shipping_cost',
        'status',
        'payment_status',
        'payment_method',
        'shipping_courier',
        'shipping_service',
        'shipping_address',
        'province',
        'city',
        'postal_code',
        'promo_code_id',
        'discount_amount',
        'discount_type',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUSES = [
        'pending' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    const PAYMENT_STATUSES = [
        'unpaid' => 'Belum Dibayar',
        'dp' => 'DP 50%',
        'paid' => 'Lunas',
        'refunded' => 'Dikembalikan',
        'failed' => 'Gagal',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_amount + $this->shipping_cost;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute()
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'paid' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
