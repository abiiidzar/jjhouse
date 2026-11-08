<?php

namespace Tests\Unit;

use App\Services\RajaOngkirService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RajaOngkirServiceTest extends TestCase
{
    public function test_connection_check_calls_api_directly_instead_of_using_cached_provinces(): void
    {
        config([
            'services.rajaongkir.api_key' => 'test-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test',
        ]);
        cache()->put('rajaongkir_provinces', [['province' => 'Cached province']]);
        Http::fake([
            'shipping.example.test/province' => Http::response([
                'rajaongkir' => [
                    'status' => ['code' => 200, 'description' => 'OK'],
                    'results' => [['province_id' => 1, 'province' => 'API province']],
                ],
            ]),
        ]);

        $result = (new RajaOngkirService())->testConnection();

        $this->assertSame('success', $result['status']);
        $this->assertSame('Connected directly to RajaOngkir API', $result['message']);
        $this->assertSame(['province_count' => 1], $result['data']);
        Http::assertSent(fn ($request) => $request->hasHeader('key', 'test-key'));
    }

    public function test_connection_check_reports_api_authentication_errors(): void
    {
        config([
            'services.rajaongkir.api_key' => 'invalid-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test',
        ]);
        Http::fake([
            'shipping.example.test/province' => Http::response([
                'rajaongkir' => [
                    'status' => ['code' => 401, 'description' => 'Invalid API key'],
                ],
            ]),
        ]);

        $result = (new RajaOngkirService())->testConnection();

        $this->assertSame('error', $result['status']);
        $this->assertSame('Invalid API key', $result['message']);
    }
}
