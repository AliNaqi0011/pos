<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialService
{
    public function recordSaleTransaction(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $transactionId = 'TXN-' . time() . '-' . $sale->id;
            
            // Debit: Cash/Accounts Receivable
            $this->createLedgerEntry([
                'transaction_id' => $transactionId,
                'account_id' => $this->getAccountId('cash'),
                'debit' => $sale->final_total,
                'credit' => 0,
                'description' => "Sale #{$sale->id} - {$sale->customer->name}",
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'transaction_date' => $sale->sale_date,
            ]);
            
            // Credit: Sales Revenue
            $this->createLedgerEntry([
                'transaction_id' => $transactionId,
                'account_id' => $this->getAccountId('sales_revenue'),
                'debit' => 0,
                'credit' => $sale->total_amount,
                'description' => "Sale #{$sale->id} - Revenue",
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'transaction_date' => $sale->sale_date,
            ]);
            
            // Credit: Tax Payable (if applicable)
            if ($sale->tax_amount > 0) {
                $this->createLedgerEntry([
                    'transaction_id' => $transactionId,
                    'account_id' => $this->getAccountId('tax_payable'),
                    'debit' => 0,
                    'credit' => $sale->tax_amount,
                    'description' => "Sale #{$sale->id} - Tax",
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'transaction_date' => $sale->sale_date,
                ]);
            }
            
            // Record COGS
            $this->recordCostOfGoodsSold($sale, $transactionId);
        });
    }

    public function generateProfitLossStatement(Carbon $startDate, Carbon $endDate): array
    {
        $revenue = $this->getAccountBalance('sales_revenue', $startDate, $endDate);
        $cogs = $this->getAccountBalance('cost_of_goods_sold', $startDate, $endDate);
        $expenses = $this->getExpenses($startDate, $endDate);
        
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses['total'];
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'revenue' => [
                'sales_revenue' => $revenue,
                'other_income' => $this->getAccountBalance('other_income', $startDate, $endDate),
                'total_revenue' => $revenue,
            ],
            'cost_of_goods_sold' => $cogs,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'profit_margin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
        ];
    }

    public function generateBalanceSheet(Carbon $asOfDate): array
    {
        return [
            'as_of_date' => $asOfDate->format('Y-m-d'),
            'assets' => [
                'current_assets' => [
                    'cash' => $this->getAccountBalance('cash', null, $asOfDate),
                    'accounts_receivable' => $this->getAccountBalance('accounts_receivable', null, $asOfDate),
                    'inventory' => $this->getInventoryValue(),
                    'prepaid_expenses' => $this->getAccountBalance('prepaid_expenses', null, $asOfDate),
                ],
                'fixed_assets' => [
                    'equipment' => $this->getAccountBalance('equipment', null, $asOfDate),
                    'furniture' => $this->getAccountBalance('furniture', null, $asOfDate),
                    'accumulated_depreciation' => $this->getAccountBalance('accumulated_depreciation', null, $asOfDate),
                ],
            ],
            'liabilities' => [
                'current_liabilities' => [
                    'accounts_payable' => $this->getAccountBalance('accounts_payable', null, $asOfDate),
                    'tax_payable' => $this->getAccountBalance('tax_payable', null, $asOfDate),
                    'accrued_expenses' => $this->getAccountBalance('accrued_expenses', null, $asOfDate),
                ],
                'long_term_liabilities' => [
                    'loans_payable' => $this->getAccountBalance('loans_payable', null, $asOfDate),
                ],
            ],
            'equity' => [
                'owner_equity' => $this->getAccountBalance('owner_equity', null, $asOfDate),
                'retained_earnings' => $this->getRetainedEarnings($asOfDate),
            ],
        ];
    }

    public function generateCashFlowStatement(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'operating_activities' => $this->getOperatingCashFlow($startDate, $endDate),
            'investing_activities' => $this->getInvestingCashFlow($startDate, $endDate),
            'financing_activities' => $this->getFinancingCashFlow($startDate, $endDate),
            'net_cash_flow' => $this->getNetCashFlow($startDate, $endDate),
        ];
    }

    public function calculateTaxes(Carbon $startDate, Carbon $endDate): array
    {
        $taxableIncome = $this->getTaxableIncome($startDate, $endDate);
        $taxRates = $this->getTaxRates();
        
        $taxes = [];
        foreach ($taxRates as $tax) {
            $taxAmount = $taxableIncome * ($tax['rate'] / 100);
            $taxes[] = [
                'name' => $tax['name'],
                'rate' => $tax['rate'],
                'amount' => $taxAmount,
            ];
        }
        
        return [
            'taxable_income' => $taxableIncome,
            'taxes' => $taxes,
            'total_tax' => array_sum(array_column($taxes, 'amount')),
        ];
    }

    private function createLedgerEntry(array $data): void
    {
        DB::table('general_ledger')->insert(array_merge($data, [
            'created_by' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    private function getAccountId(string $accountCode): int
    {
        return DB::table('chart_of_accounts')
            ->where('code', $accountCode)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->value('id');
    }

    private function getAccountBalance(string $accountCode, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = DB::table('general_ledger')
            ->join('chart_of_accounts', 'general_ledger.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.code', $accountCode)
            ->where('general_ledger.tenant_id', auth()->user()->tenant_id);
            
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        } elseif ($endDate) {
            $query->where('transaction_date', '<=', $endDate);
        }
        
        $result = $query->selectRaw('SUM(debit - credit) as balance')->first();
        
        return $result->balance ?? 0;
    }

    private function recordCostOfGoodsSold(Sale $sale, string $transactionId): void
    {
        $totalCogs = 0;
        
        foreach ($sale->saleItems as $item) {
            $cogs = $item->quantity * $item->product->cost_price;
            $totalCogs += $cogs;
        }
        
        if ($totalCogs > 0) {
            // Debit: Cost of Goods Sold
            $this->createLedgerEntry([
                'transaction_id' => $transactionId,
                'account_id' => $this->getAccountId('cost_of_goods_sold'),
                'debit' => $totalCogs,
                'credit' => 0,
                'description' => "Sale #{$sale->id} - COGS",
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'transaction_date' => $sale->sale_date,
            ]);
            
            // Credit: Inventory
            $this->createLedgerEntry([
                'transaction_id' => $transactionId,
                'account_id' => $this->getAccountId('inventory'),
                'debit' => 0,
                'credit' => $totalCogs,
                'description' => "Sale #{$sale->id} - Inventory Reduction",
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'transaction_date' => $sale->sale_date,
            ]);
        }
    }

    private function getExpenses(Carbon $startDate, Carbon $endDate): array
    {
        $expenses = DB::table('general_ledger')
            ->join('chart_of_accounts', 'general_ledger.account_id', '=', 'chart_of_accounts.id')
            ->where('chart_of_accounts.type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('general_ledger.tenant_id', auth()->user()->tenant_id)
            ->selectRaw('chart_of_accounts.name, SUM(debit - credit) as amount')
            ->groupBy('chart_of_accounts.id', 'chart_of_accounts.name')
            ->get();
            
        return [
            'details' => $expenses->toArray(),
            'total' => $expenses->sum('amount'),
        ];
    }

    private function getInventoryValue(): float
    {
        return DB::table('products')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->selectRaw('SUM(quantity * cost_price) as value')
            ->value('value') ?? 0;
    }

    private function getRetainedEarnings(Carbon $asOfDate): float
    {
        // Calculate retained earnings as accumulated profits
        return DB::table('general_ledger')
            ->join('chart_of_accounts', 'general_ledger.account_id', '=', 'chart_of_accounts.id')
            ->whereIn('chart_of_accounts.type', ['revenue', 'expense'])
            ->where('transaction_date', '<=', $asOfDate)
            ->where('general_ledger.tenant_id', auth()->user()->tenant_id)
            ->selectRaw('SUM(CASE WHEN chart_of_accounts.type = "revenue" THEN credit - debit ELSE debit - credit END) as earnings')
            ->value('earnings') ?? 0;
    }

    private function getOperatingCashFlow(Carbon $startDate, Carbon $endDate): array
    {
        // Simplified operating cash flow calculation
        $netIncome = $this->getAccountBalance('sales_revenue', $startDate, $endDate) - 
                    $this->getAccountBalance('cost_of_goods_sold', $startDate, $endDate) -
                    $this->getExpenses($startDate, $endDate)['total'];
                    
        return [
            'net_income' => $netIncome,
            'adjustments' => [],
            'total' => $netIncome,
        ];
    }

    private function getInvestingCashFlow(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'equipment_purchases' => 0,
            'asset_sales' => 0,
            'total' => 0,
        ];
    }

    private function getFinancingCashFlow(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'loan_proceeds' => 0,
            'loan_payments' => 0,
            'owner_contributions' => 0,
            'owner_withdrawals' => 0,
            'total' => 0,
        ];
    }

    private function getNetCashFlow(Carbon $startDate, Carbon $endDate): float
    {
        $operating = $this->getOperatingCashFlow($startDate, $endDate)['total'];
        $investing = $this->getInvestingCashFlow($startDate, $endDate)['total'];
        $financing = $this->getFinancingCashFlow($startDate, $endDate)['total'];
        
        return $operating + $investing + $financing;
    }

    private function getTaxableIncome(Carbon $startDate, Carbon $endDate): float
    {
        $revenue = $this->getAccountBalance('sales_revenue', $startDate, $endDate);
        $expenses = $this->getExpenses($startDate, $endDate)['total'];
        
        return max(0, $revenue - $expenses);
    }

    private function getTaxRates(): array
    {
        return DB::table('tax_rates')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('effective_from', '<=', now())
            ->where(function($query) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', now());
            })
            ->get(['name', 'rate'])
            ->toArray();
    }
}