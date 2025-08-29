<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function stats()
{
    try {
        // Basic Counts
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalSales = Sale::count();
        $totalCustomers = Customer::count();
        $totalSalesReturns = SaleReturn::count();
        $totalSalesReturnAmount = SaleReturn::sum('final_total');

        // ✅ Daily Sale & Sale Return (Today)
        $today = Carbon::today();
        $todaySaleAmount = Sale::whereDate('created_at', $today)->sum('final_total');
        $todaySaleReturnAmount = SaleReturn::whereDate('created_at', $today)->sum('final_total');

        // Sales Last 7 Days
        $dates = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $salesData = $dates->map(function ($date) {
            return Sale::whereDate('created_at', $date)->sum('final_total');
        });

        $saleReturnsData = $dates->map(function ($date) {
            return SaleReturn::whereDate('created_at', $date)->sum('final_total');
        });

        // Top 5 Customers
        $topCustomers = Customer::withSum('sales', 'final_total')
            ->orderByDesc('sales_sum_final_total')
            ->take(5)
            ->get()
            ->map(function ($c) {
                return [
                    'name' => $c->name,
                    'sales_sum_amount' => round($c->sales_sum_final_total, 2)
                ];
            });

        // Top 5 Sales
        $topSales = Sale::orderByDesc('final_total')->take(5)->get(['id', 'final_total as amount']);

        // Top Selling Products
        $topProducts = Product::withSum('saleItems', 'quantity')
            ->orderByDesc('sale_items_sum_quantity')
            ->take(5)
            ->get(['name']);

        // Stock Alert
        $stockAlertProducts = Product::whereColumn('quantity', '<=', 'stock_alert_level')
            ->get(['name', 'quantity as stock_quantity']);

        return response()->json([
            'totalProducts' => $totalProducts,
            'totalUsers' => $totalUsers,
            'totalSales' => $totalSales,
            'totalCustomers' => $totalCustomers,
            'totalSalesReturns' => $totalSalesReturns,
            'totalSalesReturnAmount' => round($totalSalesReturnAmount, 2),

            // ✅ Daily
            'todaySaleAmount' => round($todaySaleAmount, 2),
            'todaySaleReturnAmount' => round($todaySaleReturnAmount, 2),

            'salesChart' => [
                'dates' => $dates,
                'salesData' => $salesData,
                'saleReturnsData' => $saleReturnsData,
            ],
            'topCustomers' => $topCustomers,
            'topSales' => $topSales,
            'topProducts' => $topProducts,
            'stockAlertProducts' => $stockAlertProducts,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    
}
