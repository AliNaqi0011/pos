<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_low_stock_alerts()
    {
        Product::factory()->create([
            'name' => 'Low Stock Item',
            'quantity' => 2,
            'stock_alert_level' => 5
        ]);
        
        Product::factory()->create([
            'name' => 'Normal Stock Item',
            'quantity' => 10,
            'min_stock_level' => 5
        ]);

        $response = $this->actingAs($this->admin)->get('/inventory/alerts');
        
        $response->assertStatus(200);
        $response->assertSee('Low Stock Item');
        $response->assertDontSee('Normal Stock Item');
    }

    public function test_stock_movement_tracking()
    {
        $product = Product::factory()->create(['quantity' => 10]);
        
        $response = $this->actingAs($this->admin)->post('/inventory/stock-movement', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'reason' => 'Purchase'
        ]);

        $product->refresh();
        $this->assertEquals(15, $product->quantity);
        $this->assertEquals(1, StockMovement::count());
        
        $movement = StockMovement::first();
        $this->assertEquals('in', $movement->type);
        $this->assertEquals(5, $movement->quantity);
    }

    public function test_abc_analysis_categorization()
    {
        // Create products with different sales volumes
        $productA = Product::factory()->create(['name' => 'High Volume']);
        $productB = Product::factory()->create(['name' => 'Medium Volume']);
        $productC = Product::factory()->create(['name' => 'Low Volume']);

        $response = $this->actingAs($this->admin)->get('/inventory/abc-analysis');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
    }

    public function test_reorder_point_calculation()
    {
        $product = Product::factory()->create([
            'quantity' => 3,
            'stock_alert_level' => 5
        ]);

        $response = $this->actingAs($this->admin)->get('/inventory/reorder-suggestions');
        
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_supplier_performance_tracking()
    {
        $supplier = Supplier::factory()->create();
        
        $response = $this->actingAs($this->admin)->get('/inventory/supplier-performance');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
    }
}