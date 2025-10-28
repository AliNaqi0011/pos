<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthCheckController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $healthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'checks' => $checks
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkCache(): array
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            return ['status' => $value === 'ok' ? 'ok' : 'error', 'message' => 'Cache check'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache check failed'];
        }
    }

    private function checkStorage(): array
    {
        try {
            $writable = is_writable(storage_path());
            return [
                'status' => $writable ? 'ok' : 'error',
                'message' => $writable ? 'Storage writable' : 'Storage not writable'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage check failed'];
        }
    }
}