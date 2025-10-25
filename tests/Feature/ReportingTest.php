<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_sales_report_generation()
    {
        Sale::factory()->count(5)->create([
            'created_at' => Carbon::now()->subDays(5)
        ]);

        $response = $this->actingAs($this->admin)->get('/reports/sales');

        $response->assertStatus(200);
        $response->assertViewHas('sales');
    }

    public function test_product_report_generation()
    {
        $product = Product::factory()->create();
        SaleItem::factory()->create([
            'product_id' => $product->id,
            'quantity' => 10
        ]);

        $response = $this->actingAs($this->admin)->get('/reports/products');

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_customer_report_generation()
    {
        $customer = Customer::factory()->create();
        Sale::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($this->admin)->get('/reports/customers');

        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }

    public function test_date_filtered_reports()
    {
        Sale::factory()->create([
            'created_at' => Carbon::now()->subDays(10),
            'final_total' => 100
        ]);
        
        Sale::factory()->create([
            'created_at' => Carbon::now()->subDays(2),
            'final_total' => 200
        ]);

        $response = $this->actingAs($this->admin)->get('/reports/sales?from=' . Carbon::now()->subDays(5)->format('Y-m-d'));

        $response->assertStatus(200);
    }

    public function test_pdf_report_export()
    {
        Sale::factory()->create();

        $response = $this->actingAs($this->admin)->get('/reports/sales/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}