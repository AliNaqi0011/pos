<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LoggingService
{
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->warning($event, array_merge($context, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]));
    }

    public static function logApiRequest(Request $request, $response = null): void
    {
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'response_status' => $response ? $response->getStatusCode() : null,
            'timestamp' => now()->toISOString()
        ]);
    }

    public static function logError(\Exception $exception, array $context = []): void
    {
        Log::error($exception->getMessage(), array_merge($context, [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toISOString()
        ]));
    }

    public static function logPerformance(string $operation, float $duration, array $context = []): void
    {
        Log::channel('performance')->info("Performance: {$operation}", array_merge($context, [
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString()
        ]));
    }
}