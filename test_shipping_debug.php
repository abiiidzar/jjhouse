<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\RajaOngkirService;

$service = app(RajaOngkirService::class);

echo "=== TEST PROVINSI ===\n";
$provinces = $service->getProvinces();
echo "Jumlah provinsi: " . count($provinces) . "\n";
foreach ($provinces as $p) {
    if (in_array($p['province'], ['Sumatera Barat', 'Banten', 'Sulawesi Tenggara'])) {
        echo "  - ID: {$p['province_id']} => {$p['province']}\n";
    }
}

echo "\n=== TEST KOTA SUMATERA BARAT (32) ===\n";
$cities = $service->getCities(32);
echo "Jumlah kota: " . count($cities) . "\n";
foreach ($cities as $c) {
    echo "  - {$c['city_id']} | {$c['city_name']} | {$c['postal_code']}\n";
}

echo "\n=== TEST KOTA BANTEN (3) ===\n";
$cities = $service->getCities(3);
echo "Jumlah kota: " . count($cities) . "\n";
foreach ($cities as $c) {
    echo "  - {$c['city_id']} | {$c['city_name']} | {$c['postal_code']}\n";
}

echo "\n=== TEST KOTA SULAWESI TENGGARA (30) ===\n";
$cities = $service->getCities(30);
echo "Jumlah kota: " . count($cities) . "\n";
foreach ($cities as $c) {
    echo "  - {$c['city_id']} | {$c['city_name']} | {$c['postal_code']}\n";
}

echo "\n=== TEST GET CITY BY ID ===\n";
$city52 = $service->getCityById(52);
echo "City 52: " . json_encode($city52) . "\n";
$city12 = $service->getCityById(12);
echo "City 12: " . json_encode($city12) . "\n";
