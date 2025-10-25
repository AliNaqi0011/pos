<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessIntelligenceController extends Controller
{
    public function analyticsDashboard()
    {
        $data = [
            'totalRevenue' => Sale::sum('total') ?? 0,
            'profitMargin' => 25.5, // Placeholder
            'avgOrderValue' => Sale::avg('total') ?? 0,
            'totalSales' => Sale::count(),
            'topProducts' => $this->getTopProductsWithRevenue(),
            'sales_trend' => $this->getSalesTrend(),
            'customer_segments' => $this->getCustomerSegments(),
            'revenue_metrics' => $this->getRevenueMetrics()
        ];
        
        return view('bi.analytics-dashboard', compact('data'));
    }

    public function salesForecasting()
    {
        $forecast = $this->generateSalesForecast();
        return view('bi.sales-forecasting', compact('forecast'));
    }

    public function performanceMetrics()
    {
        $metrics = [
            'conversion_rate' => $this->calculateConversionRate(),
            'average_order_value' => $this->calculateAverageOrderValue(),
            'customer_lifetime_value' => $this->calculateCustomerLifetimeValue()
        ];
        
        return view('bi.performance-metrics', compact('metrics'));
    }

    public function customReports()
    {
        return view('bi.custom-reports');
    }

    public function generateReport(Request $request)
    {
        $type = $request->input('type');
        $dateRange = $request->input('date_range');
        
        switch ($type) {
            case 'sales':
                $data = $this->generateSalesReport($dateRange);
                break;
            case 'inventory':
                $data = $this->generateInventoryReport();
                break;
            case 'customer':
                $data = $this->generateCustomerReport($dateRange);
                break;
            default:
                $data = [];
        }
        
        return response()->json(['data' => $data]);
    }

    private function getSalesTrend()
    {
        return Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getTopProducts()
    {
        return Product::withSum('saleItems', 'quantity')
            ->orderBy('sale_items_sum_quantity', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTopProductsWithRevenue()
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity * sale_items.product_price) as total_revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object) [
                    'product' => (object) ['name' => $item->name],
                    'total_revenue' => $item->total_revenue
                ];
            });
    }

    private function getCustomerSegments()
    {
        return [
            'new' => Customer::where('created_at', '>=', now()->subDays(30))->count(),
            'returning' => Customer::whereHas('sales', function($q) {
                $q->havingRaw('COUNT(*) > 1');
            })->count(),
            'vip' => Customer::withSum('sales', 'total')
                ->having('sales_sum_total', '>', 10000)
                ->count()
        ];
    }

    private function getRevenueMetrics()
    {
        $thisMonth = Sale::whereMonth('created_at', now()->month)->sum('total');
        $lastMonth = Sale::whereMonth('created_at', now()->subMonth()->month)->sum('total');
        
        return [
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'growth_rate' => $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0
        ];
    }

    private function generateSalesForecast()
    {
        // Simple linear regression forecast
        $salesData = Sale::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Generate forecast for next 30 days
        $forecast = [];
        $avgDaily = $salesData->avg('total');
        
        for ($i = 1; $i <= 30; $i++) {
            $forecast[] = [
                'date' => now()->addDays($i)->format('Y-m-d'),
                'predicted_sales' => $avgDaily * (1 + rand(-10, 10) / 100) // Add some variance
            ];
        }
        
        return $forecast;
    }

    private function calculateConversionRate()
    {
        // Placeholder calculation
        return 2.5; // 2.5%
    }

    private function calculateAverageOrderValue()
    {
        return Sale::avg('total') ?? 0;
    }

    private function calculateCustomerLifetimeValue()
    {
        return DB::table('customers')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->avg('sales.total') ?? 0;
    }

    private function generateSalesReport($dateRange)
    {
        $query = Sale::with(['customer', 'items.product']);
        
        if ($dateRange) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0]),
                    Carbon::parse($dates[1])
                ]);
            }
        }
        
        return $query->get();
    }

    private function generateInventoryReport()
    {
        return Product::with(['category', 'brand'])
            ->select('id', 'name', 'quantity', 'cost_price', 'sale_price')
            ->get();
    }

    private function generateCustomerReport($dateRange)
    {
        $query = Customer::withCount('sales')->withSum('sales', 'total');
        
        if ($dateRange) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) === 2) {
                $query->whereBetween('created_at', [
                    Carbon::parse($dates[0]),
                    Carbon::parse($dates[1])
                ]);
            }
        }
        
        return $query->get();
    }
}