<?php

namespace Database\Factories;

use App\Models\LoyaltyPoint;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'points' => $this->faker->numberBetween(10, 500),
            'type' => $this->faker->randomElement(['earned', 'redeemed']),
            'reason' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}