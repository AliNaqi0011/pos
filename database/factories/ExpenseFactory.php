<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'warehouse_id' => \App\Models\Warehouse::factory(),
            'expense_category_id' => \App\Models\ExpenseCategory::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'reference_code' => 'EXP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence(3),
            'details' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}