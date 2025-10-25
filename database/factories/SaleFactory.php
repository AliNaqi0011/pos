<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'user_id' => User::factory(),
            'total_amount' => $this->faker->randomFloat(2, 50, 1000),
            'tax_amount' => $this->faker->randomFloat(2, 5, 100),
            'discount_amount' => $this->faker->randomFloat(2, 0, 50),
            'final_total' => $this->faker->randomFloat(2, 100, 1500),
            'status' => 'final',
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid', 'partial']),
            'sale_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}