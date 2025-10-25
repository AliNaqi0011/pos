<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;

class FinancialController extends Controller
{
    public function chartOfAccounts()
    {
        // Placeholder accounts structure
        $accounts = collect([
            ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'balance' => 50000],
            ['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'balance' => 25000],
            ['code' => '1200', 'name' => 'Inventory', 'type' => 'Asset', 'balance' => 75000],
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'balance' => 15000],
            ['code' => '3000', 'name' => 'Owner Equity', 'type' => 'Equity', 'balance' => 135000],
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'balance' => Sale::sum('total') ?? 0],
            ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense', 'balance' => Purchase::sum('grand_total') ?? 0],
            ['code' => '6000', 'name' => 'Operating Expenses', 'type' => 'Expense', 'balance' => Expense::sum('amount') ?? 0]
        ]);
        return view('financial.chart-of-accounts', compact('accounts'));
    }

    public function journalEntries()
    {
        // Placeholder for journal entries
        return view('financial.journal-entries');
    }

    public function balanceSheet()
    {
        $data = [
            'assets' => $this->calculateAssets(),
            'liabilities' => $this->calculateLiabilities(),
            'equity' => $this->calculateEquity()
        ];
        
        return view('financial.balance-sheet', compact('data'));
    }

    public function profitLoss()
    {
        $data = [
            'revenue' => Sale::sum('total'),
            'expenses' => Expense::sum('amount'),
            'cost_of_goods' => Purchase::sum('grand_total')
        ];
        
        $data['gross_profit'] = $data['revenue'] - $data['cost_of_goods'];
        $data['net_profit'] = $data['gross_profit'] - $data['expenses'];
        
        return view('financial.profit-loss', compact('data'));
    }

    private function calculateAssets()
    {
        return [
            'current_assets' => 0,
            'fixed_assets' => 0,
            'total_assets' => 0
        ];
    }

    private function calculateLiabilities()
    {
        return [
            'current_liabilities' => 0,
            'long_term_liabilities' => 0,
            'total_liabilities' => 0
        ];
    }

    private function calculateEquity()
    {
        return [
            'owner_equity' => 0,
            'retained_earnings' => 0,
            'total_equity' => 0
        ];
    }
}