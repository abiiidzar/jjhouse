<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Auth;

class ShippingCalculator extends Component
{
    public $postal_code = '';
    public $city = '';
    public $province = '';
    public $shippingOptions = [];
    public $selectedCourier = null;
    public $selectedService = null;
    public $shippingCost = 0;
    public $estimatedDays = '';
    public $isLoading = false;

    public $error = null;
    public $cartTotal = 0;
    public $isEditing = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->postal_code = $user->postal_code ?? '';
            $this->city = $user->city ?? '';
            $this->province = $user->province ?? '';
        }

        // Get cart subtotal
        $cart = Auth::user()->cart;
        if ($cart) {
            $this->cartTotal = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });
        }

        // Load previous selection from session
        $shipping = session()->get('checkout', []);
        if ($shipping) {
            $this->selectedCourier = $shipping['courier'] ?? null;
            $this->selectedService = $shipping['service'] ?? null;
            $this->shippingCost = $shipping['shipping_cost'] ?? 0;
            $this->estimatedDays = $shipping['estimated_days'] ?? 0;
        }
    }

    // public function calculateShipping()
    // {
    //     // Validation
    //     if (!$this->postal_code) {
    //         $this->error = 'Postal code is required';
    //         return;
    //     }

    //     if (!$this->city) {
    //         $this->error = 'City is required';
    //         return;
    //     }
    //     $this->isLoading = true;
    //     $this->error = null;
    //     $this->shippingOptions = [];

    //     try {
    //         $service = new RajaOngkirService();

    //         $destinationCityId = $this->getCityIdByName($this->city, $this->province);

    //         // Get available shipping methods
    //         $options = $service->getShippingCost(
    //             origin: 1, // Default origin city (should be configurable)
    //             destination: $this->postal_code,
    //             weight: 1000, // Default weight, should calculate from cart items
    //             courier: 'jne,pos,tiki' // All couriers
    //         );

    //         if (!$options || empty($options)) {
    //             $this->error = 'No shipping methods available for this location';
    //             $this->isLoading = false;
    //             return;
    //         }

    //         // Format options
    //         $this->shippingOptions = [];
    //         foreach ($options as $option) {
    //             foreach ($option['costs'] as $cost) {
    //                 $this->shippingOptions[] = [
    //                     'courier' => strtoupper($option['code']),
    //                     'courier_code' => $option['code'],
    //                     'service' => $cost['service'],
    //                     'description' => $cost['description'],
    //                     'cost' => $cost['cost'][0]['value'],
    //                     'etd' => $cost['cost'][0]['etd'],
    //                 ];
    //             }
    //         }

    //         // Check free shipping threshold
    //         $freeShippingThreshold = cache('shipping_free_shipping_threshold', 0);
    //         if ($this->cartTotal >= $freeShippingThreshold && $freeShippingThreshold > 0) {
    //             $this->shippingOptions[] = [
    //                 'courier' => 'FREE',
    //                 'courier_code' => 'free',
    //                 'service' => 'Free Shipping',
    //                 'description' => 'Gratis Ongkir',
    //                 'cost' => 0,
    //                 'etd' => '5-7',
    //             ];
    //         }
    //     } catch (\Exception $e) {
    //         $this->error = 'Error calculating shipping: ' . $e->getMessage();
    //     }

    //     $this->isLoading = false;
    // }
    public function calculateShipping()
    {
        // Validation
        if (!$this->postal_code) {
            $this->error = 'Postal code is required';
            return;
        }

        if (!$this->city) {
            $this->error = 'City is required';
            return;
        }

        $this->isLoading = true;
        $this->error = null;
        $this->shippingOptions = [];

        try {
            $service = new RajaOngkirService();

            // 🔥 CARI CITY ID DARI NAMA KOTA
            $destinationCityId = $this->getCityIdByName($this->city, $this->province);

            if (!$destinationCityId) {
                $this->error = "Kota '{$this->city}' tidak ditemukan. Periksa kembali.";
                $this->isLoading = false;
                return;
            }

            // 🔥 HITUNG TOTAL BERAT CART
            $totalWeight = $this->getCartTotalWeight();

            // 🔥 GET SHIPPING COSTS
            $results = $service->getShippingCost(
                origin: $this->getDefaultOriginCityId(), // Fungsi baru untuk dapat origin
                destination: $destinationCityId,
                weight: $totalWeight,
                courier: 'jne,pos,tiki'
            );

            if (!$results || empty($results)) {
                $this->error = 'No shipping methods available for this location';
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
                        'description' => $cost['description'],
                        'cost' => $cost['cost'][0]['value'],
                        'etd' => $cost['cost'][0]['etd'] ?? '?',
                    ];
                }
            }

            // Free shipping check
            if ($this->cartTotal >= 200000) {
                $this->shippingOptions[] = [
                    'courier' => 'FREE',
                    'courier_code' => 'free',
                    'service' => 'Free Shipping',
                    'description' => 'Gratis Ongkir (Min. Belanja Rp 200.000)',
                    'cost' => 0,
                    'etd' => '5-7',
                ];
            }

            if (empty($this->shippingOptions)) {
                $this->error = 'Tidak ada pilihan pengiriman untuk alamat ini';
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shipping Error: ' . $e->getMessage());
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->isLoading = false;
    }

    // 🔥 TAMBAHKAN METHOD INI:

    private function getCityIdByName($cityName, $provinceName = null)
    {
        try {
            $service = new RajaOngkirService();
            $cities = $service->getCities();

            foreach ($cities as $city) {
                // Cari berdasarkan nama kota (case insensitive)
                if (stripos($city['city_name'], $cityName) !== false) {
                    // Jika ada province, cocokkan juga
                    if ($provinceName) {
                        if (stripos($city['province'], $provinceName) !== false) {
                            return $city['city_id'];
                        }
                    } else {
                        return $city['city_id'];
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('City lookup error: ' . $e->getMessage());
        }

        return null;
    }

    private function getCartTotalWeight()
    {
        $cart = Auth::user()->cart;
        if (!$cart) return 1000; // default 1kg

        $totalWeight = 0;
        foreach ($cart->items as $item) {
            $weight = $item->product->weight ?? 250; // default 250gram
            $totalWeight += $weight * $item->quantity;
        }

        return max($totalWeight, 100); // minimal 100 gram
    }

    private function getDefaultOriginCityId()
    {
        // 🔥 GANTI DENGAN CITY ID TOKO ANDA
        // Contoh: Bandung = 23, Jakarta Selatan = 152, Surabaya = 444
        return \Illuminate\Support\Facades\Cache::get('shipping_default_origin_city', 475); // Ganti 475 dengan city ID kota toko Anda
    }

    public function selectShipping($courier, $service, $cost, $etd)
    {
        $this->selectedCourier = $courier;
        $this->selectedService = $service;
        $this->shippingCost = $cost;
        $this->estimatedDays = $etd;

        // Save to session
        session()->put('checkout', [
            'courier' => $courier,
            'service' => $service,
            'shipping_cost' => $cost,
            'estimated_days' => $etd,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'province' => $this->province,
        ]);

        $this->dispatch('shippingUpdated', [
            'shipping_cost' => $cost,
            'total' => $this->cartTotal + $cost,
        ]);
    }

    public function render()
    {
        return view('livewire.shipping-calculator');
    }
    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            // Jika batal edit, kembalikan ke data profile asli
            $user = Auth::user();
            $this->postal_code = $user->postal_code ?? '';
            $this->city = $user->city ?? '';
            $this->province = $user->province ?? '';
            $this->shippingOptions = []; // Reset pilihan
        }
    }
}
