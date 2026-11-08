<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST SANDBOX MODE RAJAONGKIR ===\n\n";

$apiKey = config('services.rajaongkir.api_key');
echo "API Key: " . ($apiKey ? substr($apiKey, 0, 8) . '...' . substr($apiKey, -4) : 'KOSONG') . "\n\n";

// Daftar URL yang mungkin digunakan untuk sandbox/starter
$urls = [
    'Starter (production)' => 'https://api.rajaongkir.com/starter/province',
    'Starter (sandbox)' => 'https://api.rajaongkir.com/starter/province',
    'Sandbox subdomain' => 'https://sandbox.rajaongkir.com/starter/province',
    'Pro' => 'https://pro.rajaongkir.com/api/province',
    'Basic' => 'https://api.rajaongkir.com/basic/province',
];

foreach ($urls as $name => $url) {
    echo "--- $name ---\n";
    echo "URL: $url\n";
    
    $start = microtime(true);
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(5)
            ->withHeaders(['key' => $apiKey])
            ->get($url);
        
        $elapsed = round((microtime(true) - $start) * 1000);
        echo "Status: " . $response->status() . " (waktu: {$elapsed}ms)\n";
        
        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['rajaongkir']['status']['code'])) {
                echo "RajaOngkir Code: " . $result['rajaongkir']['status']['code'] . "\n";
                echo "RajaOngkir Desc: " . ($result['rajaongkir']['status']['description'] ?? '') . "\n";
                echo "Jumlah Provinsi: " . count($result['rajaongkir']['results'] ?? []) . "\n";
            }
            echo "✅ BERHASIL!\n";
            break;
        } else {
            echo "Body: " . substr($response->body(), 0, 150) . "\n";
        }
    } catch (\Exception $e) {
        $elapsed = round((microtime(true) - $start) * 1000);
        echo "❌ Gagal ({$elapsed}ms): " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Cek konfigurasi sandbox
echo "\n=== CEK KONFIGURASI SANDBOX ===\n";
echo "Base URL dari config: " . config('services.rajaongkir.base_url') . "\n";
echo "Env RAJAONGKIR_BASE_URL: " . (env('RAJAONGKIR_BASE_URL') ?: 'tidak diset') . "\n";
echo "Env RAJAONGKIR_API_KEY: " . (env('RAJAONGKIR_API_KEY') ? substr(env('RAJAONGKIR_API_KEY'), 0, 8) . '...' : 'tidak diset') . "\n";
