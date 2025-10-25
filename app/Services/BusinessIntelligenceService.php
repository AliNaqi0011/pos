<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BusinessIntelligenceService
{
    public function getDashboardMetrics(string $period = '30days'): array
    {
        $cacheKey = "dashboard_metrics_{$period}_" . auth()->user()->tenant_id;
        
        return Cache::remember($cacheKey, 300, function () use ($period) {
            $dateRange = $this->getDateRange($period);
            
            return [
                'sales_overview' => $this->getSalesOverview($dateRange),
                'top_products' => $this->getTopProducts($dateRange),
                'customer_analytics' => $this->getCustomerAnalytics($dateRange),
                'inventory_alerts' => $this->getInventoryAlerts(),
                'profit_analysis' => $this->getProfitAnalysis($dateRange),
                'sales_forecast' => $this->getSalesForecast(),
            ];
        });
    }

    private function getSalesOverview(array $dateRange): array
    {
        $sales = Sale::whereBetween('sale_date', $dateRange)
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(final_total) as total_revenue,
                AVG(final_total) as avg_transaction_value,
                SUM(total_items) as total_items_sold
            ')
            ->first();

        $previousPeriod = $this->getPreviousPeriodSales($dateRange);
        
        return [
            'current' => $sales->toArray(),
            'growth' => $this->calculateGrowth($sales->total_revenue, $previousPeriod->total_revenue),
            'daily_sales' => $this->getDailySales($dateRange),
        ];
    }

    private function getTopProducts(array $dateRange, int $limit = 10): array
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', $dateRange)
            ->selectRaw('
                products.name,
                SUM(sale_items.quantity) as total_sold,
                SUM(sale_items.total) as total_revenue,
                AVG(sale_items.product_price) as avg_price
            ')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getCustomerAnalytics(array $dateRange): array
    {
        $newCustomers = Customer::whereBetween('created_at', $dateRange)->count();
        $returningCustomers = Sale::whereBetween('sale_date', $dateRange)
            ->whereIn('customer_id', function($query) use ($dateRange) {
                $query->select('customer_id')
                    ->from('sales')
                    ->where('sale_date', '<', $dateRange[0])
                    ->groupBy('customer_id');
            })
            ->distinct('customer_id')
            ->count();

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'customer_lifetime_value' => $this->getCustomerLifetimeValue(),
            'top_customers' => $this->getTopCustomers($dateRange),
        ];
    }

    private function getInventoryAlerts(): array
    {
        return [
            'low_stock' => Product::whereRaw('quantity <= stock_alert_level')->count(),
            'out_of_stock' => Product::where('quantity', 0)->count(),
            'expiring_soon' => $this->getExpiringSoonCount(),
            'overstock' => $this->getOverstockCount(),
        ];
    }

    private function getProfitAnalysis(array $dateRange): array
    {
        $profitData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', $dateRange)
            ->selectRaw('
                SUM(sale_items.total) as total_revenue,
                SUM(sale_items.quantity * products.cost_price) as total_cost,
                SUM(sale_items.total - (sale_items.quantity * products.cost_price)) as total_profit
            ')
            ->first();

        $profitMargin = $profitData->total_revenue > 0 
            ? ($profitData->total_profit / $profitData->total_revenue) * 100 
            : 0;

        return [
            'total_revenue' => $profitData->total_revenue,
            'total_cost' => $profitData->total_cost,
            'total_profit' => $profitData->total_profit,
            'profit_margin' => round($profitMargin, 2),
            'profit_by_category' => $this->getProfitByCategory($dateRange),
        ];
    }

    private function getSalesForecast(): array
    {
        // Simple linear regression for sales forecasting
        $historicalData = Sale::selectRaw('DATE(sale_date) as date, SUM(final_total) as daily_total')
            ->where('sale_date', '>=', now()->subDays(90))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalData->count() < 7) {
            return ['forecast' => [], 'trend' => 'insufficient_data'];
        }

        $forecast = $this->calculateLinearRegression($historicalData);
        
        return [
            'next_7_days' => $forecast,
            'trend' => $this->determineTrend($historicalData),
            'confidence' => $this->calculateConfidence($historicalData),
        ];
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            '7days' => [now()->subDays(7), now()],
            '30days' => [now()->subDays(30), now()],
            '90days' => [now()->subDays(90), now()],
            'year' => [now()->startOfYear(), now()],
            default => [now()->subDays(30), now()],
        };
    }

    private function calculateGrowth(float $current, float $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function getDailySales(array $dateRange): array
    {
        return Sale::selectRaw('DATE(sale_date) as date, SUM(final_total) as total')
            ->whereBetween('sale_date', $dateRange)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();
    }

    private function getPreviousPeriodSales(array $dateRange): object
    {
        $daysDiff = Carbon::parse($dateRange[1])->diffInDays(Carbon::parse($dateRange[0]));
        $previousStart = Carbon::parse($dateRange[0])->subDays($daysDiff);
        $previousEnd = Carbon::parse($dateRange[0]);

        return Sale::whereBetween('sale_date', [$previousStart, $previousEnd])
            ->selectRaw('SUM(final_total) as total_revenue')
            ->first();
    }

    private function getCustomerLifetimeValue(): float
    {
        return DB::table('customers')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->selectRaw('AVG(customer_total) as avg_clv')
            ->fromSub(function($query) {
                $query->select('customers.id', DB::raw('SUM(sales.final_total) as customer_total'))
                    ->from('customers')
                    ->join('sales', 'customers.id', '=', 'sales.customer_id')
                    ->groupBy('customers.id');
            }, 'customer_totals')
            ->value('avg_clv') ?? 0;
    }

    private function getTopCustomers(array $dateRange, int $limit = 5): array
    {
        return DB::table('customers')
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->whereBetween('sales.sale_date', $dateRange)
            ->selectRaw('
                customers.name,
                customers.email,
                COUNT(sales.id) as total_orders,
                SUM(sales.final_total) as total_spent
            ')
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getExpiringSoonCount(): int
    {
        return DB::table('product_batches')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->sum('quantity');
    }

    private function getOverstockCount(): int
    {
        return Product::whereRaw('quantity > (stock_alert_level * 5)')->count();
    }

    private function getProfitByCategory(array $dateRange): array
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('sales.sale_date', $dateRange)
            ->selectRaw('
                categories.name,
                SUM(sale_items.total - (sale_items.quantity * products.cost_price)) as profit
            ')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('profit')
            ->get()
            ->toArray();
    }

    private function calculateLinearRegression(object $data): array
    {
        $n = $data->count();
        $sumX = $sumY = $sumXY = $sumX2 = 0;

        foreach ($data as $index => $point) {
            $x = $index + 1;
            $y = $point->daily_total;
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $forecast[] = [
                'date' => now()->addDays($i)->format('Y-m-d'),
                'predicted_sales' => round($slope * ($n + $i) + $intercept, 2)
            ];
        }

        return $forecast;
    }

    private function determineTrend(object $data): string
    {
        if ($data->count() < 2) return 'stable';
        
        $first = $data->first()->daily_total;
        $last = $data->last()->daily_total;
        
        $change = (($last - $first) / $first) * 100;
        
        if ($change > 5) return 'increasing';
        if ($change < -5) return 'decreasing';
        return 'stable';
    }

    private function calculateConfidence(object $data): float
    {
        // Simple confidence calculation based on data consistency
        $values = $data->pluck('daily_total')->toArray();
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($x) => pow($x - $mean, 2), $values)) / count($values);
        $stdDev = sqrt($variance);
        
        $coefficient = $mean > 0 ? $stdDev / $mean : 1;
        return max(0, min(100, 100 - ($coefficient * 100)));
    }
}