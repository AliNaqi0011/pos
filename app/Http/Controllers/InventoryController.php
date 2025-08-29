<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function fixInventory()
    {
        DB::beginTransaction();
        
        try {
            $products = Product::all();
            $fixed = 0;
            
            foreach ($products as $product) {
                // Calculate actual stock from sales and purchases
                $totalPurchased = Purchase::join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
                    ->where('purchase_items.product_id', $product->id)
                    ->sum('purchase_items.quantity');
                
                $totalSold = Sale::join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                    ->where('sale_items.product_id', $product->id)
                    ->where('sales.status', 'final')
                    ->sum('sale_items.quantity');
                
                $actualStock = $totalPurchased - $totalSold;
                
                if ($product->quantity != $actualStock) {
                    $product->update(['quantity' => max(0, $actualStock)]);
                    $fixed++;
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Fixed inventory for {$fixed} products",
                'fixed_count' => $fixed
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error fixing inventory: ' . $e->getMessage()
            ]);
        }
    }
    
    public function validateStock(Request $request)
    {
        $productId = $request->get('product_id');
        $quantity = $request->get('quantity');
        
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json(['valid' => false, 'message' => 'Product not found']);
        }
        
        if ($product->quantity < $quantity) {
            return response()->json([
                'valid' => false, 
                'message' => "Insufficient stock. Available: {$product->quantity}"
            ]);
        }
        
        return response()->json(['valid' => true]);
    }
}