<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class FinancialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_profit_loss_calculation()
    {
        // Create sales
        $sale = Sale::factory()->create(['final_total' => 1000]);
        
        // Create product with cost
        $product = Product::factory()->create(['cost_price' => 50]);
        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'product_price' => 100
        ]);
        
        // Create expense
        Expense::factory()->create(['amount' => 200]);

        $response = $this->actingAs($this->admin)->get('/financial/profit-loss');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertEquals(1000, $data['totalRevenue']);
        $this->assertEquals(500, $data['totalCost']); // 10 * 50
        $this->assertEquals(200, $data['totalExpenses']);
        $this->assertEquals(300, $data['netProfit']); // 1000 - 500 - 200
    }

    public function test_cash_flow_tracking()
    {
        Sale::factory()->create([
            'final_total' => 500,
            'created_at' => now()
        ]);
        
        Expense::factory()->create([
            'amount' => 100,
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->admin)->get('/financial/cash-flow');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertEquals(500, $data['totalInflow']);
        $this->assertEquals(100, $data['totalOutflow']);
        $this->assertEquals(400, $data['netCashFlow']);
    }

    public function test_expense_creation_validation()
    {
        $response = $this->actingAs($this->admin)->post('/financial/expenses', [
            'description' => '',
            'amount' => 'invalid',
            'category' => ''
        ]);

        $response->assertSessionHasErrors(['description', 'amount', 'category']);
    }

    public function test_budget_vs_actual_comparison()
    {
        // Create actual expenses
        Expense::factory()->create([
            'category' => 'Marketing',
            'amount' => 800
        ]);

        $response = $this->actingAs($this->admin)->get('/financial/budget-analysis');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
    }
}