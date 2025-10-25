<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Product;

class TestController extends Controller
{
    public function testSalesReturns()
    {
        $salesReturns = SaleReturn::with(['sale', 'items.product'])->get();
        return response()->json(['sales_returns' => $salesReturns]);
    }

    public function testPurchasesReturns()
    {
        $purchaseReturns = PurchaseReturn::with(['purchase', 'items.product'])->get();
        return response()->json(['purchase_returns' => $purchaseReturns]);
    }

    public function testProductStock()
    {
        $products = Product::select('id', 'name', 'quantity')->get();
        return response()->json(['products' => $products]);
    }
}