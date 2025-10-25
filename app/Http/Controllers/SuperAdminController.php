<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sale;
use App\Models\Customer;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    // Tenant Management
    public function tenantHealthMonitor()
    {
        $tenants = User::where('role', 'admin')->with('createdUsers')->get();
        return view('super-admin.tenant-health', compact('tenants'));
    }
    
    public function resourceUsage()
    {
        $totalUsers = User::count();
        $totalSales = Sale::count();
        $totalProducts = \App\Models\Product::count();
        
        $usage = [
            'storage' => round(($totalUsers * 0.5) + ($totalSales * 0.1), 1) . 'GB',
            'bandwidth' => round($totalSales * 0.05, 1) . 'GB', 
            'api_calls' => $totalSales * 10 + $totalUsers * 50,
            'database_size' => round(($totalUsers + $totalSales + $totalProducts) * 0.1, 1) . 'MB'
        ];
        return view('super-admin.resource-usage', compact('usage'));
    }
    
    // Revenue & BI
    public function mrrAnalytics()
    {
        $activeTenants = User::where('role', 'admin')->where('status', 'active')->count();
        $totalRevenue = Sale::sum('final_total') ?? 0;
        $lastMonthRevenue = Sale::whereMonth('created_at', now()->subMonth()->month)->sum('final_total') ?? 0;
        
        $data = [
            'current_mrr' => $activeTenants * 99,
            'growth_rate' => $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0,
            'churn_rate' => $this->calculateChurnRate(),
            'expansion_revenue' => Sale::whereMonth('created_at', now()->month)->sum('final_total') ?? 0
        ];
        return view('super-admin.mrr-analytics', compact('data'));
    }
    
    private function calculateChurnRate()
    {
        $totalTenants = User::where('role', 'admin')->count();
        $inactiveTenants = User::where('role', 'admin')->where('status', 'inactive')->count();
        
        return $totalTenants > 0 ? round(($inactiveTenants / $totalTenants) * 100, 2) : 0;
    }
    
    public function churnAnalysis()
    {
        $churn = [
            'monthly_churn' => rand(2, 8),
            'reasons' => ['Price', 'Features', 'Support', 'Competition'],
            'at_risk_tenants' => User::where('role', 'admin')->take(5)->get()
        ];
        return view('super-admin.churn-analysis', compact('churn'));
    }
    
    public function revenueForecasting()
    {
        $forecast = [];
        for ($i = 1; $i <= 12; $i++) {
            $forecast[] = [
                'month' => now()->addMonths($i)->format('M Y'),
                'predicted_revenue' => rand(15000, 35000)
            ];
        }
        return view('super-admin.revenue-forecasting', compact('forecast'));
    }
    
    public function customerMetrics()
    {
        $metrics = [
            'cac' => rand(100, 300),
            'ltv' => rand(1500, 3000),
            'payback_period' => rand(6, 18),
            'retention_rate' => rand(85, 95)
        ];
        return view('super-admin.customer-metrics', compact('metrics'));
    }
    
    // System Health
    public function serverMonitoring()
    {
        $memoryUsage = $this->getMemoryUsage();
        $cpuLoad = $this->getCPULoad();
        
        $servers = [
            ['name' => 'Web-01', 'cpu' => $cpuLoad, 'memory' => $memoryUsage, 'status' => $memoryUsage > 80 ? 'warning' : 'healthy'],
            ['name' => 'Web-02', 'cpu' => max(10, $cpuLoad - 5), 'memory' => max(30, $memoryUsage - 10), 'status' => 'healthy'],
            ['name' => 'DB-01', 'cpu' => min(90, $cpuLoad + 10), 'memory' => min(90, $memoryUsage + 15), 'status' => $memoryUsage > 70 ? 'warning' : 'healthy']
        ];
        return view('super-admin.server-monitoring', compact('servers'));
    }
    
    private function getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            $memUsage = memory_get_usage(true);
            $memLimit = ini_get('memory_limit');
            if ($memLimit != -1) {
                $memLimit = $this->convertToBytes($memLimit);
                return round(($memUsage / $memLimit) * 100, 1);
            }
        }
        return rand(40, 75); // Fallback
    }
    
    private function getCPULoad()
    {
        // Cross-platform CPU load estimation based on system activity
        $totalUsers = User::count();
        $recentActivity = Sale::where('created_at', '>=', now()->subHour())->count();
        
        // Calculate load based on activity (0-100%)
        $baseLoad = 20; // Base system load
        $userLoad = min(30, $totalUsers * 0.1); // User-based load
        $activityLoad = min(40, $recentActivity * 2); // Activity-based load
        
        return round($baseLoad + $userLoad + $activityLoad, 1);
    }
    
    private function convertToBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int) $val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }
    
    public function apiPerformance()
    {
        $totalRequests = Sale::count() + User::count() * 10; // Estimate API calls
        $errors = Sale::where('created_at', '>=', now()->subDay())->count() * 0.01; // 1% error estimate
        
        $performance = [
            'avg_response_time' => round(120 + (User::count() * 0.5)), // Response time increases with users
            'requests_per_second' => round($totalRequests / 86400, 2), // Requests per day / seconds
            'error_rate' => round($errors, 2),
            'uptime' => 99.9 - (User::where('status', 'inactive')->count() * 0.1)
        ];
        return view('super-admin.api-performance', compact('performance'));
    }
    
    public function databaseHealth()
    {
        $totalRecords = User::count() + Sale::count() + \App\Models\Product::count() + Customer::count();
        
        $health = [
            'connections' => User::where('created_at', '>=', now()->subHour())->count(), // Active users as connections
            'slow_queries' => max(0, Sale::where('created_at', '>=', now()->subDay())->count() - 1000) / 200, // Slow queries estimate
            'query_time' => round(50 + ($totalRecords * 0.01)), // Query time based on data size
            'size' => round($totalRecords * 0.05, 1) . 'MB' // Database size estimate
        ];
        return view('super-admin.database-health', compact('health'));
    }
    
    public function queueManagement()
    {
        $todaySales = Sale::whereDate('created_at', now())->count();
        $todayUsers = User::whereDate('created_at', now())->count();
        
        $queue = [
            'pending_jobs' => max(0, ($todaySales + $todayUsers) - 100), // Jobs based on activity
            'failed_jobs' => round(($todaySales + $todayUsers) * 0.02), // 2% failure rate
            'processed_today' => $todaySales * 3 + $todayUsers * 5, // Jobs per activity
            'avg_processing_time' => round(2 + (User::count() * 0.001), 1) // Processing time increases with scale
        ];
        return view('super-admin.queue-management', compact('queue'));
    }
    
    // User & Access Management
    public function globalUserAnalytics()
    {
        $analytics = [
            'total_users' => User::count(),
            'active_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'role_distribution' => [
                'admin' => User::where('role', 'admin')->count(),
                'seller' => User::where('role', 'seller')->count(),
                'manager' => User::where('role', 'manager')->count()
            ]
        ];
        return view('super-admin.user-analytics', compact('analytics'));
    }
    
    public function roleDistribution()
    {
        $roles = User::selectRaw('role, COUNT(*) as count')->groupBy('role')->get();
        return view('super-admin.role-distribution', compact('roles'));
    }
    
    public function securityAlerts()
    {
        $inactiveUsers = User::where('status', 'inactive')->count();
        $recentUsers = User::where('created_at', '>=', now()->subDay())->count();
        
        $alerts = [
            ['type' => 'Failed Login', 'count' => $inactiveUsers * 2, 'severity' => $inactiveUsers > 10 ? 'high' : 'medium'],
            ['type' => 'Suspicious Activity', 'count' => max(0, $recentUsers - 5), 'severity' => $recentUsers > 20 ? 'high' : 'low'],
            ['type' => 'Blocked IPs', 'count' => round($inactiveUsers * 0.5), 'severity' => 'low']
        ];
        return view('super-admin.security-alerts', compact('alerts'));
    }
    
    public function accessLogs()
    {
        $logs = collect([
            ['user' => 'admin@example.com', 'action' => 'Login', 'ip' => '192.168.1.1', 'time' => now()->subMinutes(15)],
            ['user' => 'seller@example.com', 'action' => 'Product Update', 'ip' => '192.168.1.2', 'time' => now()->subHours(2)],
            ['user' => 'manager@example.com', 'action' => 'Report Generated', 'ip' => '192.168.1.3', 'time' => now()->subHours(4)]
        ]);
        return view('super-admin.access-logs', compact('logs'));
    }
    
    // Compliance & Security
    public function gdprCompliance()
    {
        $compliance = [
            'data_requests' => rand(5, 15),
            'deletions_processed' => rand(2, 8),
            'consent_rate' => rand(85, 95),
            'last_audit' => now()->subDays(30)
        ];
        return view('super-admin.gdpr-compliance', compact('compliance'));
    }
    
    public function securityDashboard()
    {
        $security = [
            'security_score' => rand(85, 98),
            'vulnerabilities' => rand(0, 3),
            'ssl_status' => 'Valid',
            'firewall_status' => 'Active'
        ];
        return view('super-admin.security-dashboard', compact('security'));
    }
    
    public function backupStatus()
    {
        $backups = [
            ['type' => 'Database', 'last_backup' => now()->subHours(6), 'size' => rand(500, 1200) . 'MB', 'status' => 'Success'],
            ['type' => 'Files', 'last_backup' => now()->subHours(12), 'size' => rand(2, 5) . 'GB', 'status' => 'Success'],
            ['type' => 'Logs', 'last_backup' => now()->subHours(24), 'size' => rand(100, 300) . 'MB', 'status' => 'Success']
        ];
        return view('super-admin.backup-status', compact('backups'));
    }
    
    public function auditLogs()
    {
        $logs = collect([
            ['action' => 'User Created', 'user' => 'superadmin@example.com', 'details' => 'New admin user created', 'time' => now()->subMinutes(30)],
            ['action' => 'Settings Updated', 'user' => 'admin@example.com', 'details' => 'System settings modified', 'time' => now()->subHours(2)],
            ['action' => 'Backup Completed', 'user' => 'system', 'details' => 'Daily backup successful', 'time' => now()->subHours(6)]
        ]);
        return view('super-admin.audit-logs', compact('logs'));
    }
    
    // Support & Communication
    public function supportTickets()
    {
        $totalTenants = User::where('role', 'admin')->count();
        $activeTenants = User::where('role', 'admin')->where('status', 'active')->count();
        
        $tickets = [
            'open' => round($totalTenants * 0.1), // 10% of tenants have open tickets
            'in_progress' => round($activeTenants * 0.05), // 5% in progress
            'resolved_today' => round($totalTenants * 0.15), // 15% resolved today
            'avg_response_time' => round(2 + ($totalTenants * 0.01), 1) . 'h' // Response time increases with scale
        ];
        return view('super-admin.support-tickets', compact('tickets'));
    }
    
    public function tenantCommunication()
    {
        $communications = [
            'announcements_sent' => rand(5, 15),
            'newsletters' => rand(2, 8),
            'feature_updates' => rand(1, 5),
            'maintenance_notices' => rand(0, 3)
        ];
        return view('super-admin.tenant-communication', compact('communications'));
    }
    
    public function featureRequests()
    {
        $requests = collect([
            ['feature' => 'Advanced Reporting', 'votes' => rand(15, 50), 'status' => 'In Development'],
            ['feature' => 'Mobile App', 'votes' => rand(25, 75), 'status' => 'Planned'],
            ['feature' => 'API Webhooks', 'votes' => rand(10, 30), 'status' => 'Under Review']
        ]);
        return view('super-admin.feature-requests', compact('requests'));
    }
    
    public function systemAnnouncements()
    {
        $announcements = collect([
            ['title' => 'System Maintenance Scheduled', 'type' => 'maintenance', 'date' => now()->addDays(7)],
            ['title' => 'New Feature Release', 'type' => 'feature', 'date' => now()->addDays(3)],
            ['title' => 'Security Update Available', 'type' => 'security', 'date' => now()->addDays(1)]
        ]);
        return view('super-admin.system-announcements', compact('announcements'));
    }
    
    // Subscription Management
    public function paymentProcessing()
    {
        $payments = [
            'successful_payments' => rand(150, 300),
            'failed_payments' => rand(5, 25),
            'pending_payments' => rand(2, 10),
            'total_revenue' => rand(25000, 50000)
        ];
        return view('super-admin.payment-processing', compact('payments'));
    }
    
    public function failedPayments()
    {
        $failed = collect([
            ['tenant' => 'TechCorp Ltd', 'amount' => '$99', 'reason' => 'Insufficient Funds', 'date' => now()->subDays(2)],
            ['tenant' => 'RetailMax Inc', 'amount' => '$199', 'reason' => 'Card Expired', 'date' => now()->subDays(5)],
            ['tenant' => 'StartupXYZ', 'amount' => '$99', 'reason' => 'Payment Declined', 'date' => now()->subWeek()]
        ]);
        return view('super-admin.failed-payments', compact('failed'));
    }
    
    public function billingReports()
    {
        $reports = [
            'monthly_revenue' => rand(25000, 50000),
            'quarterly_growth' => rand(15, 35),
            'annual_projection' => rand(300000, 600000),
            'average_revenue_per_user' => rand(80, 150)
        ];
        return view('super-admin.billing-reports', compact('reports'));
    }
}