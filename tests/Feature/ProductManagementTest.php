<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_product_creation()
    {
        $category = Category::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/products', [
            'name' => 'Test Product',
            'product_price' => 100,
            'cost_price' => 50,
            'quantity' => 10,
            'category_id' => $category->id
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
        $response->assertRedirect();
    }

    public function test_product_validation()
    {
        $response = $this->actingAs($this->admin)->post('/products', [
            'name' => '',
            'price' => 'invalid'
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_product_update()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->put("/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 150
        ]);

        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_product_deletion()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/products/{$product->id}");

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}