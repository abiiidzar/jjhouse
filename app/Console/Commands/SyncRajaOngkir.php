<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncRajaOngkir extends Command
{
    protected $signature = 'rajaongkir:sync {--timeout=30 : HTTP timeout in seconds}';
    protected $description = 'Sync RajaOngkir provinces and cities data to local JSON fallback files';

    public function handle(): int
    {
        $apiKey = cache('shipping_rajaongkir_api_key') ?? config('services.rajaongkir.api_key');
        $baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
        $timeout = (int) $this->option('timeout');

        $this->info("API Key: " . substr($apiKey, 0, 6) . '...');
        $this->info("Base URL: $baseUrl");

        // --- PROVINCES ---
        $this->info("\n📍 Fetching provinces...");
        try {
            $response = Http::timeout($timeout)->withHeaders(['key' => $apiKey])->get("$baseUrl/province");
            if ($response->successful()) {
                $result = $response->json();
                if (($result['rajaongkir']['status']['code'] ?? null) == 200) {
                    $provinces = $result['rajaongkir']['results'];
                    $path = resource_path('data/rajaongkir_provinces.json');
                    file_put_contents($path, json_encode($provinces, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $this->info('✅ Saved ' . count($provinces) . ' provinces.');
                } else {
                    $this->error('❌ API error: ' . json_encode($result['rajaongkir']['status'] ?? 'unknown'));
                    return 1;
                }
            } else {
                $this->error('❌ HTTP error: ' . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ ' . $e->getMessage());
            $this->warn('Tip: Aktifkan VPN lalu jalankan ulang: php artisan rajaongkir:sync');
            return 1;
        }

        // --- CITIES ---
        $this->info("\n🏙️  Fetching all cities...");
        try {
            $response = Http::timeout($timeout)->withHeaders(['key' => $apiKey])->get("$baseUrl/city");
            if ($response->successful()) {
                $result = $response->json();
                if (($result['rajaongkir']['status']['code'] ?? null) == 200) {
                    $cities = $result['rajaongkir']['results'];
                    $path = resource_path('data/rajaongkir_cities.json');
                    file_put_contents($path, json_encode($cities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $this->info('✅ Saved ' . count($cities) . ' cities.');
                } else {
                    $this->error('❌ API error: ' . json_encode($result['rajaongkir']['status'] ?? 'unknown'));
                    return 1;
                }
            } else {
                $this->error('❌ HTTP error: ' . $response->status());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ ' . $e->getMessage());
            $this->warn('Tip: Aktifkan VPN lalu jalankan ulang: php artisan rajaongkir:sync');
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Sync selesai! Data tersimpan di resources/data/');
        $this->info('Kalkulasi ongkir sekarang menggunakan data lokal sebagai fallback.');
        return 0;
    }
}
