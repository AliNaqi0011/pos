<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_relationships()
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->sales->contains($sale));
    }

    public function test_product_model_relationships()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_sale_model_calculations()
    {
        $sale = Sale::factory()->create([
            'total_amount' => 100,
            'tax_amount' => 10,
            'discount_amount' => 5,
            'final_total' => 105
        ]);

        $this->assertEquals(105, $sale->final_total);
    }

    public function test_customer_model_relationships()
    {
        $customer = Customer::factory()->create();
        $sale = Sale::factory()->create(['customer_id' => $customer->id]);

        $this->assertTrue($customer->sales->contains($sale));
    }

    public function test_product_stock_management()
    {
        $product = Product::factory()->create(['quantity' => 10]);

        $product->decreaseStock(3);
        $this->assertEquals(7, $product->quantity);

        $product->increaseStock(5);
        $this->assertEquals(12, $product->quantity);
    }

    public function test_model_validation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Product::create([
            'name' => null, // Required field
            'product_price' => 100
        ]);
    }
}