<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_customer_creation()
    {
        $response = $this->actingAs($this->admin)->post('/customers', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St'
        ]);

        $this->assertDatabaseHas('customers', ['name' => 'John Doe']);
    }

    public function test_customer_validation()
    {
        $response = $this->actingAs($this->admin)->post('/customers', [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_customer_update()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)->put("/customers/{$customer->id}", [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com'
        ]);

        $this->assertDatabaseHas('customers', ['name' => 'Updated Customer']);
    }

    public function test_customer_deletion()
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/customers/{$customer->id}");

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}