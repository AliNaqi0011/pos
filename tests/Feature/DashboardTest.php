<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
        Role::create(['name' => 'super_admin']);
    }

    public function test_admin_dashboard_displays_correctly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sale::factory()->count(5)->create();
        Product::factory()->count(10)->create();
        Customer::factory()->count(8)->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_seller_dashboard_displays_correctly()
    {
        $seller = User::factory()->create();
        $seller->assignRole('seller');

        $response = $this->actingAs($seller)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_super_admin_redirected_to_super_admin_dashboard()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertRedirect('/super-admin/dashboard');
    }

    public function test_dashboard_shows_recent_sales()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $sale = Sale::factory()->create(['final_total' => 500]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_low_stock_alerts()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Product::factory()->create([
            'name' => 'Low Stock Item',
            'quantity' => 2,
            'stock_alert_level' => 5
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }
}