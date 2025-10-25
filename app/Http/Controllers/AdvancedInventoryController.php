<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class AdvancedInventoryController extends Controller
{
    public function stockTransfers()
    {
        // Sample transfers - in real app this would be from a transfers table
        $transfers = collect([
            ['id' => 'ST001', 'from' => 'Main Warehouse', 'to' => 'Branch Store', 'product' => 'Sample Product', 'quantity' => 10, 'date' => '2024-01-15', 'status' => 'Completed'],
            ['id' => 'ST002', 'from' => 'Branch Store', 'to' => 'Main Warehouse', 'product' => 'Another Product', 'quantity' => 5, 'date' => '2024-01-14', 'status' => 'Pending']
        ]);
        
        return view('inventory.stock-transfers', compact('transfers'));
    }

    public function stockAdjustments()
    {
        // Sample adjustments - in real app this would be from adjustments table
        $adjustments = collect([
            ['id' => 'ADJ001', 'product' => 'Sample Product', 'warehouse' => 'Main Warehouse', 'previous' => 50, 'adjusted' => 48, 'difference' => -2, 'reason' => 'Damaged goods', 'date' => '2024-01-15'],
            ['id' => 'ADJ002', 'product' => 'Another Product', 'warehouse' => 'Branch Store', 'previous' => 25, 'adjusted' => 27, 'difference' => 2, 'reason' => 'Found in storage', 'date' => '2024-01-14']
        ]);
        
        return view('inventory.stock-adjustments', compact('adjustments'));
    }

    public function lowStockAlerts()
    {
        $lowStockProducts = Product::where('quantity', '<=', DB::raw('COALESCE(stock_alert_level, 10)'))
            ->with(['category', 'warehouse'])
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'current_stock' => $product->quantity,
                    'minimum_level' => $product->stock_alert_level ?? 10,
                    'warehouse' => $product->warehouse->name ?? 'Main Warehouse',
                    'last_updated' => $product->updated_at->format('Y-m-d')
                ];
            });
            
        return view('inventory.low-stock-alerts', compact('lowStockProducts'));
    }

    public function inventoryValuation()
    {
        $totalValue = Product::sum(DB::raw('quantity * COALESCE(cost_price, 0)'));
        
        $products = Product::select('name', 'quantity', 'cost_price', DB::raw('quantity * COALESCE(cost_price, 0) as total_value'))
            ->where('quantity', '>', 0)
            ->orderBy('total_value', 'desc')
            ->get();
            
        $fastMovingValue = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sale_items.created_at', '>=', now()->subDays(30))
            ->sum(DB::raw('sale_items.quantity * sale_items.product_price'));
            
        $data = [
            'totalValue' => $totalValue,
            'fastMoving' => $fastMovingValue,
            'slowMoving' => max(0, ($totalValue - $fastMovingValue) * 0.6),
            'deadStock' => max(0, ($totalValue - $fastMovingValue) * 0.4),
            'products' => $products
        ];
        
        return view('inventory.inventory-valuation', compact('data'));
    }
}