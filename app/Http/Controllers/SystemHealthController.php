<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemHealthController extends Controller
{
    public function checkSystem()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'permissions' => $this->checkPermissions()
        ];

        return response()->json([
            'status' => 'ok',
            'checks' => $checks,
            'timestamp' => now()
        ]);
    }

    public function fixCommonIssues(Request $request)
    {
        $fixes = [];
        
        try {
            // Clear cache
            Cache::flush();
            $fixes[] = 'Cache cleared';
            
            // Clear config cache
            \Artisan::call('config:clear');
            $fixes[] = 'Config cache cleared';
            
            // Clear route cache
            \Artisan::call('route:clear');
            $fixes[] = 'Route cache cleared';
            
            return response()->json([
                'status' => 'success',
                'fixes_applied' => $fixes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'test', 60);
            $value = Cache::get('health_check');
            return ['status' => $value === 'test' ? 'ok' : 'error'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        try {
            $writable = is_writable(storage_path());
            return ['status' => $writable ? 'ok' : 'error', 'writable' => $writable];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkPermissions()
    {
        $paths = [
            storage_path(),
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views')
        ];

        $results = [];
        foreach ($paths as $path) {
            $results[$path] = is_writable($path);
        }

        return $results;
    }
}