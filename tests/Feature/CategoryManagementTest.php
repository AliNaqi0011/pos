<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_category_creation()
    {
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => 'Electronics',
            'description' => 'Electronic items'
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_category_validation()
    {
        $response = $this->actingAs($this->admin)->post('/categories', [
            'name' => ''
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_category_update()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->put("/categories/{$category->id}", [
            'name' => 'Updated Category'
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }
}