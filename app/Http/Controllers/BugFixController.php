<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BugFixController extends Controller
{
    public function fixCommonIssues()
    {
        try {
            // Fix 1: Ensure all users have proper roles
            $this->fixUserRoles();
            
            // Fix 2: Fix product stock calculations
            $this->fixProductStock();
            
            // Fix 3: Fix sale calculations
            $this->fixSaleCalculations();
            
            // Fix 4: Clean up orphaned records
            $this->cleanupOrphanedRecords();
            
            return response()->json([
                'success' => true,
                'message' => 'Common issues fixed successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bug fix failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fix issues: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function fixUserRoles()
    {
        // Assign default role to users without roles
        $usersWithoutRoles = User::doesntHave('roles')->get();
        
        foreach ($usersWithoutRoles as $user) {
            if ($user->email === 'superadmin@example.com') {
                $user->assignRole('super_admin');
            } elseif ($user->email === 'admin@example.com') {
                $user->assignRole('admin');
            } else {
                $user->assignRole('seller');
            }
        }
        
        Log::info('Fixed roles for ' . $usersWithoutRoles->count() . ' users');
    }
    
    private function fixProductStock()
    {
        // Fix negative stock quantities
        Product::where('quantity', '<', 0)->update(['quantity' => 0]);
        
        // Set default stock alert level if null
        Product::whereNull('stock_alert_level')->update(['stock_alert_level' => 10]);
        
        Log::info('Fixed product stock issues');
    }
    
    private function fixSaleCalculations()
    {
        // Recalculate sale totals
        $sales = Sale::with('saleItems')->get();
        
        foreach ($sales as $sale) {
            $subtotal = $sale->saleItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });
            
            $tax = $subtotal * ($sale->tax_percentage / 100);
            $discount = $sale->discount_amount ?? 0;
            $finalTotal = $subtotal + $tax - $discount;
            
            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'final_total' => $finalTotal
            ]);
        }
        
        Log::info('Fixed sale calculations for ' . $sales->count() . ' sales');
    }
    
    private function cleanupOrphanedRecords()
    {
        // Clean up sale items without valid sales
        DB::table('sale_items')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('sales')
                      ->whereRaw('sales.id = sale_items.sale_id');
            })
            ->delete();
            
        Log::info('Cleaned up orphaned records');
    }
    
    public function optimizeDatabase()
    {
        try {
            // Add indexes for better performance
            DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_created_at ON sales(created_at)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_products_quantity ON products(quantity)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_users_tenant_id ON users(tenant_id)');
            
            return response()->json([
                'success' => true,
                'message' => 'Database optimized successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Database optimization failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize database: ' . $e->getMessage()
            ], 500);
        }
    }
}