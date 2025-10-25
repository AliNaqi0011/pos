<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_warehouse_creation()
    {
        $response = $this->actingAs($this->admin)->post('/warehouses', [
            'name' => 'Main Warehouse',
            'location' => 'Downtown',
            'description' => 'Main warehouse description'
        ]);

        $this->assertDatabaseHas('warehouses', ['name' => 'Main Warehouse']);
    }

    public function test_warehouse_product_assignment()
    {
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->post('/warehouse-products', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 50
        ]);

        $this->assertDatabaseHas('warehouse_products', [
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id
        ]);
    }

    public function test_warehouse_stock_transfer()
    {
        $warehouse1 = Warehouse::factory()->create();
        $warehouse2 = Warehouse::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->post('/stock-transfers', [
            'from_warehouse_id' => $warehouse1->id,
            'to_warehouse_id' => $warehouse2->id,
            'product_id' => $product->id,
            'quantity' => 10
        ]);

        $this->assertDatabaseHas('stock_transfers', [
            'from_warehouse_id' => $warehouse1->id,
            'to_warehouse_id' => $warehouse2->id
        ]);
    }
}