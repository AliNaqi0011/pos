<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Sale;

class CRMController extends Controller
{
    public function loyaltyPrograms()
    {
        // Placeholder loyalty programs
        $programs = collect([
            ['id' => 1, 'name' => 'Bronze Tier', 'points_required' => 100, 'discount' => 5],
            ['id' => 2, 'name' => 'Silver Tier', 'points_required' => 500, 'discount' => 10],
            ['id' => 3, 'name' => 'Gold Tier', 'points_required' => 1000, 'discount' => 15],
            ['id' => 4, 'name' => 'Platinum Tier', 'points_required' => 2500, 'discount' => 20]
        ]);
        return view('crm.loyalty-programs', compact('programs'));
    }

    public function customerPoints()
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'total')
            ->get()
            ->map(function($customer) {
                $customer->points = ($customer->sales_sum_total ?? 0) / 10; // 1 point per $10 spent
                return $customer;
            });
        return view('crm.customer-points', compact('customers'));
    }

    public function rewards()
    {
        return view('crm.rewards');
    }

    public function customerAnalytics()
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::whereHas('sales', function($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        })->count();
        
        $data = [
            'totalCustomers' => $totalCustomers,
            'activeMembers' => $activeCustomers,
            'avgPurchase' => Sale::avg('total') ?? 0,
            'retentionRate' => $totalCustomers > 0 ? ($activeCustomers / $totalCustomers) * 100 : 0,
            'topCustomers' => Customer::withSum('sales', 'total')
                ->withCount('sales')
                ->orderBy('sales_sum_total', 'desc')
                ->limit(10)
                ->get()
                ->map(function($customer) {
                    return (object) [
                        'name' => $customer->name,
                        'total_spent' => $customer->sales_sum_total ?? 0,
                        'total_orders' => $customer->sales_count ?? 0
                    ];
                })
        ];
        
        return view('crm.customer-analytics', compact('data'));
    }
}