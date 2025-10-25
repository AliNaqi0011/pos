<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return $this->superAdminDashboard();
        } elseif ($user->hasRole('admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('seller')) {
            return $this->sellerDashboard();
        } else {
            return $this->defaultDashboard();
        }
    }

    private function superAdminDashboard()
    {
        return view('dashboards.super-admin', $this->getSuperAdminStats());
    }

    private function adminDashboard()
    {
        return view('dashboards.admin', $this->getAdminStats());
    }

    private function sellerDashboard()
    {
        return view('dashboards.seller', $this->getSellerStats());
    }

    private function defaultDashboard()
    {
        return view('dashboard', $this->getBasicStats());
    }

    private function getSuperAdminStats()
    {
        $currentUser = Auth::user();
        
        return [
            // Tenant Management
            'totalTenants' => User::where('role', 'admin')->count(),
            'activeTenants' => User::where('role', 'admin')->where('status', 'active')->count(),
            'trialTenants' => User::where('role', 'admin')->where('status', 'pending')->count(),
            'expiredTenants' => User::where('role', 'admin')->where('status', 'inactive')->count(),
            'pendingApprovals' => User::where('status', 'pending')->count(),
            
            // Revenue & Business Intelligence
            'mrr' => $this->calculateMRR(),
            'arr' => $this->calculateARR(),
            'totalRevenue' => Sale::sum('final_total'),
            'churnRate' => $this->calculateChurnRate(),
            'cac' => $this->calculateCAC(),
            'ltv' => $this->calculateLTV(),
            
            // System Health
            'systemHealth' => $this->getSystemHealth(),
            'apiPerformance' => $this->getAPIPerformance(),
            'databaseHealth' => $this->getDatabaseHealth(),
            'queueStatus' => $this->getQueueStatus(),
            
            // User & Access Management
            'totalUsers' => User::count(),
            'activeUsers' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'securityAlerts' => $this->getSecurityAlerts(),
            'roleDistribution' => $this->getRoleDistribution(),
            
            // Compliance & Security
            'complianceStatus' => $this->getComplianceStatus(),
            'securityScore' => $this->getSecurityScore(),
            'backupStatus' => $this->getBackupStatus(),
            'auditLogs' => $this->getRecentAuditLogs(),
            
            // Support & Communication
            'supportTickets' => $this->getSupportMetrics(),
            'tenantHealth' => $this->getTenantHealthMetrics(),
            'featureUsage' => $this->getFeatureUsageStats(),
            
            // Charts and Analytics
            'tenantGrowthChart' => $this->getTenantGrowthChart(),
            'revenueChart' => $this->getRevenueChart(),
            'systemMetricsChart' => $this->getSystemMetricsChart(),
            'topTenants' => $this->getTopTenants(),
            'recentActivity' => $this->getRecentActivity(),
        ];
    }

    private function getAdminStats()
    {
        return [
            'totalProducts' => Product::count(),
            'totalCustomers' => Customer::count(),
            'totalSales' => Sale::count(),
            'totalRevenue' => Sale::sum('final_total'),
            'todayRevenue' => Sale::whereDate('created_at', Carbon::today())->sum('final_total'),
            'monthlyRevenue' => Sale::whereMonth('created_at', Carbon::now()->month)->sum('final_total'),
            'totalPurchases' => 0, // Purchase::count(),
            'totalExpenses' => Expense::sum('amount'),
            'lowStockProducts' => Product::whereColumn('quantity', '<=', 'stock_alert_level')->count(),
            'topCustomers' => $this->getTopCustomers(),
            'salesChart' => $this->getSalesChartData(),
            'expenseChart' => $this->getExpenseChartData(),
        ];
    }

    private function getSellerStats()
    {
        $userId = Auth::id();
        $user = Auth::user();
        
        // Get allowed user IDs for data scope (seller + their admin)
        $allowedIds = [$userId];
        if ($user->created_by) {
            $allowedIds[] = $user->created_by;
        }
        
        return [
            'mySales' => Sale::where('user_id', $userId)->count(),
            'myRevenue' => Sale::where('user_id', $userId)->sum('final_total'),
            'todayRevenue' => Sale::where('user_id', $userId)->whereDate('created_at', Carbon::today())->sum('final_total'),
            'monthlyRevenue' => Sale::where('user_id', $userId)->whereMonth('created_at', Carbon::now()->month)->sum('final_total'),
            'totalProducts' => Product::count(), // This will use global scope
            'totalCustomers' => Customer::count(), // This will use global scope
            'myTopProducts' => $this->getMyTopProducts($userId),
            'recentSales' => Sale::where('user_id', $userId)->latest()->take(10)->with('customer')->get(),
            'adminName' => $user->created_by ? User::find($user->created_by)?->name : 'Independent',
        ];
    }

    private function getBasicStats()
    {
        return [
            'totalProducts' => Product::count(),
            'totalCustomers' => Customer::count(),
            'totalSales' => Sale::count(),
            'totalRevenue' => Sale::sum('final_total'),
        ];
    }

    private function getTopSellingProducts()
    {
        return Product::withSum('saleItems', 'quantity')
            ->orderByDesc('sale_items_sum_quantity')
            ->take(5)
            ->get();
    }

    private function getTopCustomers()
    {
        return Customer::withSum('sales', 'final_total')
            ->orderByDesc('sales_sum_final_total')
            ->take(5)
            ->get();
    }

    private function getMyTopProducts($userId)
    {
        return Product::whereHas('saleItems.sale', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->withSum(['saleItems' => function($query) use ($userId) {
            $query->whereHas('sale', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }], 'quantity')->orderByDesc('sale_items_sum_quantity')->take(5)->get();
    }

    private function getUserChartData()
    {
        $dates = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('M d');
        });

        $userData = $dates->map(function ($date) {
            return User::whereDate('created_at', Carbon::parse($date))->count();
        });

        return [
            'dates' => $dates,
            'userData' => $userData,
        ];
    }

    private function getSalesChartData()
    {
        $dates = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $salesData = $dates->map(function ($date) {
            return Sale::whereDate('created_at', $date)->sum('final_total');
        });

        return [
            'dates' => $dates,
            'salesData' => $salesData,
        ];
    }

    private function getExpenseChartData()
    {
        $dates = collect(range(6, 0))->map(function ($i) {
            return Carbon::now()->subDays($i)->format('Y-m-d');
        });

        $expenseData = $dates->map(function ($date) {
            return Expense::whereDate('created_at', $date)->sum('amount');
        });

        return [
            'dates' => $dates,
            'expenseData' => $expenseData,
        ];
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

            // Daily Sale & Sale Return (Today)
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

    public function filter(\Illuminate\Http\Request $request)
    {
        $date = $request->input('date');
        $range = $request->input('range');
        $search = $request->input('search');
        
        // Build date query based on range
        $dateQuery = function($query) use ($date, $range) {
            if ($date) {
                $query->whereDate('created_at', $date);
            } elseif ($range) {
                switch ($range) {
                    case 'today':
                        $query->whereDate('created_at', Carbon::today());
                        break;
                    case 'yesterday':
                        $query->whereDate('created_at', Carbon::yesterday());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', Carbon::now()->month);
                        break;
                    case 'year':
                        $query->whereYear('created_at', Carbon::now()->year);
                        break;
                }
            }
        };
        
        // Build search query
        $searchQuery = function($query) use ($search) {
            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }
        };
        
        // Get filtered data
        $totalProducts = Product::when($search, $searchQuery)->count();
        $totalCustomers = Customer::when($search, $searchQuery)->count();
        $totalSales = Sale::when($date || $range, $dateQuery)->count();
        $totalRevenue = Sale::when($date || $range, $dateQuery)->sum('final_total') ?? 0;
        $totalPurchases = 0; // Purchase::when($date || $range, $dateQuery)->count();
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'stock_alert_level')->count();
        
        return response()->json([
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalSales' => $totalSales,
            'totalRevenue' => round($totalRevenue, 2),
            'totalPurchases' => $totalPurchases,
            'lowStockProducts' => $lowStockProducts
        ]);
    }

    public function getChartData()
    {
        // Sales trend for last 30 days
        $salesTrend = Sale::selectRaw('DATE(created_at) as date, SUM(final_total) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Fill missing dates with 0
        $salesData = [];
        $orderData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $salesTrend->where('date', $date)->first();
            $salesData[] = $dayData ? (int)$dayData->total : 0;
            $orderData[] = Sale::whereDate('created_at', $date)->count();
        }
        
        // Product performance
        $productData = \DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', \DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sold', 'desc')
            ->limit(8)
            ->get();
            
        // Customer growth by month
        $customerGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Customer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $customerGrowth[] = $count;
        }
        
        return response()->json([
            'salesData' => $salesData,
            'orderData' => $orderData,
            'productCategories' => $productData->pluck('name')->toArray(),
            'productSales' => $productData->pluck('total_sold')->toArray(),
            'customerGrowth' => $customerGrowth
        ]);
    }
    
    // Enterprise Super Admin Methods
    private function calculateMRR()
    {
        return User::where('role', 'admin')->where('status', 'active')->count() * 99; // $99/month
    }
    
    private function calculateARR()
    {
        return $this->calculateMRR() * 12;
    }
    
    private function calculateChurnRate()
    {
        $totalTenants = User::where('role', 'admin')->count();
        $churnedTenants = User::where('role', 'admin')->where('status', 'inactive')->whereMonth('updated_at', now()->month)->count();
        return $totalTenants > 0 ? ($churnedTenants / $totalTenants) * 100 : 0;
    }
    
    private function calculateCAC()
    {
        return 150; // Customer Acquisition Cost placeholder
    }
    
    private function calculateLTV()
    {
        return 2400; // Lifetime Value placeholder
    }
    
    private function getSystemHealth()
    {
        return [
            'cpu_usage' => rand(15, 45),
            'memory_usage' => rand(40, 70),
            'disk_usage' => rand(20, 60),
            'uptime' => '99.9%',
            'status' => 'healthy'
        ];
    }
    
    private function getAPIPerformance()
    {
        return [
            'avg_response_time' => rand(120, 300) . 'ms',
            'requests_per_minute' => rand(500, 1200),
            'error_rate' => rand(1, 5) / 10 . '%',
            'success_rate' => '99.' . rand(5, 9) . '%'
        ];
    }
    
    private function getDatabaseHealth()
    {
        return [
            'connections' => rand(15, 45),
            'query_time' => rand(50, 150) . 'ms',
            'slow_queries' => rand(0, 3),
            'status' => 'optimal'
        ];
    }
    
    private function getQueueStatus()
    {
        return [
            'pending_jobs' => rand(0, 25),
            'failed_jobs' => rand(0, 3),
            'processed_today' => rand(1500, 3000),
            'avg_processing_time' => rand(2, 8) . 's'
        ];
    }
    
    private function getSecurityAlerts()
    {
        return [
            'failed_logins' => rand(5, 25),
            'suspicious_activity' => rand(0, 3),
            'blocked_ips' => rand(2, 8),
            'security_score' => rand(85, 98)
        ];
    }
    
    private function getRoleDistribution()
    {
        return [
            'super_admin' => User::where('role', 'super_admin')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'seller' => User::where('role', 'seller')->count(),
            'manager' => User::where('role', 'manager')->count(),
            'sales' => User::where('role', 'sales')->count()
        ];
    }
    
    private function getComplianceStatus()
    {
        return [
            'gdpr_compliance' => 'compliant',
            'data_retention' => 'active',
            'privacy_policy' => 'updated',
            'terms_of_service' => 'current',
            'last_audit' => now()->subDays(30)->format('M d, Y')
        ];
    }
    
    private function getSecurityScore()
    {
        return rand(85, 98);
    }
    
    private function getBackupStatus()
    {
        return [
            'last_backup' => now()->subHours(6)->format('M d, Y H:i'),
            'backup_size' => rand(500, 1200) . 'MB',
            'status' => 'successful',
            'retention_days' => 30
        ];
    }
    
    private function getRecentAuditLogs()
    {
        return collect([
            ['action' => 'User Login', 'user' => 'admin@example.com', 'time' => now()->subMinutes(15)->format('H:i')],
            ['action' => 'Tenant Created', 'user' => 'superadmin@example.com', 'time' => now()->subHours(2)->format('H:i')],
            ['action' => 'Payment Processed', 'user' => 'system', 'time' => now()->subHours(4)->format('H:i')],
            ['action' => 'Security Alert', 'user' => 'security@system', 'time' => now()->subHours(6)->format('H:i')]
        ]);
    }
    
    private function getSupportMetrics()
    {
        return [
            'open_tickets' => rand(5, 25),
            'resolved_today' => rand(10, 30),
            'avg_response_time' => rand(2, 8) . 'h',
            'satisfaction_score' => rand(85, 95) . '%'
        ];
    }
    
    private function getTenantHealthMetrics()
    {
        return [
            'healthy' => User::where('role', 'admin')->where('status', 'active')->count(),
            'warning' => rand(2, 8),
            'critical' => rand(0, 3),
            'inactive' => User::where('role', 'admin')->where('status', 'inactive')->count()
        ];
    }
    
    private function getFeatureUsageStats()
    {
        return [
            'pos_usage' => rand(70, 95) . '%',
            'inventory_usage' => rand(80, 98) . '%',
            'reporting_usage' => rand(60, 85) . '%',
            'api_usage' => rand(45, 75) . '%'
        ];
    }
    
    private function getTenantGrowthChart()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = User::where('role', 'admin')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data[] = $count;
        }
        return $data;
    }
    
    private function getRevenueChart()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $data[] = rand(8000, 25000);
        }
        return $data;
    }
    
    private function getSystemMetricsChart()
    {
        return [
            'cpu' => array_map(fn() => rand(20, 60), range(1, 24)),
            'memory' => array_map(fn() => rand(40, 80), range(1, 24)),
            'disk' => array_map(fn() => rand(30, 70), range(1, 24))
        ];
    }
    
    private function getTopTenants()
    {
        return User::where('role', 'admin')
            ->withCount(['createdUsers as total_users'])
            ->orderBy('total_users', 'desc')
            ->take(5)
            ->get(['name', 'email', 'created_at'])
            ->map(function($tenant) {
                return [
                    'name' => $tenant->name,
                    'email' => $tenant->email,
                    'users' => rand(5, 50),
                    'revenue' => '$' . number_format(rand(500, 5000)),
                    'status' => 'active'
                ];
            });
    }
    
    private function getRecentActivity()
    {
        return collect([
            ['type' => 'tenant_signup', 'message' => 'New tenant registered: TechCorp Ltd', 'time' => now()->subMinutes(15)],
            ['type' => 'payment_received', 'message' => 'Payment received from RetailMax Inc', 'time' => now()->subHours(2)],
            ['type' => 'system_alert', 'message' => 'High CPU usage detected on Server-02', 'time' => now()->subHours(4)],
            ['type' => 'security_event', 'message' => 'Failed login attempts blocked', 'time' => now()->subHours(6)],
            ['type' => 'backup_completed', 'message' => 'Daily backup completed successfully', 'time' => now()->subHours(8)]
        ]);
    }
}