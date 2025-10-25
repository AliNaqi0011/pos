<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\LoyaltyProgram;
use App\Models\Promotion;
use App\Models\TaxRate;
use App\Models\PaymentMethod;
use App\Models\ChartOfAccounts;

class ComprehensiveSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCurrencies();
        $this->seedChartOfAccounts();
        $this->seedTaxRates();
        $this->seedPaymentMethods();
        $this->seedLoyaltyPrograms();
        $this->seedPromotions();
    }

    private function seedCurrencies(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0000, 'is_base' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.8500, 'is_base' => false],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.7300, 'is_base' => false],
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'exchange_rate' => 280.0000, 'is_base' => false],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 83.0000, 'is_base' => false],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'exchange_rate' => 3.6700, 'is_base' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(['code' => $currency['code']], $currency);
        }
    }

    private function seedChartOfAccounts(): void
    {
        $accounts = [
            // Assets
            ['code' => 'cash', 'name' => 'Cash', 'type' => 'asset', 'subtype' => 'current'],
            ['code' => 'acc_receivable', 'name' => 'Accounts Receivable', 'type' => 'asset', 'subtype' => 'current'],
            ['code' => 'inventory', 'name' => 'Inventory', 'type' => 'asset', 'subtype' => 'current'],
            ['code' => 'prepaid_expenses', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'subtype' => 'current'],
            ['code' => 'equipment', 'name' => 'Equipment', 'type' => 'asset', 'subtype' => 'non_current'],
            ['code' => 'furniture', 'name' => 'Furniture & Fixtures', 'type' => 'asset', 'subtype' => 'non_current'],
            ['code' => 'acc_depreciation', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'subtype' => 'non_current'],
            
            // Liabilities
            ['code' => 'acc_payable', 'name' => 'Accounts Payable', 'type' => 'liability', 'subtype' => 'current'],
            ['code' => 'tax_payable', 'name' => 'Tax Payable', 'type' => 'liability', 'subtype' => 'current'],
            ['code' => 'accrued_expenses', 'name' => 'Accrued Expenses', 'type' => 'liability', 'subtype' => 'current'],
            ['code' => 'loans_payable', 'name' => 'Loans Payable', 'type' => 'liability', 'subtype' => 'non_current'],
            
            // Equity
            ['code' => 'owner_equity', 'name' => 'Owner Equity', 'type' => 'equity', 'subtype' => 'operating'],
            ['code' => 'retained_earnings', 'name' => 'Retained Earnings', 'type' => 'equity', 'subtype' => 'operating'],
            
            // Revenue
            ['code' => 'sales_revenue', 'name' => 'Sales Revenue', 'type' => 'revenue', 'subtype' => 'operating'],
            ['code' => 'other_income', 'name' => 'Other Income', 'type' => 'revenue', 'subtype' => 'non_operating'],
            
            // Expenses
            ['code' => 'cogs', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'subtype' => 'operating'],
            ['code' => 'rent_expense', 'name' => 'Rent Expense', 'type' => 'expense', 'subtype' => 'operating'],
            ['code' => 'utilities_expense', 'name' => 'Utilities Expense', 'type' => 'expense', 'subtype' => 'operating'],
            ['code' => 'salaries_expense', 'name' => 'Salaries Expense', 'type' => 'expense', 'subtype' => 'operating'],
            ['code' => 'marketing_expense', 'name' => 'Marketing Expense', 'type' => 'expense', 'subtype' => 'operating'],
            ['code' => 'depreciation', 'name' => 'Depreciation Expense', 'type' => 'expense', 'subtype' => 'operating'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccounts::firstOrCreate(
                ['code' => $account['code'], 'tenant_id' => 1], 
                array_merge($account, ['tenant_id' => 1])
            );
        }
    }

    private function seedTaxRates(): void
    {
        $taxRates = [
            ['name' => 'GST', 'rate' => 18.00, 'type' => 'exclusive', 'effective_from' => '2024-01-01'],
            ['name' => 'VAT', 'rate' => 15.00, 'type' => 'inclusive', 'effective_from' => '2024-01-01'],
            ['name' => 'Sales Tax', 'rate' => 10.00, 'type' => 'exclusive', 'effective_from' => '2024-01-01'],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::firstOrCreate(
                ['name' => $taxRate['name'], 'tenant_id' => 1],
                array_merge($taxRate, ['tenant_id' => 1])
            );
        }
    }

    private function seedPaymentMethods(): void
    {
        $paymentMethods = [
            ['name' => 'Cash', 'code' => 'cash', 'type' => 'cash'],
            ['name' => 'Credit Card', 'code' => 'credit_card', 'type' => 'card'],
            ['name' => 'Debit Card', 'code' => 'debit_card', 'type' => 'card'],
            ['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'type' => 'bank_transfer'],
            ['name' => 'Mobile Payment', 'code' => 'mobile_payment', 'type' => 'digital_wallet'],
            ['name' => 'Check', 'code' => 'check', 'type' => 'check'],
            ['name' => 'Store Credit', 'code' => 'store_credit', 'type' => 'credit'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(
                ['code' => $method['code'], 'tenant_id' => 1],
                array_merge($method, ['tenant_id' => 1])
            );
        }
    }

    private function seedLoyaltyPrograms(): void
    {
        LoyaltyProgram::firstOrCreate([
            'name' => 'Default Loyalty Program',
            'tenant_id' => 1,
        ], [
            'description' => 'Earn 1 point for every dollar spent',
            'points_per_currency' => 1.00,
            'currency_per_point' => 0.01,
            'min_points_redeem' => 100,
            'start_date' => now(),
            'is_active' => true,
            'tenant_id' => 1,
        ]);
    }

    private function seedPromotions(): void
    {
        $promotions = [
            [
                'name' => 'Welcome Discount',
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_purchase_amount' => 50.00,
                'usage_limit' => 1000,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'tenant_id' => 1,
            ],
            [
                'name' => 'Birthday Special',
                'code' => 'BIRTHDAY20',
                'type' => 'percentage',
                'value' => 20.00,
                'min_purchase_amount' => 100.00,
                'usage_limit' => null,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'tenant_id' => 1,
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::firstOrCreate(
                ['code' => $promotion['code'], 'tenant_id' => $promotion['tenant_id']],
                $promotion
            );
        }
    }
}