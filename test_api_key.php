<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\RajaOngkirService;

echo "=== TEST API KEY RAJAONGKIR ===\n\n";

// 1. Cek dari cache (Admin Settings)
$cacheKey = cache('shipping_rajaongkir_api_key');
echo "1. API Key dari Cache (Admin Settings): " . ($cacheKey ? substr($cacheKey, 0, 8) . '...' . substr($cacheKey, -4) : 'KOSONG') . "\n";

// 2. Cek dari config (.env)
$configKey = config('services.rajaongkir.api_key');
echo "2. API Key dari Config (.env): " . ($configKey ? substr($configKey, 0, 8) . '...' . substr($configKey, -4) : 'KOSONG') . "\n";

// 3. Cek dari service
$service = new RajaOngkirService();
$reflection = new ReflectionClass($service);
$property = $reflection->getProperty('apiKey');
$property->setAccessible(true);
$serviceKey = $property->getValue($service);
echo "3. API Key dari Service: " . ($serviceKey ? substr($serviceKey, 0, 8) . '...' . substr($serviceKey, -4) : 'KOSONG') . "\n";

// 4. Test koneksi ke API
echo "\n=== TEST KONEKSI KE API RAJAONGKIR ===\n";
try {
    $response = \Illuminate\Support\Facades\Http::timeout(10)
        ->withHeaders(['key' => $serviceKey])
        ->get('https://api.rajaongkir.com/starter/province');

    echo "Status: " . $response->status() . "\n";
    echo "Body (awal): " . substr($response->body(), 0, 200) . "\n";

    if ($response->successful()) {
        $result = $response->json();
        if (isset($result['rajaongkir']['status']['code'])) {
            echo "RajaOngkir Status Code: " . $result['rajaongkir']['status']['code'] . "\n";
            echo "RajaOngkir Status Desc: " . ($result['rajaongkir']['status']['description'] ?? '') . "\n";
        }
    } else {
        echo "Response tidak sukses\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// 5. Test hitung ongkir langsung ke API
echo "\n=== TEST HITUNG ONGKIR LANGSUNG KE API ===\n";
try {
    $response2 = \Illuminate\Support\Facades\Http::timeout(10)
        ->withHeaders([
            'key' => $serviceKey,
            'content-type' => 'application/x-www-form-urlencoded',
        ])
        ->post('https://api.rajaongkir.com/starter/cost', [
            'origin' => 495,
            'destination' => 12,
            'weight' => 1000,
            'courier' => 'jne',
        ]);

    echo "Status: " . $response2->status() . "\n";
    echo "Body (awal): " . substr($response2->body(), 0, 300) . "\n";

    if ($response2->successful()) {
        $result2 = $response2->json();
        if (isset($result2['rajaongkir']['status']['code'])) {
            echo "RajaOngkir Status Code: " . $result2['rajaongkir']['status']['code'] . "\n";
            echo "RajaOngkir Status Desc: " . ($result2['rajaongkir']['status']['description'] ?? '') . "\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
