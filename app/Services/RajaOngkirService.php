<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;
    protected $originCity;
    protected $timeout = 30;
    protected $citiesData = null;

    public function __construct()
    {
        // 🔥 PRIORITAS: Baca API key dari cache (diisi dari Admin Settings)
        // Fallback ke config (dari .env)
        $this->apiKey = cache('shipping_rajaongkir_api_key') 
            ?? config('services.rajaongkir.api_key');
            
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
        
        // 🔥 PRIORITAS: Baca origin city dari cache, fallback ke config
        $this->originCity = cache('shipping_default_origin_city') 
            ?? config('services.rajaongkir.origin_city', 495);
        
        // 🔥 Pre-load data kota lokal sekali di konstruktor agar getCityById() cepat
        // tanpa memicu panggilan API timeout
        $this->citiesData = $this->loadLocalCitiesData();
        
        // Log untuk debugging (jangan log full API key)
        if (empty($this->apiKey)) {
            Log::warning('RajaOngkir API Key is EMPTY! Check .env or Admin Settings');
        } else {
            Log::info('RajaOngkir API Key loaded from: ' . 
                (cache('shipping_rajaongkir_api_key') ? 'cache (admin settings)' : 'config (.env)'));
        }
    }

    /**
     * Load semua data kota dari file lokal
     * Dipanggil sekali di constructor agar lookup cepat
     */
    private function loadLocalCitiesData(): array
    {
        // Cek cache dulu
        if (Cache::has('rajaongkir_cities_all')) {
            return Cache::get('rajaongkir_cities_all');
        }

        $paths = [
            resource_path('data/rajaongkir_cities.json'),
            resource_path('data/cities.json'),
            base_path('resources/data/rajaongkir_cities.json'),
            base_path('resources/data/cities.json'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
                    Cache::put('rajaongkir_cities_all', $data, 86400);
                    return $data;
                }
            }
        }

        return $this->getDefaultCities();
    }
     public function getOriginCity()
    {
        return $this->originCity;
    }
    public function getOriginCityName()
    {
        $city = $this->getCityById($this->originCity);
        return $city['city_name'] ?? 'Unknown';
    }

    public function getProvinces()
    {
        $cacheKey = 'rajaongkir_provinces';
        
        // 🔥 Cek cache dulu
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 🔥 PRIORITAS: Gunakan data lokal dulu (cepat, tidak menunggu API timeout)
        // Data lokal sudah lengkap, sehingga dropdown provinsi langsung responsif
        $provinces = $this->getLocalProvinces();
        if (!empty($provinces)) {
            Cache::put($cacheKey, $provinces, 86400);
            return $provinces;
        }

        // 🔥 FALLBACK: Coba API hanya jika data lokal kosong (timeout pendek 5 detik)
        try {
            Log::info('RajaOngkir getProvinces attempt');
            
            $response = Http::timeout(5)->withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/province');

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['rajaongkir']['status']['code']) && $result['rajaongkir']['status']['code'] == 200) {
                    $provinces = $result['rajaongkir']['results'] ?? [];
                    if (!empty($provinces)) {
                        Cache::put($cacheKey, $provinces, 86400);
                        return $provinces;
                    }
                }
            }
            Log::error('RajaOngkir Province Error: ' . ($response->successful() ? json_encode($response->json()) : $response->body()));
        } catch (\Exception $e) {
            Log::warning('RajaOngkir Province Exception: ' . $e->getMessage());
        }

        return $provinces;
    }

    public function getLocalProvinces(): array
    {
        $paths = [
            resource_path('data/rajaongkir_provinces.json'),
            resource_path('data/provinces.json'),
            base_path('resources/data/rajaongkir_provinces.json'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
                    return $data;
                }
            }
        }
        return $this->getDefaultProvinces();
    }

    public function getCities($provinceId = null)
    {
        // 🔥 Gunakan province_id langsung (data lokal sudah konsisten dengan penomoran API)
        $cacheKey = 'rajaongkir_cities_' . ($provinceId ?? 'all');
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 🔥 PRIORITAS: Gunakan data lokal dulu (cepat, tidak menunggu API timeout)
        // Data lokal sudah lengkap, sehingga dropdown kota langsung responsif
        $cities = $this->getLocalCities($provinceId);
        if (!empty($cities)) {
            Cache::put($cacheKey, $cities, 86400);
            return $cities;
        }

        // 🔥 FALLBACK: Coba API hanya jika data lokal kosong (timeout pendek 5 detik)
        try {
            $params = $provinceId ? ['province' => $provinceId] : [];
            $response = Http::timeout(5)->withHeaders([
                'key' => $this->apiKey,
            ])->get($this->baseUrl . '/city', $params);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['rajaongkir']['status']['code']) && $result['rajaongkir']['status']['code'] == 200) {
                    $cities = $result['rajaongkir']['results'] ?? [];
                    if (!empty($cities)) {
                        // 🔥 TAMBAHKAN: Merge postal code dari lokal
                        $cities = $this->mergePostalCode($cities);
                        Cache::put($cacheKey, $cities, 86400);
                        return $cities;
                    }
                }
            }
            Log::error('RajaOngkir City Error: ' . ($response->successful() ? json_encode($response->json()) : $response->body()));
        } catch (\Exception $e) {
            Log::warning('RajaOngkir City Exception: ' . $e->getMessage());
        }

        return $cities;
    }


    
    private function mergePostalCode($cities)
    {
        $localCities = $this->getLocalCities();
        $localMap = [];
        foreach ($localCities as $lc) {
            $localMap[$lc['city_id']] = $lc['postal_code'] ?? null;
        }

        foreach ($cities as &$city) {
            if (isset($localMap[$city['city_id']])) {
                $city['postal_code'] = $localMap[$city['city_id']];
            }
        }
        return $cities;
    }

    

    

    /**
     * Get city by name with postal code
     */
    public function getCityByName($cityName, $provinceId = null)
    {
        $cities = $this->getCities($provinceId);
        foreach ($cities as $city) {
            if (stripos($city['city_name'], $cityName) !== false) {
                return $city;
            }
        }
        return null;
    }

    /**
     * Get postal code for a city
     */
    public function getCityById($cityId)
    {
        // 🔥 Gunakan data yang sudah di-preload di constructor
        // agar tidak memicu panggilan API timeout
        if ($this->citiesData !== null) {
            foreach ($this->citiesData as $city) {
                if ($city['city_id'] == $cityId) {
                    return $city;
                }
            }
        }

        // Fallback: coba dari getCities (bisa memicu API, tapi hanya jika preload gagal)
        $cities = $this->getCities();
        foreach ($cities as $city) {
            if ($city['city_id'] == $cityId) {
                return $city;
            }
        }
        return null;
    }

    public function getPostalCode($cityId)
    {
        $city = $this->getCityById($cityId);
        return $city['postal_code'] ?? null;
    }
    /**
     * Get all cities with postal codes
     */
    public function getAllCitiesWithPostalCode()
    {
        $cities = $this->getCities();
        $result = [];
        foreach ($cities as $city) {
            $result[] = [
                'city_id' => $city['city_id'],
                'city_name' => $city['city_name'],
                'province_id' => $city['province_id'],
                'postal_code' => $city['postal_code'] ?? '-',
            ];
        }
        return $result;
    }

    public function getLocalCities($provinceId = null): array
    {
        // 🔥 Gunakan data yang sudah di-preload di constructor
        // agar tidak memicu panggilan API timeout
        if ($this->citiesData !== null) {
            if ($provinceId) {
                return array_values(array_filter($this->citiesData, function($c) use ($provinceId) {
                    return ($c['province_id'] ?? null) == $provinceId;
                }));
            }
            return $this->citiesData;
        }

        // Fallback: baca dari file langsung
        $paths = [
            resource_path('data/rajaongkir_cities.json'),
            resource_path('data/cities.json'),
            base_path('resources/data/rajaongkir_cities.json'),
            base_path('resources/data/cities.json'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($data)) {
                    if ($provinceId) {
                        return array_values(array_filter($data, function($c) use ($provinceId) {
                            return ($c['province_id'] ?? null) == $provinceId;
                        }));
                    }
                    return $data;
                }
            }
        }

        return $this->getDefaultCities();
    }

    protected function getDefaultCities(): array
    {
        return [
            // DKI Jakarta (province_id: 11)
            ['city_id' => 152, 'city_name' => 'Jakarta Selatan', 'province_id' => 11, 'postal_code' => '12110'],
            ['city_id' => 153, 'city_name' => 'Jakarta Timur', 'province_id' => 11, 'postal_code' => '13110'],
            ['city_id' => 154, 'city_name' => 'Jakarta Pusat', 'province_id' => 11, 'postal_code' => '10110'],
            ['city_id' => 155, 'city_name' => 'Jakarta Barat', 'province_id' => 11, 'postal_code' => '11110'],
            ['city_id' => 156, 'city_name' => 'Jakarta Utara', 'province_id' => 11, 'postal_code' => '14110'],
            
            // Jawa Barat (province_id: 12)
            ['city_id' => 475, 'city_name' => 'Bandung', 'province_id' => 12, 'postal_code' => '40111'],
            ['city_id' => 476, 'city_name' => 'Bekasi', 'province_id' => 12, 'postal_code' => '17111'],
            ['city_id' => 477, 'city_name' => 'Bogor', 'province_id' => 12, 'postal_code' => '16111'],
            ['city_id' => 478, 'city_name' => 'Cimahi', 'province_id' => 12, 'postal_code' => '40511'],
            ['city_id' => 479, 'city_name' => 'Cirebon', 'province_id' => 12, 'postal_code' => '45111'],
            ['city_id' => 480, 'city_name' => 'Depok', 'province_id' => 12, 'postal_code' => '16411'],
            ['city_id' => 481, 'city_name' => 'Sukabumi', 'province_id' => 12, 'postal_code' => '43111'],
            ['city_id' => 482, 'city_name' => 'Tasikmalaya', 'province_id' => 12, 'postal_code' => '46111'],
            ['city_id' => 483, 'city_name' => 'Bandung Barat', 'province_id' => 12, 'postal_code' => '40552'],
            ['city_id' => 484, 'city_name' => 'Cianjur', 'province_id' => 12, 'postal_code' => '43211'],
            ['city_id' => 485, 'city_name' => 'Garut', 'province_id' => 12, 'postal_code' => '44111'],
            ['city_id' => 486, 'city_name' => 'Indramayu', 'province_id' => 12, 'postal_code' => '45211'],
            ['city_id' => 487, 'city_name' => 'Karawang', 'province_id' => 12, 'postal_code' => '41311'],
            ['city_id' => 488, 'city_name' => 'Kuningan', 'province_id' => 12, 'postal_code' => '45511'],
            ['city_id' => 489, 'city_name' => 'Majalengka', 'province_id' => 12, 'postal_code' => '45411'],
            ['city_id' => 490, 'city_name' => 'Purwakarta', 'province_id' => 12, 'postal_code' => '41111'],
            ['city_id' => 491, 'city_name' => 'Subang', 'province_id' => 12, 'postal_code' => '41211'],
            ['city_id' => 492, 'city_name' => 'Sumedang', 'province_id' => 12, 'postal_code' => '45311'],
            
            // Sumatera Barat (province_id: 3)
            ['city_id' => 493, 'city_name' => 'Agam', 'province_id' => 3, 'postal_code' => '26411'],
            ['city_id' => 494, 'city_name' => 'Padang', 'province_id' => 3, 'postal_code' => '25111'],
            ['city_id' => 495, 'city_name' => 'Bukittinggi', 'province_id' => 3, 'postal_code' => '26111'],
            ['city_id' => 496, 'city_name' => 'Payakumbuh', 'province_id' => 3, 'postal_code' => '26211'],
            ['city_id' => 497, 'city_name' => 'Padang Panjang', 'province_id' => 3, 'postal_code' => '27111'],
            ['city_id' => 498, 'city_name' => 'Pariaman', 'province_id' => 3, 'postal_code' => '25511'],
            ['city_id' => 499, 'city_name' => 'Sawahlunto', 'province_id' => 3, 'postal_code' => '27411'],
            ['city_id' => 500, 'city_name' => 'Solok', 'province_id' => 3, 'postal_code' => '27311'],
            ['city_id' => 501, 'city_name' => 'Dharmasraya', 'province_id' => 3, 'postal_code' => '27611'],
            ['city_id' => 502, 'city_name' => 'Kepulauan Mentawai', 'province_id' => 3, 'postal_code' => '25711'],
            ['city_id' => 503, 'city_name' => 'Lima Puluh Kota', 'province_id' => 3, 'postal_code' => '26611'],
            ['city_id' => 504, 'city_name' => 'Padang Pariaman', 'province_id' => 3, 'postal_code' => '25511'],
            ['city_id' => 505, 'city_name' => 'Pasaman', 'province_id' => 3, 'postal_code' => '26311'],
            ['city_id' => 506, 'city_name' => 'Pasaman Barat', 'province_id' => 3, 'postal_code' => '26511'],
            ['city_id' => 507, 'city_name' => 'Pesisir Selatan', 'province_id' => 3, 'postal_code' => '25611'],
            ['city_id' => 508, 'city_name' => 'Sijunjung', 'province_id' => 3, 'postal_code' => '27511'],
            ['city_id' => 509, 'city_name' => 'Solok Selatan', 'province_id' => 3, 'postal_code' => '27711'],
            ['city_id' => 510, 'city_name' => 'Tanah Datar', 'province_id' => 3, 'postal_code' => '27211'],
        ];
    }
    public function getCitiesWithPostalCode($provinceId = null)
    {
        $cities = $this->getCities($provinceId);
        
        // Jika cities dari API tidak punya postal_code, ambil dari file lokal
        if (!empty($cities) && !isset($cities[0]['postal_code'])) {
            $localCities = $this->getLocalCities($provinceId);
            // Merge postal_code dari local data
            foreach ($cities as &$city) {
                $found = array_filter($localCities, function($lc) use ($city) {
                    return $lc['city_id'] == $city['city_id'];
                });
                if (!empty($found)) {
                    $found = array_values($found);
                    $city['postal_code'] = $found[0]['postal_code'] ?? null;
                }
            }
        }
        
        return $cities;
    }
    public function calculateCost($origin, $destination, $weight, $courier)
    {
        // 🔥 Jika API key kosong, langsung gunakan estimasi lokal (tanpa menunggu timeout)
        if (empty($this->apiKey)) {
            Log::warning('RajaOngkir API Key is EMPTY, using local fallback immediately');
            return $this->calculateCostLocal($origin, $destination, $weight, $courier);
        }

        try {
            // 🔥 Gunakan timeout sangat pendek (2 detik) agar tidak membuat user menunggu lama
            // Jika API lambat/tidak merespons, langsung fallback ke estimasi lokal
            $response = Http::timeout(2)->withHeaders([
                'key' => $this->apiKey,
                'content-type' => 'application/x-www-form-urlencoded',
            ])->post($this->baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Cek status response
                if ($result['rajaongkir']['status']['code'] == 200) {
                    // 🔥 Kembalikan data lengkap semua kurir (code, name, costs)
                    // agar konsisten dengan format fallback lokal
                    $results = $result['rajaongkir']['results'] ?? [];
                    $data = [];
                    foreach ($results as $courier) {
                        $data[] = [
                            'code' => $courier['code'] ?? '',
                            'name' => $courier['name'] ?? $courier['code'] ?? '',
                            'costs' => $courier['costs'] ?? [],
                        ];
                    }
                    return [
                        'success' => true,
                        'data' => $data,
                        'is_local' => false,
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $result['rajaongkir']['status']['description'] ?? 'Gagal mendapatkan ongkir'
                    ];
                }
            }
            
            Log::error('RajaOngkir Cost Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke server'
            ];
            
        } catch (\Exception $e) {
            Log::warning('RajaOngkir Cost Exception (fallback to local): ' . $e->getMessage());
            
            // 🔥 FALLBACK: Gunakan perhitungan ongkir lokal (offline)
            return $this->calculateCostLocal($origin, $destination, $weight, $courier);
        }
    }

    /**
     * 🔥 FALLBACK: Hitung ongkir secara lokal (offline) ketika API tidak dapat dijangkau
     * Menggunakan tarif estimasi berdasarkan jarak/region
     */
    public function calculateCostLocal($origin, $destination, $weight, $courier = 'jne,tiki,pos')
    {
        Log::info("RajaOngkir using LOCAL fallback for cost (origin: {$origin}, dest: {$destination}, weight: {$weight}g)");
        
        // Ambil nama kota tujuan untuk estimasi
        $destCity = $this->getCityById($destination);
        $destName = $destCity['city_name'] ?? 'Kota Tujuan';
        $destProvinceId = $destCity['province_id'] ?? null;
        
        // Ambil nama kota asal
        $originCity = $this->getCityById($origin);
        $originName = $originCity['city_name'] ?? 'Kota Asal';
        $originProvinceId = $originCity['province_id'] ?? null;
        
        // Tentukan zona berdasarkan provinsi
        // DKI Jakarta=11, Jawa Barat=12, Jawa Tengah=13, Jawa Timur=15, Banten=16, DI Yogyakarta=14
        $jawaIds = [11, 12, 13, 14, 15, 16]; 
        // Sumatera Barat=3, Sumatera Utara=2, Sumatera Selatan=6, Riau=4, Jambi=5, Lampung=8, Aceh=1, Bengkulu=7, Kep Bangka Belitung=9, Kep Riau=10
        $sumateraIds = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; 
        
        $isJawa = in_array($destProvinceId, $jawaIds);
        $isSumatera = in_array($destProvinceId, $sumateraIds);
        $isOriginJawa = in_array($originProvinceId, $jawaIds);
        $isOriginSumatera = in_array($originProvinceId, $sumateraIds);
        
        // Tentukan kategori jarak
        if ($isOriginJawa && $isJawa) {
            $zone = 'jawa_jawa'; // Dalam Pulau Jawa
        } elseif ($isOriginSumatera && $isSumatera) {
            $zone = 'sumatera_sumatera'; // Dalam Sumatera
        } elseif (($isOriginJawa && $isSumatera) || ($isOriginSumatera && $isJawa)) {
            $zone = 'jawa_sumatera'; // Jawa - Sumatera
        } else {
            $zone = 'antar_pulau'; // Antar pulau lainnya
        }
        
        // Tarif dasar per kg (dalam Rupiah) per zona
        $rates = [
            'jawa_jawa' => [
                'jne' => ['base' => 8000, 'per_kg' => 3000, 'etd' => '1-2'],
                'tiki' => ['base' => 9000, 'per_kg' => 3500, 'etd' => '1-2'],
                'pos' => ['base' => 10000, 'per_kg' => 4000, 'etd' => '2-3'],
            ],
            'sumatera_sumatera' => [
                'jne' => ['base' => 10000, 'per_kg' => 4000, 'etd' => '1-3'],
                'tiki' => ['base' => 11000, 'per_kg' => 4500, 'etd' => '1-3'],
                'pos' => ['base' => 12000, 'per_kg' => 5000, 'etd' => '2-4'],
            ],
            'jawa_sumatera' => [
                'jne' => ['base' => 15000, 'per_kg' => 6000, 'etd' => '2-4'],
                'tiki' => ['base' => 16000, 'per_kg' => 6500, 'etd' => '2-4'],
                'pos' => ['base' => 18000, 'per_kg' => 7000, 'etd' => '3-5'],
            ],
            'antar_pulau' => [
                'jne' => ['base' => 20000, 'per_kg' => 8000, 'etd' => '3-7'],
                'tiki' => ['base' => 22000, 'per_kg' => 9000, 'etd' => '3-7'],
                'pos' => ['base' => 25000, 'per_kg' => 10000, 'etd' => '4-8'],
            ],
        ];
        
        // Normalisasi weight ke kg (minimal 1kg)
        $weightKg = max(1, ceil($weight / 1000));
        
        // Parse courier list
        $couriers = array_filter(array_map('trim', explode(',', $courier)));
        if (empty($couriers)) {
            $couriers = ['jne', 'tiki', 'pos'];
        }
        
        $courierNames = [
            'jne' => 'Jalur Nugraha Ekakurir (JNE)',
            'tiki' => 'Citra Van Titipan Kilat (TIKI)',
            'pos' => 'POS Indonesia (POS)',
        ];
        
        $results = [];
        foreach ($couriers as $c) {
            $c = strtolower($c);
            if (!isset($rates[$zone][$c])) {
                continue;
            }
            
            $rate = $rates[$zone][$c];
            $totalCost = $rate['base'] + ($rate['per_kg'] * ($weightKg - 1));
            
            $costs = [
                [
                    'service' => 'REG',
                    'description' => 'Reguler (Estimasi lokal)',
                    'cost' => [
                        ['value' => $totalCost, 'etd' => $rate['etd'], 'note' => '']
                    ],
                ],
            ];
            
            // Tambahkan opsi OKE (ekonomis) untuk JNE
            if ($c == 'jne') {
                $okeCost = $rate['base'] - 2000 + ($rate['per_kg'] * ($weightKg - 1));
                $costs[] = [
                    'service' => 'OKE',
                    'description' => 'Ongkos Kirim Ekonomis (Estimasi lokal)',
                    'cost' => [
                        ['value' => max($okeCost, 5000), 'etd' => $rate['etd'], 'note' => '']
                    ],
                ];
            }
            
            $results[] = [
                'code' => $c,
                'name' => $courierNames[$c] ?? strtoupper($c),
                'costs' => $costs,
            ];
        }
        
        if (empty($results)) {
            return [
                'success' => false,
                'message' => 'Tidak ada kurir yang tersedia untuk rute ini'
            ];
        }
        
        return [
            'success' => true,
            'data' => $results,
            'is_local' => true,
            'message' => "Menggunakan estimasi lokal (API tidak dapat dijangkau). Ongkir dari {$originName} ke {$destName}."
        ];
    }

    public function testConnection()
    {
        if (empty($this->apiKey)) {
            return ['status' => 'error', 'message' => 'RajaOngkir API key is not configured'];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['key' => $this->apiKey])
                ->get(rtrim($this->baseUrl, '/') . '/province');

            if (!$response->successful()) {
                return [
                    'status' => 'error',
                    'message' => 'RajaOngkir returned HTTP ' . $response->status(),
                ];
            }

            $result = $response->json();
            $apiStatus = $result['rajaongkir']['status'] ?? [];

            if (($apiStatus['code'] ?? null) == 200) {
                return [
                    'status' => 'success',
                    'message' => 'Connected directly to RajaOngkir API',
                    'data' => [
                        'province_count' => count($result['rajaongkir']['results'] ?? []),
                    ],
                ];
            }

            return [
                'status' => 'error',
                'message' => $apiStatus['description'] ?? 'RajaOngkir returned an unexpected response',
            ];
        } catch (\Exception $e) {
            Log::warning('RajaOngkir connection test failed: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Unable to reach RajaOngkir API'];
        }
    }

    protected function getDefaultProvinces(): array
    {
        return [
            ['province_id' => 1, 'province' => 'Aceh'],
            ['province_id' => 2, 'province' => 'Sumatera Utara'],
            ['province_id' => 3, 'province' => 'Sumatera Barat'],
            ['province_id' => 4, 'province' => 'Riau'],
            ['province_id' => 5, 'province' => 'Jambi'],
            ['province_id' => 6, 'province' => 'Sumatera Selatan'],
            ['province_id' => 7, 'province' => 'Bengkulu'],
            ['province_id' => 8, 'province' => 'Lampung'],
            ['province_id' => 9, 'province' => 'Kepulauan Bangka Belitung'],
            ['province_id' => 10, 'province' => 'Kepulauan Riau'],
            ['province_id' => 11, 'province' => 'DKI Jakarta'],
            ['province_id' => 12, 'province' => 'Jawa Barat'],
            ['province_id' => 13, 'province' => 'Jawa Tengah'],
            ['province_id' => 14, 'province' => 'DI Yogyakarta'],
            ['province_id' => 15, 'province' => 'Jawa Timur'],
            ['province_id' => 16, 'province' => 'Banten'],
            ['province_id' => 17, 'province' => 'Bali'],
            ['province_id' => 18, 'province' => 'Nusa Tenggara Barat'],
            ['province_id' => 19, 'province' => 'Nusa Tenggara Timur'],
            ['province_id' => 20, 'province' => 'Kalimantan Barat'],
            ['province_id' => 21, 'province' => 'Kalimantan Tengah'],
            ['province_id' => 22, 'province' => 'Kalimantan Selatan'],
            ['province_id' => 23, 'province' => 'Kalimantan Timur'],
            ['province_id' => 24, 'province' => 'Kalimantan Utara'],
            ['province_id' => 25, 'province' => 'Sulawesi Utara'],
            ['province_id' => 26, 'province' => 'Sulawesi Tengah'],
            ['province_id' => 27, 'province' => 'Sulawesi Selatan'],
            ['province_id' => 28, 'province' => 'Sulawesi Tenggara'],
            ['province_id' => 29, 'province' => 'Gorontalo'],
            ['province_id' => 30, 'province' => 'Sulawesi Barat'],
            ['province_id' => 31, 'province' => 'Maluku'],
            ['province_id' => 32, 'province' => 'Maluku Utara'],
            ['province_id' => 33, 'province' => 'Papua'],
            ['province_id' => 34, 'province' => 'Papua Barat'],
            ['province_id' => 35, 'province' => 'Papua Selatan'],
            ['province_id' => 36, 'province' => 'Papua Tengah'],
            ['province_id' => 37, 'province' => 'Papua Pegunungan'],
            ['province_id' => 38, 'province' => 'Papua Barat Daya'],
        ];
    }
}
