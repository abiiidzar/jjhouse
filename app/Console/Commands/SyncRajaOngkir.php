<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class SyncRajaOngkir extends Command
{
    protected $signature = 'rajaongkir:sync {--timeout=30 : HTTP timeout in seconds}';
    protected $description = 'Check RajaOngkir Shipping Cost API connectivity';

    public function handle(): int
    {
        $timeout = (int) $this->option('timeout');
        $result = app(RajaOngkirService::class)->testConnection($timeout);

        if ($result['status'] !== 'success') {
            $this->error('RajaOngkir connection failed: ' . $result['message']);
            return self::FAILURE;
        }

        $this->info($result['message']);
        $this->info('Destination data is searched on demand; no full province/city sync is required.');
        return 0;
    }
}
