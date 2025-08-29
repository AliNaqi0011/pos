<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemHealthController extends Controller
{
    public function checkSystem()
    {
        $checks = [];
        
        // Database Connection Check
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'OK', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            $checks['database'] = ['status' => 'ERROR', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
        
        // Tables Check
        $requiredTables = [
            'users', 'products', 'categories', 'brands', 'customers', 
            'warehouses', 'sales', 'sale_items', 'purchases', 'purchase_items',
            'notifications', 'roles', 'permissions', 'model_has_roles'
        ];
        
        $missingTables = [];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            $checks['tables'] = ['status' => 'OK', 'message' => 'All required tables exist'];
        } else {
            $checks['tables'] = ['status' => 'ERROR', 'message' => 'Missing tables: ' . implode(', ', $missingTables)];
        }
        
        // Models Check
        try {
            $userCount = User::count();
            $productCount = Product::count();
            $saleCount = Sale::count();
            
            $checks['models'] = [
                'status' => 'OK', 
                'message' => "Models working - Users: {$userCount}, Products: {$productCount}, Sales: {$saleCount}"
            ];
        } catch (\Exception $e) {
            $checks['models'] = ['status' => 'ERROR', 'message' => 'Model access failed: ' . $e->getMessage()];
        }
        
        // Roles Check
        try {
            $superAdminExists = User::role('super_admin')->exists();
            $adminExists = User::role('admin')->exists();
            
            $checks['roles'] = [
                'status' => $superAdminExists ? 'OK' : 'WARNING',
                'message' => "Super Admin exists: " . ($superAdminExists ? 'Yes' : 'No') . ", Admin exists: " . ($adminExists ? 'Yes' : 'No')
            ];
        } catch (\Exception $e) {
            $checks['roles'] = ['status' => 'ERROR', 'message' => 'Roles check failed: ' . $e->getMessage()];
        }
        
        // File Permissions Check
        $storageWritable = is_writable(storage_path());
        $publicWritable = is_writable(public_path());
        
        $checks['permissions'] = [
            'status' => ($storageWritable && $publicWritable) ? 'OK' : 'ERROR',
            'message' => "Storage writable: " . ($storageWritable ? 'Yes' : 'No') . ", Public writable: " . ($publicWritable ? 'Yes' : 'No')
        ];
        
        // Routes Check
        $criticalRoutes = [
            'dashboard', 'pos', 'products.index', 'admin.sales.index', 
            'notifications.index', 'reports.index'
        ];
        
        $routeIssues = [];
        foreach ($criticalRoutes as $routeName) {
            try {
                route($routeName);
            } catch (\Exception $e) {
                $routeIssues[] = $routeName;
            }
        }
        
        $checks['routes'] = [
            'status' => empty($routeIssues) ? 'OK' : 'ERROR',
            'message' => empty($routeIssues) ? 'All critical routes exist' : 'Missing routes: ' . implode(', ', $routeIssues)
        ];
        
        return response()->json([
            'overall_status' => $this->getOverallStatus($checks),
            'checks' => $checks,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
    
    private function getOverallStatus($checks)
    {
        $hasError = false;
        $hasWarning = false;
        
        foreach ($checks as $check) {
            if ($check['status'] === 'ERROR') {
                $hasError = true;
            } elseif ($check['status'] === 'WARNING') {
                $hasWarning = true;
            }
        }
        
        if ($hasError) return 'ERROR';
        if ($hasWarning) return 'WARNING';
        return 'OK';
    }
    
    public function fixCommonIssues()
    {
        $fixes = [];
        
        // Fix missing walk-in customer
        try {
            $walkInCustomer = Customer::firstOrCreate(
                ['name' => 'Walk-in Customer'],
                ['email' => 'walkin@customer.com', 'phone' => '0000000000']
            );
            $fixes['walk_in_customer'] = 'Walk-in customer ensured';
        } catch (\Exception $e) {
            $fixes['walk_in_customer'] = 'Failed to create walk-in customer: ' . $e->getMessage();
        }
        
        // Clear cache
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            $fixes['cache'] = 'Cache cleared successfully';
        } catch (\Exception $e) {
            $fixes['cache'] = 'Failed to clear cache: ' . $e->getMessage();
        }
        
        // Fix storage link
        try {
            if (!file_exists(public_path('storage'))) {
                \Artisan::call('storage:link');
                $fixes['storage_link'] = 'Storage link created';
            } else {
                $fixes['storage_link'] = 'Storage link already exists';
            }
        } catch (\Exception $e) {
            $fixes['storage_link'] = 'Failed to create storage link: ' . $e->getMessage();
        }
        
        return response()->json([
            'status' => 'completed',
            'fixes' => $fixes,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}