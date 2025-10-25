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

class SalesManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'seller']);
        $this->seller = User::factory()->create();
        $this->seller->assignRole('seller');
    }

    public function test_sale_creation()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        $response = $this->actingAs($this->seller)->post('/sales', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ],
            'total_amount' => 200,
            'final_total' => 200
        ]);

        $this->assertDatabaseHas('sales', ['customer_id' => $customer->id]);
        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id]);
    }

    public function test_sale_updates_inventory()
    {
        $product = Product::factory()->create(['quantity' => 10]);
        $customer = Customer::factory()->create();

        $this->actingAs($this->seller)->post('/sales', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'price' => 100
                ]
            ],
            'subtotal' => 300,
            'total' => 300
        ]);

        $product->refresh();
        $this->assertEquals(7, $product->quantity);
    }

    public function test_sale_validation()
    {
        $response = $this->actingAs($this->seller)->post('/sales', [
            'items' => []
        ]);

        $response->assertSessionHasErrors();
    }
}