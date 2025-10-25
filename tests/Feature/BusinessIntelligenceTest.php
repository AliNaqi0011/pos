<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class BusinessIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_analytics_dashboard_calculates_metrics_correctly()
    {
        // Create test data
        Sale::factory()->create(['final_total' => 100]);
        Sale::factory()->create(['final_total' => 200]);
        
        $response = $this->actingAs($this->admin)->get('/bi/analytics');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertEquals(300, $data['totalRevenue']);
        $this->assertEquals(2, $data['totalSales']);
        $this->assertEquals(150, $data['avgOrderValue']);
    }

    public function test_department_performance_groups_by_category()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        $product = Product::factory()->create(['category_id' => $category->id]);
        
        $sale = Sale::factory()->create();
        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'product_price' => 50
        ]);

        $response = $this->actingAs($this->admin)->get('/bi/performance');
        
        $response->assertStatus(200);
        $response->assertSee('Electronics');
    }

    public function test_sales_forecasting_generates_predictions()
    {
        // Create historical sales data
        Sale::factory()->count(5)->create([
            'created_at' => now()->subMonth(),
            'final_total' => 1000
        ]);

        $response = $this->actingAs($this->admin)->get('/bi/forecasting');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertArrayHasKey('forecast', $data);
        $this->assertArrayHasKey('nextMonth', $data['forecast']);
    }

    public function test_custom_report_generation()
    {
        Sale::factory()->create(['final_total' => 500]);
        
        $response = $this->actingAs($this->admin)->post('/bi/generate-report', [
            'type' => 'sales',
            'date_range' => 'last_30_days',
            'format' => 'pdf'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/html');
    }
}