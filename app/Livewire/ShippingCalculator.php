<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShippingCalculator extends Component
{
    public $postal_code = '';
    public $city = '';
    public $city_id = '';
    public $province = '';
    public $province_id = '';
    public $shippingOptions = [];
    public $selectedCourier = null;
    public $selectedService = null;
    public $shippingCost = 0;
    public $estimatedDays = '';
    public $isLoading = false;
    public $error = null;
    public $notice = null;
    public $cartTotal = 0;
    public $isEditing = false;
    public $citiesList = [];
    public $provincesList = [];

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->postal_code = $user->postal_code ?? '';
            $this->city = $user->city ?? '';
            $this->province = $user->province ?? '';
        }

        $cart = Auth::user()->cart;
        if ($cart) {
            $this->cartTotal = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
        }

        $this->loadProvinces();

        $shipping = session()->get('checkout', []);
        if ($shipping) {
            $this->selectedCourier = $shipping['courier'] ?? null;
            $this->selectedService = $shipping['service'] ?? null;
            $this->shippingCost = $shipping['shipping_cost'] ?? 0;
            $this->estimatedDays = $shipping['estimated_days'] ?? '';
            $this->city = $shipping['city'] ?? $this->city;
            $this->province = $shipping['province'] ?? $this->province;
            $this->postal_code = $shipping['postal_code'] ?? $this->postal_code;
            $this->city_id = $shipping['city_id'] ?? null;
            $this->province_id = $shipping['province_id'] ?? null;
        }

        if ($this->province) {
            $this->loadCitiesByProvinceName($this->province);
        }
    }

    public function loadProvinces()
    {
        try {
            $service = new RajaOngkirService();
            $this->provincesList = $service->getProvinces();
        } catch (\Exception $e) {
            Log::error('Load provinces error: ' . $e->getMessage());
            $this->provincesList = [];
        }
    }

    public function loadCitiesByProvinceName($provinceName)
    {
        try {
            $service = new RajaOngkirService();
            $province = collect($this->provincesList)->first(function($p) use ($provinceName) {
                return stripos($p['province'], $provinceName) !== false;
            });

            if ($province) {
                $this->province_id = $province['province_id'];
                $this->citiesList = $service->getCities($this->province_id);
            }
        } catch (\Exception $e) {
            Log::error('Load cities error: ' . $e->getMessage());
            $this->citiesList = [];
        }
    }

    public function updatedProvince($value)
    {
        if ($value) {
            $this->loadCitiesByProvinceName($value);
            $this->resetShippingSelection();
        }
    }

    public function updatedCity($value)
    {
        if ($value) {
            $city = collect($this->citiesList)->first(function($c) use ($value) {
                return stripos($c['city_name'], $value) !== false;
            });
            if ($city) {
                $this->city_id = $city['city_id'];
            }
            $this->resetShippingSelection();
        }
    }

    public function selectProvince($provinceName, $provinceId)
    {
        $this->province = $provinceName;
        $this->province_id = $provinceId;
        $this->loadCitiesByProvinceName($provinceName);
        $this->city = '';
        $this->city_id = '';
        $this->resetShippingSelection();
    }

    public function selectCity($cityName, $cityId)
    {
        $this->city = $cityName;
        $this->city_id = $cityId;
        
        // 🔥 Auto-fill postal code
        $service = new RajaOngkirService();
        $city = $service->getCityById($cityId);
        if ($city && isset($city['postal_code'])) {
            $this->postal_code = $city['postal_code'];
        }
        
        $this->resetShippingSelection();
    }

    /**
     * 🔥 Reset semua data pengiriman terkait (ongkir, kurir, error, notice)
     * Dipanggil ketika user mengganti provinsi/kota
     */
    private function resetShippingSelection()
    {
        $this->shippingOptions = [];
        $this->shippingCost = 0;
        $this->selectedCourier = null;
        $this->selectedService = null;
        $this->estimatedDays = '';
        $this->error = null;
        $this->notice = null;
    }


    public function calculateShipping()
    {
        // Validation
        if (!$this->postal_code) {
            $this->error = 'Kode pos harus diisi';
            return;
        }

        if (!$this->city_id) {
            $this->error = 'Kota tidak valid. Silakan pilih dari daftar.';
            return;
        }

        if (!$this->province_id) {
            $this->error = 'Provinsi tidak valid. Silakan pilih dari daftar.';
            return;
        }

        $this->isLoading = true;
        $this->error = null;
        $this->notice = null;
        $this->shippingOptions = [];

        try {
            $service = new RajaOngkirService();
            $totalWeight = $this->getCartTotalWeight();

            $result = $service->calculateCost(
                origin: $this->getDefaultOriginCityId(),
                destination: $this->city_id,
                weight: $totalWeight,
                courier: 'jne,tiki,pos'
            );

            // 🔥 Cek apakah ada error
            if (isset($result['success']) && $result['success'] === false) {
                $this->error = $result['message'] ?? 'Gagal menghitung ongkir';
                $this->isLoading = false;
                return;
            }

            // 🔥 Cek apakah menggunakan estimasi lokal
            if (!empty($result['is_local'])) {
                $this->notice = $result['message'] ?? 'Menggunakan estimasi ongkir lokal karena API tidak dapat dijangkau.';
            }

            // 🔥 Ambil data dari result
            $results = $result['data'] ?? [];

            if (!$results || empty($results)) {
                $this->error = 'Tidak ada metode pengiriman untuk alamat ini';
                $this->isLoading = false;
                return;
            }

            // Format options
            $this->shippingOptions = [];
            foreach ($results as $courierData) {
                foreach ($courierData['costs'] as $cost) {
                    $this->shippingOptions[] = [
                        'courier' => strtoupper($courierData['code']),
                        'courier_code' => $courierData['code'],
                        'service' => $cost['service'],
                        'description' => $cost['description'] ?? $cost['service'],
                        'cost' => $cost['cost'][0]['value'],
                        'etd' => $cost['cost'][0]['etd'] ?? '?',
                    ];
                }
            }

            // Free shipping check
            $threshold = config('shipping.free_shipping_threshold', 200000);
            if ($this->cartTotal >= $threshold) {
                $this->shippingOptions[] = [
                    'courier' => 'FREE',
                    'courier_code' => 'free',
                    'service' => 'Free Shipping',
                    'description' => 'Gratis Ongkir (Min. Belanja Rp ' . number_format($threshold, 0, ',', '.') . ')',
                    'cost' => 0,
                    'etd' => '5-7',
                ];
            }

            if (empty($this->shippingOptions)) {
                $this->error = 'Tidak ada pilihan pengiriman untuk alamat ini';
            }

            // Save to session
            session()->put('checkout', array_merge(
                session()->get('checkout', []),
                [
                    'city' => $this->city,
                    'city_id' => $this->city_id,
                    'province' => $this->province,
                    'province_id' => $this->province_id,
                    'postal_code' => $this->postal_code,
                ]
            ));

        } catch (\Exception $e) {
            Log::error('Shipping Error: ' . $e->getMessage());
            $this->error = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        $this->isLoading = false;
    }

    private function getCartTotalWeight()
    {
        $cart = Auth::user()->cart;
        if (!$cart) return 1000;

        $totalWeight = 0;
        foreach ($cart->items as $item) {
            $weight = $item->product->weight ?? 250;
            $totalWeight += $weight * $item->quantity;
        }

        return max($totalWeight, 100);
    }

    private function getDefaultOriginCityId()
    {
        // 🔥 PRIORITAS: Baca dari cache (diisi dari Admin Settings)
        // Fallback ke config
        return cache('shipping_default_origin_city') 
            ?? config('shipping.origin_city', 495);
    }



    public function selectShipping($courier, $service, $cost, $etd)
    {
        $this->selectedCourier = $courier;
        $this->selectedService = $service;
        $this->shippingCost = $cost;
        $this->estimatedDays = $etd;

        session()->put('checkout', array_merge(
            session()->get('checkout', []),
            [
                'courier' => $courier,
                'service' => $service,
                'shipping_cost' => $cost,
                'estimated_days' => $etd,
            ]
        ));

        $this->dispatch('shippingUpdated', [
            'shipping_cost' => $cost,
            'total' => $this->cartTotal + $cost,
        ]);
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $user = Auth::user();
            $this->postal_code = $user->postal_code ?? '';
            $this->city = $user->city ?? '';
            $this->province = $user->province ?? '';
            $this->shippingOptions = [];
            $this->shippingCost = 0;
        }
    }

    public function render()
    {
        return view('livewire.shipping-calculator', [
            'filteredProvinces' => $this->provincesList,
            'filteredCities' => $this->citiesList,
        ]);
    }
}