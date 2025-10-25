<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class POSTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'seller']);
        Role::create(['name' => 'admin']);
    }

    public function test_pos_checkout_creates_sale_correctly()
    {
        $user = User::factory()->create();
        $user->assignRole('seller');
        
        $product = Product::factory()->create([
            'price' => 100,
            'quantity' => 10
        ]);
        
        $customer = Customer::factory()->create();

        $response = $this->actingAs($user)->post('/pos/checkout', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ],
            'total_amount' => 200,
            'tax_amount' => 20,
            'final_total' => 220
        ]);

        $sale = Sale::first();
        $this->assertEquals(220, $sale->final_total);
        $this->assertEquals($customer->id, $sale->customer_id);
        $this->assertEquals(1, SaleItem::count());
    }

    public function test_pos_validates_insufficient_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('seller');
        
        $product = Product::factory()->create([
            'quantity' => 1
        ]);

        $response = $this->actingAs($user)->post('/pos/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5
                ]
            ]
        ]);

        $response->assertSessionHasErrors();
        $this->assertEquals(0, Sale::count());
    }

    public function test_pos_updates_product_stock()
    {
        $user = User::factory()->create();
        $user->assignRole('seller');
        
        $product = Product::factory()->create(['quantity' => 10]);

        $this->actingAs($user)->post('/pos/checkout', [
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'price' => 100
                ]
            ],
            'total_amount' => 300,
            'final_total' => 300
        ]);

        $product->refresh();
        $this->assertEquals(7, $product->quantity);
    }
}