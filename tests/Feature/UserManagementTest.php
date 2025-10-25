<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
    }

    public function test_admin_can_create_seller_with_correct_created_by()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Test Seller',
            'email' => 'seller@test.com',
            'phone_number' => '1234567890',
            'password' => 'password123'
        ]);

        $seller = User::where('email', 'seller@test.com')->first();
        
        $this->assertEquals($admin->id, $seller->created_by);
        $this->assertTrue($seller->hasRole('seller'));
        $response->assertRedirect('/users');
    }

    public function test_super_admin_creates_admin_without_created_by()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->actingAs($superAdmin)->post('/users', [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'phone_number' => '1234567890',
            'password' => 'password123'
        ]);

        $admin = User::where('email', 'admin@test.com')->first();
        
        $this->assertNull($admin->created_by);
        $this->assertTrue($admin->hasRole('admin'));
    }

    public function test_admin_only_sees_own_sellers()
    {
        $admin1 = User::factory()->create();
        $admin1->assignRole('admin');
        
        $admin2 = User::factory()->create();
        $admin2->assignRole('admin');
        
        $seller1 = User::factory()->create(['created_by' => $admin1->id]);
        $seller1->assignRole('seller');
        
        $seller2 = User::factory()->create(['created_by' => $admin2->id]);
        $seller2->assignRole('seller');

        $response = $this->actingAs($admin1)->get('/users');
        
        $response->assertSee($admin1->name);
        $response->assertSee($seller1->name);
        $response->assertDontSee($seller2->name);
    }

    public function test_user_validation_rules()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/users', [
            'name' => '',
            'email' => 'invalid-email',
            'phone_number' => '',
            'password' => '123'
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone_number', 'password']);
    }
}