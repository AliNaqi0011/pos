<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'memory' => $this->checkMemory(),
            'disk_space' => $this->checkDiskSpace(),
        ];

        $overall = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'status' => $overall ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'checks' => $checks
        ]);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $count = DB::table('users')->count();
            return [
                'status' => 'ok',
                'message' => "Database connected. {$count} users found.",
                'response_time' => $this->measureTime(fn() => DB::select('SELECT 1'))
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            
            return [
                'status' => $value === 'test' ? 'ok' : 'error',
                'message' => $value === 'test' ? 'Cache working' : 'Cache not working'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache error: ' . $e->getMessage()
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'test');
            $exists = Storage::exists($testFile);
            Storage::delete($testFile);
            
            return [
                'status' => $exists ? 'ok' : 'error',
                'message' => $exists ? 'Storage working' : 'Storage not working'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage error: ' . $e->getMessage()
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $connection = config('queue.default');
            return [
                'status' => 'ok',
                'message' => "Queue connection: {$connection}"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Queue error: ' . $e->getMessage()
            ];
        }
    }

    private function checkMemory(): array
    {
        $used = memory_get_usage(true);
        $limit = ini_get('memory_limit');
        $limitBytes = $this->convertToBytes($limit);
        $percentage = ($used / $limitBytes) * 100;

        return [
            'status' => $percentage < 80 ? 'ok' : 'warning',
            'message' => sprintf('Memory usage: %s / %s (%.1f%%)', 
                $this->formatBytes($used), 
                $limit, 
                $percentage
            ),
            'usage_percentage' => round($percentage, 1)
        ];
    }

    private function checkDiskSpace(): array
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        $percentage = (($total - $free) / $total) * 100;

        return [
            'status' => $percentage < 90 ? 'ok' : 'warning',
            'message' => sprintf('Disk usage: %.1f%% used', $percentage),
            'usage_percentage' => round($percentage, 1)
        ];
    }

    private function measureTime(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        return round((microtime(true) - $start) * 1000, 2);
    }

    private function convertToBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $number = (int) $value;
        
        return match($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number
        };
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($bytes, 1024));
        return sprintf('%.2f %s', $bytes / (1024 ** $factor), $units[$factor]);
    }
}