<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        // Try to get from cache settings first, then fallback to config
        $this->apiKey = Cache::get('shipping_rajaongkir_api_key') ?? config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
    }

    public function getProvinces()
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            try {
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get($this->baseUrl . '/province');

                if ($response->successful()) {
                    return $response->json()['rajaongkir']['results'];
                }
            } catch (\Exception $e) {
                Log::error('RajaOngkir Province Error: ' . $e->getMessage());
            }

            return [];
        });
    }

    public function getCities($provinceId = null)
    {
        $cacheKey = 'rajaongkir_cities_' . ($provinceId ?? 'all');

        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            try {
                $params = $provinceId ? ['province' => $provinceId] : [];
                $response = Http::withHeaders([
                    'key' => $this->apiKey,
                ])->get($this->baseUrl . '/city', $params);

                if ($response->successful()) {
                    return $response->json()['rajaongkir']['results'];
                }
            } catch (\Exception $e) {
                Log::error('RajaOngkir City Error: ' . $e->getMessage());
            }

            return [];
        });
    }

    public function getSubdistricts($cityId)
    {
        // Note: This endpoint may not be available in starter API
        // Only available in pro/basic API
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/subdistrict', ['city' => $cityId]);

            if ($response->successful()) {
                return $response->json()['rajaongkir']['results'];
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir Subdistrict Error: ' . $e->getMessage());
        }

        return [];
    }

    public function calculateCost($origin, $destination, $weight, $courier)
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post($this->baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);

            if ($response->successful()) {
                $result = $response->json()['rajaongkir']['results'][0] ?? null;
                if ($result && isset($result['costs'])) {
                    return $result['costs'];
                }
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost Calculation Error: ' . $e->getMessage());
        }

        return [];
    }

    public function getWaybill($waybill, $courier)
    {
        // Note: This endpoint may require pro/basic API
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'content-type' => 'application/x-www-form-urlencoded',
            ])->post($this->baseUrl . '/waybill', [
                'waybill' => $waybill,
                'courier' => $courier,
            ]);

            if ($response->successful()) {
                return $response->json()['rajaongkir']['result'];
            }
        } catch (\Exception $e) {
            Log::error('RajaOngkir Waybill Error: ' . $e->getMessage());
        }

        return null;
    }

    public function getAvailableCouriers()
    {
        return ['jne', 'tiki', 'pos', 'sicepat', 'jnt', 'wahana'];
    }

    public function getCourierName($code)
    {
        $couriers = [
            'jne' => 'JNE',
            'tiki' => 'TIKI',
            'pos' => 'POS Indonesia',
            'sicepat' => 'SiCepat',
            'jnt' => 'J&T Express',
            'wahana' => 'Wahana',
        ];

        return $couriers[$code] ?? strtoupper($code);
    }

    public function getCityName($cityId)
    {
        $cities = $this->getCities();
        $city = collect($cities)->firstWhere('city_id', $cityId);
        return $city['city_name'] ?? null;
    }

    public function getProvinceName($provinceId)
    {
        $provinces = $this->getProvinces();
        $province = collect($provinces)->firstWhere('province_id', $provinceId);
        return $province['province'] ?? null;
    }

    public function getFreeShippingThreshold()
    {
        return Cache::get('shipping_free_shipping_threshold', 0);
    }

    public function getDefaultOriginCity()
    {
        return Cache::get('shipping_default_origin_city', 152); // Jakarta Selatan
    }

    public function isEligibleForFreeShipping($subtotal)
    {
        $threshold = $this->getFreeShippingThreshold();
        return $threshold > 0 && $subtotal >= $threshold;
    }

    /**
     * Get shipping cost with formatted options
     * @param int $origin Origin city ID
     * @param string $destination Destination postal code or city ID
     * @param int $weight Weight in grams
     * @param string $courier Courier codes (comma-separated: jne,pos,tiki)
     * @return array Formatted shipping options
     */
    public function getShippingCost($origin, $destination, $weight = 1000, $courier = 'jne,pos,tiki')
    {
        $results = [];
        
        try {
            $couriers = explode(',', $courier);
            
            foreach ($couriers as $c) {
                $c = trim($c);
                $costs = $this->calculateCost($origin, $destination, $weight, $c);
                
                if (!empty($costs)) {
                    $results[] = [
                        'code' => $c,
                        'name' => $this->getCourierName($c),
                        'costs' => $costs,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting shipping cost: ' . $e->getMessage());
        }
        
        return $results;
    }
}
