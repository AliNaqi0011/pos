<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class PurchaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_purchase_creation()
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'cost_price' => 50
                ]
            ],
            'total_amount' => 500
        ]);

        $this->assertDatabaseHas('purchases', ['supplier_id' => $supplier->id]);
    }

    public function test_purchase_updates_inventory()
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['quantity' => 5]);

        $this->actingAs($this->admin)->post('/purchases', [
            'supplier_id' => $supplier->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'cost_price' => 50
                ]
            ],
            'total_amount' => 500
        ]);

        $product->refresh();
        $this->assertEquals(15, $product->quantity);
    }

    public function test_purchase_validation()
    {
        $response = $this->actingAs($this->admin)->post('/purchases', [
            'items' => []
        ]);

        $response->assertSessionHasErrors();
    }
}