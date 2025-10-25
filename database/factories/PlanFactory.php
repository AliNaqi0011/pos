<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true) . ' Plan',
            'slug' => $this->faker->slug(),
            'stripe_plan' => 'plan_' . $this->faker->uuid(),
            'price' => $this->faker->numberBetween(999, 9999),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}