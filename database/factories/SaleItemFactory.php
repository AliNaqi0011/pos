<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'product_price' => $this->faker->randomFloat(2, 10, 500),
            'total' => $this->faker->randomFloat(2, 10, 5000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}