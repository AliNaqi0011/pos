<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_expense_creation()
    {
        $response = $this->actingAs($this->admin)->post('/expenses', [
            'description' => 'Office Rent',
            'amount' => 5000,
            'category' => 'Rent',
            'expense_date' => now()->format('Y-m-d')
        ]);

        $this->assertDatabaseHas('expenses', ['description' => 'Office Rent']);
    }

    public function test_expense_validation()
    {
        $response = $this->actingAs($this->admin)->post('/expenses', [
            'description' => '',
            'amount' => 'invalid'
        ]);

        $response->assertSessionHasErrors(['description', 'amount']);
    }

    public function test_expense_category_creation()
    {
        $response = $this->actingAs($this->admin)->post('/expense-categories', [
            'name' => 'Marketing',
            'description' => 'Marketing expenses'
        ]);

        $this->assertDatabaseHas('expense_categories', ['name' => 'Marketing']);
    }

    public function test_expense_update()
    {
        $expense = Expense::factory()->create();

        $response = $this->actingAs($this->admin)->put("/expenses/{$expense->id}", [
            'description' => 'Updated Expense',
            'amount' => 1000
        ]);

        $this->assertDatabaseHas('expenses', ['description' => 'Updated Expense']);
    }
}