<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\Discount;
use App\Models\Cart;
use Illuminate\Support\Facades\Cache;

class DiscountService
{
    /**
     * Validate dan apply promo code ke cart
     */
    public function validateAndApplyPromoCode($code, $cartSubtotal)
    {
        $promoCode = PromoCode::where('code', strtoupper($code))->first();

        if (!$promoCode) {
            return [
                'success' => false,
                'message' => 'Kode promo tidak ditemukan',
            ];
        }

        if (!$promoCode->canBeUsed()) {
            return [
                'success' => false,
                'message' => $this->getPromoCodeErrorMessage($promoCode),
            ];
        }

        $discount = $promoCode->discount;

        if (!$discount->isActive()) {
            return [
                'success' => false,
                'message' => 'Promosi ini sudah berakhir',
            ];
        }

        // Check minimum purchase
        if ($discount->minimum_purchase && $cartSubtotal < $discount->minimum_purchase) {
            return [
                'success' => false,
                'message' => 'Pembelian minimum Rp ' . number_format($discount->minimum_purchase, 0, ',', '.'),
            ];
        }

        // Calculate discount
        $discountAmount = $this->calculateDiscount($discount, $cartSubtotal);

        return [
            'success' => true,
            'message' => 'Kode promo berhasil diterapkan',
            'promo_code_id' => $promoCode->id,
            'discount_amount' => $discountAmount,
            'discount_type' => $discount->discount_type,
            'promo_code' => $promoCode->code,
        ];
    }

    /**
     * Calculate discount amount based on type
     */
    public function calculateDiscount($discount, $cartSubtotal)
    {
        $discountAmount = 0;

        switch ($discount->discount_type) {
            case 'percentage':
                $discountAmount = ($cartSubtotal * $discount->discount_value) / 100;
                // Apply maximum discount if set
                if ($discount->maximum_discount && $discountAmount > $discount->maximum_discount) {
                    $discountAmount = $discount->maximum_discount;
                }
                break;

            case 'fixed':
                $discountAmount = $discount->discount_value;
                break;

            case 'volume':
            case 'bogo':
            case 'flash':
                // These will be calculated per item
                $discountAmount = $discount->discount_value;
                break;
        }

        return min($discountAmount, $cartSubtotal);
    }

    /**
     * Get error message untuk promo code yang tidak valid
     */
    private function getPromoCodeErrorMessage($promoCode)
    {
        if (!$promoCode->is_active) {
            return 'Kode promo tidak aktif';
        }

        if ($promoCode->isExpired()) {
            return 'Kode promo sudah kadaluarsa';
        }

        if ($promoCode->usage_limit && $promoCode->used_count >= $promoCode->usage_limit) {
            return 'Kode promo sudah mencapai batas penggunaan';
        }

        if (!$promoCode->discount->isActive()) {
            return 'Promosi ini sudah berakhir';
        }

        return 'Kode promo tidak dapat digunakan';
    }

    /**
     * Get applicable discounts untuk cart
     */
    public function getApplicableDiscounts()
    {
        return Discount::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get flash sale discounts (Limited time)
     */
    public function getFlashSaleDiscounts()
    {
        return Discount::where('discount_type', 'flash')
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->get();
    }

    /**
     * Get BOGO deals
     */
    public function getBOGODeals()
    {
        return Discount::where('discount_type', 'bogo')
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->with('discountProducts.product')
            ->get();
    }

    /**
     * Get volume discount options
     */
    public function getVolumeDiscounts()
    {
        return Discount::where('discount_type', 'volume')
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->with('discountProducts.product')
            ->get();
    }

    /**
     * Increment promo code usage
     */
    public function incrementPromoCodeUsage($promoCodeId)
    {
        $promoCode = PromoCode::find($promoCodeId);
        if ($promoCode) {
            $promoCode->incrementUsage();
        }
    }

    /**
     * Cache applicable discounts
     */
    public function cacheDiscounts()
    {
        Cache::put('applicable_discounts', $this->getApplicableDiscounts(), now()->addHours(1));
        Cache::put('flash_sale_discounts', $this->getFlashSaleDiscounts(), now()->addHours(1));
        Cache::put('bogo_deals', $this->getBOGODeals(), now()->addHours(1));
        Cache::put('volume_discounts', $this->getVolumeDiscounts(), now()->addHours(1));
    }

    /**
     * Clear discount cache
     */
    public function clearCache()
    {
        Cache::forget('applicable_discounts');
        Cache::forget('flash_sale_discounts');
        Cache::forget('bogo_deals');
        Cache::forget('volume_discounts');
    }
}
