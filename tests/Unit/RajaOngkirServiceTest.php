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
            'services.rajaongkir.base_url' => 'https://shipping.example.test/api/v1',
        ]);
        cache()->put('rajaongkir_provinces', [['province' => 'Cached province']]);
        Http::fake([
            'shipping.example.test/api/v1/destination/domestic-destination*' => Http::response([
                'meta' => ['code' => 200, 'message' => 'Success', 'status' => true],
                'data' => [['id' => 123, 'city_name' => 'Jakarta']],
            ]),
        ]);

        $result = (new RajaOngkirService())->testConnection();

        $this->assertSame('success', $result['status']);
        $this->assertSame('Connected directly to RajaOngkir API', $result['message']);
        $this->assertSame(['destination_count' => 1], $result['data']);
        Http::assertSent(fn ($request) => $request->hasHeader('key', 'test-key')
            && str_contains($request->url(), '/destination/domestic-destination')
            && $request['search'] === 'Jakarta');
    }

    public function test_connection_check_reports_api_authentication_errors(): void
    {
        config([
            'services.rajaongkir.api_key' => 'invalid-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test/api/v1',
        ]);
        Http::fake([
            'shipping.example.test/api/v1/destination/domestic-destination*' => Http::response([
                'meta' => ['code' => 401, 'message' => 'Invalid API key', 'status' => false],
            ], 401),
        ]);

        $result = (new RajaOngkirService())->testConnection();

        $this->assertSame('error', $result['status']);
        $this->assertSame('Invalid API key', $result['message']);
    }

    public function test_calculate_cost_resolves_legacy_city_ids_and_normalizes_new_api_response(): void
    {
        config([
            'services.rajaongkir.api_key' => 'test-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test/api/v1',
        ]);
        cache()->put('rajaongkir_cities_all', [
            ['city_id' => 495, 'city_name' => 'Bukittinggi', 'province_id' => 3],
            ['city_id' => 476, 'city_name' => 'Bekasi', 'province_id' => 9],
        ]);

        Http::fake([
            'shipping.example.test/api/v1/destination/domestic-destination*' => function ($request) {
                $id = $request['search'] === 'Bukittinggi' ? 1001 : 1002;

                return Http::response([
                    'meta' => ['code' => 200, 'message' => 'Success', 'status' => true],
                    'data' => [['id' => $id, 'city_name' => $request['search']]],
                ]);
            },
            'shipping.example.test/api/v1/calculate/domestic-cost' => function ($request) {
                $courier = $request['courier'];

                return Http::response([
                    'meta' => ['code' => 200, 'message' => 'Success', 'status' => true],
                    'data' => [[
                        'code' => $courier,
                        'name' => strtoupper($courier),
                        'service' => 'REG',
                        'description' => 'Regular service',
                        'cost' => 18000,
                        'etd' => '2-3',
                    ]],
                ]);
            },
        ]);

        $courierList = implode(',', config('shipping.couriers'));
        $result = (new RajaOngkirService())->calculateCost(495, 476, 1000, $courierList);

        $this->assertTrue($result['success']);
        $this->assertSame(18000, $result['data'][0]['costs'][0]['cost'][0]['value']);
        $this->assertSame('2-3', $result['data'][0]['costs'][0]['cost'][0]['etd']);
        $costRequests = Http::recorded(
            fn ($request) => str_ends_with($request->url(), '/calculate/domestic-cost')
        );
        $sentCouriers = $costRequests
            ->map(fn (array $pair) => $pair[0]['courier'])
            ->sort()
            ->values()
            ->all();

        $this->assertSame(count(config('shipping.couriers')), $costRequests->count());
        $this->assertSame(collect(config('shipping.couriers'))->sort()->values()->all(), $sentCouriers);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/calculate/domestic-cost')
            && $request->method() === 'POST'
            && $request->hasHeader('key', 'test-key')
            && (string) $request['origin'] === '1001'
            && (string) $request['destination'] === '1002'
            && (string) $request['weight'] === '1000'
            && in_array($request['courier'], config('shipping.couriers'), true));
    }

    public function test_calculate_cost_requires_an_api_key(): void
    {
        config(['services.rajaongkir.api_key' => null]);

        $result = (new RajaOngkirService())->calculateCost(495, 476, 1000, 'jne');

        $this->assertFalse($result['success']);
        $this->assertSame('API key RajaOngkir belum diatur', $result['message']);
    }

    public function test_calculate_cost_rejects_unknown_couriers_before_calling_api(): void
    {
        config(['services.rajaongkir.api_key' => 'test-key']);
        Http::fake();

        $result = (new RajaOngkirService())->calculateCost(495, 476, 1000, 'jne,unknown');

        $this->assertFalse($result['success']);
        $this->assertSame('Kode kurir tidak valid: unknown', $result['message']);
        Http::assertNothingSent();
    }

    public function test_cargo_options_are_filtered_by_package_weight(): void
    {
        $service = new RajaOngkirService();
        $couriers = [[
            'code' => 'jne',
            'costs' => array_map(
                fn (string $code): array => ['service' => $code],
                ['REG', 'JTR', 'JTR<130', 'JTR>130', 'JTR>200', 'Idtruck', 'BIGPACK', 'MIC', 'DRGREG']
            ),
        ]];

        $underFiveKg = $service->filterCargoOptions($couriers, 4999);
        $atFiveKg = $service->filterCargoOptions($couriers, 5000);
        $underOneHundredKg = $service->filterCargoOptions($couriers, 99999);
        $atOneHundredKg = $service->filterCargoOptions($couriers, 100000);

        $this->assertSame(['REG'], array_column($underFiveKg[0]['costs'], 'service'));
        $this->assertSame(
            ['REG', 'JTR', 'Idtruck', 'BIGPACK', 'MIC', 'DRGREG'],
            array_column($atFiveKg[0]['costs'], 'service')
        );
        $this->assertSame(
            ['REG', 'JTR', 'Idtruck', 'BIGPACK', 'MIC', 'DRGREG'],
            array_column($underOneHundredKg[0]['costs'], 'service')
        );
        $this->assertSame(9, count($atOneHundredKg[0]['costs']));
        $this->assertTrue($atFiveKg[0]['costs'][1]['is_cargo']);
        $this->assertFalse($atFiveKg[0]['costs'][0]['is_cargo']);
    }

    public function test_cost_results_normalize_eta_and_pos_description(): void
    {
        config([
            'services.rajaongkir.api_key' => 'test-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test/api/v1',
            'shipping.couriers' => ['jne', 'pos'],
        ]);
        cache()->put('rajaongkir_cities_all', [
            ['city_id' => 495, 'city_name' => 'Bukittinggi', 'province_id' => 3],
            ['city_id' => 476, 'city_name' => 'Bekasi', 'province_id' => 9],
        ]);

        Http::fake([
            'shipping.example.test/api/v1/destination/domestic-destination*' => function ($request) {
                $id = $request['search'] === 'Bukittinggi' ? 1001 : 1002;

                return Http::response([
                    'meta' => ['code' => 200, 'message' => 'Success', 'status' => true],
                    'data' => [['id' => $id, 'city_name' => $request['search']]],
                ]);
            },
            'shipping.example.test/api/v1/calculate/domestic-cost' => function ($request) {
                $isPos = $request['courier'] === 'pos';

                return Http::response([
                    'meta' => ['code' => 200, 'message' => 'Success', 'status' => true],
                    'data' => [[
                        'code' => $request['courier'],
                        'name' => strtoupper($request['courier']),
                        'service' => $isPos ? 'Pos Reguler' : 'REG',
                        'description' => $isPos ? '240' : 'Regular service',
                        'cost' => 18000,
                        'etd' => $isPos ? '-' : '4 day',
                    ]],
                ]);
            },
        ]);

        $result = (new RajaOngkirService())->calculateCost(495, 476, 1000, 'jne,pos');
        $services = collect($result['data'])->keyBy('code');

        $this->assertSame('4', $services['jne']['costs'][0]['cost'][0]['etd']);
        $this->assertSame('Layanan Pos Reguler', $services['pos']['costs'][0]['description']);
        $this->assertNull($services['pos']['costs'][0]['cost'][0]['etd']);
    }

    public function test_calculate_cost_uses_cached_results_for_repeat_requests(): void
    {
        config([
            'services.rajaongkir.api_key' => 'test-key',
            'services.rajaongkir.base_url' => 'https://shipping.example.test/api/v1',
        ]);
        cache()->put('rajaongkir_cities_all', [
            ['city_id' => 495, 'city_name' => 'Bukittinggi', 'province_id' => 3],
            ['city_id' => 476, 'city_name' => 'Bekasi', 'province_id' => 9],
        ]);

        Http::fake([
            'shipping.example.test/api/v1/destination/domestic-destination*' => function ($request) {
                $id = $request['search'] === 'Bukittinggi' ? 1001 : 1002;

                return Http::response([
                    'meta' => ['code' => 200],
                    'data' => [['id' => $id, 'city_name' => $request['search']]],
                ]);
            },
            'shipping.example.test/api/v1/calculate/domestic-cost' => Http::response([
                'meta' => ['code' => 200],
                'data' => [[
                    'code' => 'jne',
                    'name' => 'JNE',
                    'service' => 'REG',
                    'description' => 'Regular service',
                    'cost' => 18000,
                    'etd' => '2-3',
                ]],
            ]),
        ]);

        $service = new RajaOngkirService();
        $firstResult = $service->calculateCost(495, 476, 1000, 'jne');
        $secondResult = $service->calculateCost(495, 476, 1000, 'jne');

        $this->assertTrue($firstResult['success']);
        $this->assertSame($firstResult['data'], $secondResult['data']);
        $costRequests = Http::recorded(
            fn ($request) => str_ends_with($request->url(), '/calculate/domestic-cost')
        );
        $this->assertCount(1, $costRequests);
    }
}
