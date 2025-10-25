<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
    }

    public function test_unauthorized_access_blocked()
    {
        $seller = User::factory()->create();
        $seller->assignRole('seller');

        // Seller trying to access admin routes
        $response = $this->actingAs($seller)->get('/admin/users');
        $response->assertStatus(403);
        
        $response = $this->actingAs($seller)->get('/financial/profit-loss');
        $response->assertStatus(403);
    }

    public function test_super_admin_redirect_middleware()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        // Super admin should be redirected from tenant routes
        $response = $this->actingAs($superAdmin)->get('/pos');
        $response->assertRedirect('/super-admin/dashboard');
    }

    public function test_rate_limiting_protection()
    {
        $user = User::factory()->create();
        
        // Make multiple rapid requests
        for ($i = 0; $i < 65; $i++) {
            $response = $this->actingAs($user)->get('/api/products');
        }
        
        // Should be rate limited after 60 requests
        $response->assertStatus(429);
    }

    public function test_input_validation_prevents_xss()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/users', [
            'name' => '<script>alert("xss")</script>',
            'email' => 'test@example.com',
            'phone_number' => '1234567890',
            'password' => 'password123'
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertStringNotContainsString('<script>', $user->name);
    }

    public function test_sql_injection_prevention()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Attempt SQL injection in search
        $response = $this->actingAs($admin)->get('/products?search=\'; DROP TABLE products; --');
        
        $response->assertStatus(200);
        // Products table should still exist
        $this->assertDatabaseHas('products', []);
    }

    public function test_csrf_protection()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Request without CSRF token should fail
        $response = $this->post('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(419); // CSRF token mismatch
    }
}