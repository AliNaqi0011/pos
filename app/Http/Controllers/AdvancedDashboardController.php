<?php

namespace App\Http\Controllers;

use App\Services\BusinessIntelligenceService;
use App\Services\FinancialService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdvancedDashboardController extends Controller
{
    public function __construct(
        private BusinessIntelligenceService $biService,
        private FinancialService $financialService,
        private InventoryService $inventoryService
    ) {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $period = $request->get('period', '30days');
        $cacheKey = "advanced_dashboard_{$period}_" . auth()->id();
        
        $data = Cache::remember($cacheKey, 300, function () use ($period) {
            return [
                'metrics' => $this->biService->getDashboardMetrics($period),
                'financial_summary' => $this->getFinancialSummary(),
                'inventory_alerts' => $this->getInventoryAlerts(),
                'recent_activities' => $this->getRecentActivities(),
                'performance_indicators' => $this->getPerformanceIndicators(),
            ];
        });
        
        return view('admin.dashboard.advanced', $data);
    }

    public function getFinancialSummary(): array
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        
        return [
            'profit_loss' => $this->financialService->generateProfitLossStatement($startDate, $endDate),
            'cash_flow' => $this->financialService->generateCashFlowStatement($startDate, $endDate),
            'key_ratios' => $this->calculateKeyRatios(),
        ];
    }

    public function getInventoryAlerts(): array
    {
        return [
            'low_stock' => $this->inventoryService->getLowStockProducts(),
            'expiring_soon' => $this->inventoryService->getExpiryReport(30),
            'reorder_suggestions' => $this->inventoryService->getReorderSuggestions(),
            'dead_stock' => $this->inventoryService->getDeadStock(),
        ];
    }

    public function getRecentActivities(): array
    {
        return [
            'recent_sales' => \App\Models\Sale::with('customer')
                ->latest()
                ->limit(10)
                ->get(),
            'recent_customers' => \App\Models\Customer::latest()
                ->limit(5)
                ->get(),
            'stock_movements' => \App\Models\StockMovement::with(['product', 'warehouse'])
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }

    public function getPerformanceIndicators(): array
    {
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        
        return [
            'sales_growth' => $this->calculateSalesGrowth($currentMonth, $previousMonth),
            'customer_acquisition' => $this->calculateCustomerAcquisition(),
            'inventory_turnover' => $this->calculateInventoryTurnover(),
            'profit_margin' => $this->calculateProfitMargin(),
        ];
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sales,inventory,financial,customer',
            'format' => 'required|in:pdf,excel,csv',
            'period' => 'required|in:today,week,month,quarter,year',
        ]);
        
        $reportData = match($request->type) {
            'sales' => $this->generateSalesReport($request->period),
            'inventory' => $this->generateInventoryReport($request->period),
            'financial' => $this->generateFinancialReport($request->period),
            'customer' => $this->generateCustomerReport($request->period),
        };
        
        return $this->exportToFormat($reportData, $request->format, $request->type);
    }

    public function getRealtimeMetrics()
    {
        return response()->json([
            'current_sales_today' => \App\Models\Sale::whereDate('sale_date', today())->sum('final_total'),
            'transactions_today' => \App\Models\Sale::whereDate('sale_date', today())->count(),
            'active_users' => $this->getActiveUsersCount(),
            'low_stock_count' => \App\Models\Product::whereRaw('quantity <= stock_alert_level')->count(),
            'pending_orders' => \App\Models\Purchase::where('status', 'pending')->count(),
        ]);
    }

    private function calculateKeyRatios(): array
    {
        $currentAssets = 100000; // Would be calculated from balance sheet
        $currentLiabilities = 50000;
        $totalRevenue = \App\Models\Sale::whereMonth('sale_date', now()->month)->sum('final_total');
        $totalExpenses = 30000; // Would be calculated from expenses
        
        return [
            'current_ratio' => $currentLiabilities > 0 ? $currentAssets / $currentLiabilities : 0,
            'quick_ratio' => $currentLiabilities > 0 ? ($currentAssets * 0.8) / $currentLiabilities : 0,
            'gross_margin' => $totalRevenue > 0 ? (($totalRevenue - $totalExpenses) / $totalRevenue) * 100 : 0,
            'return_on_assets' => 15.5, // Would be calculated from financial data
        ];
    }

    private function calculateSalesGrowth(Carbon $current, Carbon $previous): float
    {
        $currentSales = \App\Models\Sale::whereMonth('sale_date', $current->month)->sum('final_total');
        $previousSales = \App\Models\Sale::whereMonth('sale_date', $previous->month)->sum('final_total');
        
        if ($previousSales == 0) return $currentSales > 0 ? 100 : 0;
        
        return (($currentSales - $previousSales) / $previousSales) * 100;
    }

    private function calculateCustomerAcquisition(): array
    {
        $thisMonth = \App\Models\Customer::whereMonth('created_at', now()->month)->count();
        $lastMonth = \App\Models\Customer::whereMonth('created_at', now()->subMonth()->month)->count();
        
        return [
            'new_customers_this_month' => $thisMonth,
            'growth_rate' => $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0,
        ];
    }

    private function calculateInventoryTurnover(): float
    {
        $cogs = \App\Models\Sale::whereYear('sale_date', now()->year)->sum('final_total') * 0.7; // Assuming 70% COGS
        $avgInventory = \App\Models\Product::avg('quantity') * \App\Models\Product::avg('cost_price');
        
        return $avgInventory > 0 ? $cogs / $avgInventory : 0;
    }

    private function calculateProfitMargin(): float
    {
        $totalRevenue = \App\Models\Sale::whereMonth('sale_date', now()->month)->sum('final_total');
        $totalCost = $totalRevenue * 0.7; // Assuming 70% cost
        
        return $totalRevenue > 0 ? (($totalRevenue - $totalCost) / $totalRevenue) * 100 : 0;
    }

    private function getActiveUsersCount(): int
    {
        return \App\Models\User::where('last_login', '>=', now()->subHours(24))->count();
    }

    private function generateSalesReport(string $period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'summary' => \App\Models\Sale::whereBetween('sale_date', $dateRange)
                ->selectRaw('COUNT(*) as total_sales, SUM(final_total) as total_revenue')
                ->first(),
            'by_product' => \App\Models\SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', $dateRange)
                ->selectRaw('products.name, SUM(sale_items.quantity) as qty_sold, SUM(sale_items.total) as revenue')
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('revenue')
                ->get(),
        ];
    }

    private function generateInventoryReport(string $period): array
    {
        return [
            'valuation' => $this->inventoryService->getInventoryValuation(),
            'movements' => \App\Models\StockMovement::with(['product', 'warehouse'])
                ->whereBetween('created_at', $this->getDateRange($period))
                ->get(),
            'turnover' => $this->inventoryService->getInventoryTurnover(
                Carbon::parse($this->getDateRange($period)[0]),
                Carbon::parse($this->getDateRange($period)[1])
            ),
        ];
    }

    private function generateFinancialReport(string $period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'profit_loss' => $this->financialService->generateProfitLossStatement(
                Carbon::parse($dateRange[0]),
                Carbon::parse($dateRange[1])
            ),
            'cash_flow' => $this->financialService->generateCashFlowStatement(
                Carbon::parse($dateRange[0]),
                Carbon::parse($dateRange[1])
            ),
        ];
    }

    private function generateCustomerReport(string $period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'new_customers' => \App\Models\Customer::whereBetween('created_at', $dateRange)->count(),
            'top_customers' => \App\Models\Customer::join('sales', 'customers.id', '=', 'sales.customer_id')
                ->whereBetween('sales.sale_date', $dateRange)
                ->selectRaw('customers.name, customers.email, SUM(sales.final_total) as total_spent')
                ->groupBy('customers.id', 'customers.name', 'customers.email')
                ->orderByDesc('total_spent')
                ->limit(20)
                ->get(),
        ];
    }

    private function getDateRange(string $period): array
    {
        return match($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    private function exportToFormat(array $data, string $format, string $type): mixed
    {
        // This would integrate with export libraries like PhpSpreadsheet or DomPDF
        return response()->json([
            'message' => 'Export functionality would be implemented here',
            'data' => $data,
            'format' => $format,
            'type' => $type,
        ]);
    }
}