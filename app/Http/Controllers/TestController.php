<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testSalesReturns()
    {
        // Test sales with returns functionality
        $sales = Sale::with(['saleItems.product', 'customer', 'returns'])->get();
        
        $data = [];
        foreach ($sales as $sale) {
            $data[] = [
                'sale_id' => $sale->id,
                'customer' => $sale->customer->name ?? 'N/A',
                'total_items' => $sale->saleItems->count(),
                'total_amount' => $sale->final_total,
                'returns_count' => $sale->returns->count(),
                'status' => $sale->status
            ];
        }
        
        return response()->json([
            'message' => 'Sales and Returns Test',
            'sales_data' => $data
        ]);
    }
    
    public function testPurchasesReturns()
    {
        // Test purchases with returns functionality
        $purchases = Purchase::with(['items.product', 'warehouse', 'returns'])->get();
        
        $data = [];
        foreach ($purchases as $purchase) {
            $data[] = [
                'purchase_id' => $purchase->id,
                'warehouse' => $purchase->warehouse->name ?? 'N/A',
                'total_items' => $purchase->items->count(),
                'grand_total' => $purchase->grand_total,
                'returns_count' => $purchase->returns->count(),
                'status' => $purchase->status
            ];
        }
        
        return response()->json([
            'message' => 'Purchases and Returns Test',
            'purchases_data' => $data
        ]);
    }
    
    public function testProductStock()
    {
        // Test product stock levels
        $products = Product::all();
        
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'current_stock' => $product->quantity,
                'sale_price' => $product->sale_price,
                'cost_price' => $product->cost_price
            ];
        }
        
        return response()->json([
            'message' => 'Product Stock Test',
            'products_data' => $data
        ]);
    }
}