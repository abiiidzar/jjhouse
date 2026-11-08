<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\RajaOngkirService;

$service = new RajaOngkirService();

echo "=== TEST CALCULATE COST LOCAL ===\n";

// Test 1: Sumatera Barat (32) ke Sumatera Barat (32) - dalam Sumatera
$result = $service->calculateCostLocal(495, 12, 1000, 'jne,tiki,pos');
echo "Test 1 (Sumbar ke Sumbar):\n";
echo "  Success: " . ($result['success'] ? 'true' : 'false') . "\n";
echo "  is_local: " . ($result['is_local'] ?? 'N/A') . "\n";
echo "  Message: " . ($result['message'] ?? 'N/A') . "\n";
if (!empty($result['data'])) {
    foreach ($result['data'] as $courier) {
        echo "  Courier: {$courier['code']} ({$courier['name']})\n";
        foreach ($courier['costs'] as $cost) {
            echo "    - {$cost['service']}: Rp " . number_format($cost['cost'][0]['value']) . " (ETD: {$cost['cost'][0]['etd']})\n";
        }
    }
}
echo "\n";

// Test 2: Sumatera Barat (32) ke Sulawesi Tenggara (30) - antar pulau
$result2 = $service->calculateCostLocal(495, 52, 1000, 'jne,tiki,pos');
echo "Test 2 (Sumbar ke Sultra):\n";
echo "  Success: " . ($result2['success'] ? 'true' : 'false') . "\n";
echo "  is_local: " . ($result2['is_local'] ?? 'N/A') . "\n";
echo "  Message: " . ($result2['message'] ?? 'N/A') . "\n";
if (!empty($result2['data'])) {
    foreach ($result2['data'] as $courier) {
        echo "  Courier: {$courier['code']} ({$courier['name']})\n";
        foreach ($courier['costs'] as $cost) {
            echo "    - {$cost['service']}: Rp " . number_format($cost['cost'][0]['value']) . " (ETD: {$cost['cost'][0]['etd']})\n";
        }
    }
}
echo "\n";

// Test 3: Test calculateCost dengan API key kosong (harus langsung fallback lokal)
echo "=== TEST CALCULATE COST (API Key Kosong) ===\n";
$result3 = $service->calculateCost(495, 12, 1000, 'jne');
echo "  Success: " . ($result3['success'] ? 'true' : 'false') . "\n";
echo "  is_local: " . ($result3['is_local'] ?? 'N/A') . "\n";
echo "  Message: " . ($result3['message'] ?? 'N/A') . "\n";
