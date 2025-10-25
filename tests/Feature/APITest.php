<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class APITest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'seller']);
        
        $this->user = User::factory()->create();
        $this->user->assignRole('seller');
    }

    public function test_api_authentication_required()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    public function test_api_products_endpoint()
    {
        Sanctum::actingAs($this->user);
        
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');
        
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_api_customers_endpoint()
    {
        Sanctum::actingAs($this->user);
        
        Customer::factory()->count(2)->create();

        $response = $this->getJson('/api/customers');
        
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_api_pos_checkout()
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create([
            'product_price' => 100,
            'quantity' => 10
        ]);
        
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/pos/checkout', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ],
            'subtotal' => 200,
            'tax' => 20,
            'total' => 220
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_api_validation_errors()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/pos/checkout', [
            'items' => []
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
    }

    public function test_api_rate_limiting()
    {
        Sanctum::actingAs($this->user);

        // Make 65 requests rapidly
        for ($i = 0; $i < 65; $i++) {
            $response = $this->getJson('/api/products');
        }

        $response->assertStatus(429);
    }
}