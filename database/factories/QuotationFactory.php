<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'subtotal' => $this->faker->randomFloat(2, 100, 1000),
            'tax' => $this->faker->randomFloat(2, 10, 100),
            'total' => $this->faker->randomFloat(2, 110, 1100),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'quotation_date' => $this->faker->date(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}