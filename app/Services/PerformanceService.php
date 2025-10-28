<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    public static function cacheQuery(string $key, \Closure $callback, int $minutes = 60)
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }

    public static function optimizeQuery($query)
    {
        return $query->select(['id', 'name', 'created_at'])
                    ->limit(100)
                    ->orderBy('created_at', 'desc');
    }

    public static function batchInsert(string $table, array $data, int $chunkSize = 1000): void
    {
        collect($data)->chunk($chunkSize)->each(function ($chunk) use ($table) {
            DB::table($table)->insert($chunk->toArray());
        });
    }

    public static function clearCache(array $keys = []): void
    {
        if (empty($keys)) {
            Cache::flush();
        } else {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }
}