<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RajaOngkirService;

class CheckOriginCity extends Command
{
    protected $signature = 'shipping:check-origin';
    protected $description = 'Check the configured origin city for shipping';

    public function handle()
    {
        $service = new RajaOngkirService();
        $originId = $service->getOriginCity();
        $originName = $service->getOriginCityName();

        $this->info('=== Shipping Origin Configuration ===');
        $this->line('Origin City ID: ' . $originId);
        $this->line('Origin City Name: ' . $originName);
        $this->line('-------------------------------------');
        $this->line('Config source: config/services.rajaongkir.origin_city');
        $this->line('ENV variable: SHIPPING_ORIGIN_CITY=' . env('SHIPPING_ORIGIN_CITY', 'not set'));

        // Test connection
        $this->newLine();
        $this->info('Testing connection to RajaOngkir...');
        
        $test = $service->testConnection();
        $this->line('Status: ' . ($test['status'] ?? 'unknown'));
        $this->line('Message: ' . ($test['message'] ?? ''));
        
        // Test calculate cost
        $this->newLine();
        $this->info('Testing calculate cost (Jakarta Selatan to Agam)...');
        
        $result = $service->calculateCost(
            origin: 495,
            destination: 493,
            weight: 1000,
            courier: 'jne'
        );
        
        if (isset($result['success']) && $result['success']) {
            $this->info('✅ Calculate cost successful!');
            $this->line('Number of options: ' . count($result['data']));
        } else {
            $this->error('❌ Calculate cost failed: ' . ($result['message'] ?? 'Unknown error'));
        }
    }
}