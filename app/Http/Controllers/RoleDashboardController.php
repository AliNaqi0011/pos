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
        return [
            'totalUsers' => User::count(),
            'totalAdmins' => User::role('admin')->count(),
            'pendingApprovals' => User::where('status', 'pending')->count(),
            'activeTenants' => User::role('admin')->where('status', 'approved')->count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'pendingUsers' => User::where('status', 'pending')
                ->whereHas('roles', function($q) {
                    $q->where('name', 'admin');
                })->take(5)->get(),
            'userChart' => $this->getUserChartData(),
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
        
        return [
            'mySales' => Sale::where('created_by', $userId)->count(),
            'myRevenue' => Sale::where('created_by', $userId)->sum('final_total'),
            'todayRevenue' => Sale::where('created_by', $userId)->whereDate('created_at', Carbon::today())->sum('final_total'),
            'monthlyRevenue' => Sale::where('created_by', $userId)->whereMonth('created_at', Carbon::now()->month)->sum('final_total'),
            'totalProducts' => Product::count(),
            'totalCustomers' => Customer::count(),
            'myTopProducts' => $this->getMyTopProducts($userId),
            'recentSales' => Sale::where('created_by', $userId)->latest()->take(10)->with('customer')->get(),
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
            $query->where('created_by', $userId);
        })->withSum(['saleItems' => function($query) use ($userId) {
            $query->whereHas('sale', function($q) use ($userId) {
                $q->where('created_by', $userId);
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
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return response()->json($this->getSuperAdminStats());
        } elseif ($user->hasRole('admin')) {
            return response()->json($this->getAdminStats());
        } elseif ($user->hasRole('seller')) {
            return response()->json($this->getSellerStats());
        } else {
            return response()->json($this->getBasicStats());
        }
    }
}