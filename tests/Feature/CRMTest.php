<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\LoyaltyPoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class CRMTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_customer_segmentation()
    {
        // Create customers with different spending patterns
        $highValue = Customer::factory()->create();
        Sale::factory()->create([
            'customer_id' => $highValue->id,
            'final_total' => 5000
        ]);
        
        $lowValue = Customer::factory()->create();
        Sale::factory()->create([
            'customer_id' => $lowValue->id,
            'final_total' => 100
        ]);

        $response = $this->actingAs($this->admin)->get('/crm/segmentation');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertArrayHasKey('highValue', $data);
        $this->assertArrayHasKey('lowValue', $data);
    }

    public function test_loyalty_points_calculation()
    {
        $customer = Customer::factory()->create();
        
        $response = $this->actingAs($this->admin)->post('/crm/loyalty/add-points', [
            'customer_id' => $customer->id,
            'points' => 100,
            'reason' => 'Purchase reward'
        ]);

        $this->assertEquals(1, LoyaltyPoint::count());
        
        $loyaltyPoint = LoyaltyPoint::first();
        $this->assertEquals(100, $loyaltyPoint->points);
        $this->assertEquals('earned', $loyaltyPoint->type);
    }

    public function test_customer_lifetime_value()
    {
        $customer = Customer::factory()->create();
        
        // Create multiple sales for the customer
        Sale::factory()->count(3)->create([
            'customer_id' => $customer->id,
            'final_total' => 500
        ]);

        $response = $this->actingAs($this->admin)->get('/crm/analytics');
        
        $response->assertStatus(200);
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $customerData = collect($data['customerAnalytics'])->firstWhere('id', $customer->id);
        $this->assertEquals(1500, $customerData['totalSpent']);
    }

    public function test_loyalty_program_redemption()
    {
        $customer = Customer::factory()->create();
        
        // Add points first
        LoyaltyPoint::factory()->create([
            'customer_id' => $customer->id,
            'points' => 200,
            'type' => 'earned'
        ]);

        $response = $this->actingAs($this->admin)->post('/crm/loyalty/redeem', [
            'customer_id' => $customer->id,
            'points' => 100,
            'reason' => 'Discount applied'
        ]);

        $redeemed = LoyaltyPoint::where('type', 'redeemed')->first();
        $this->assertEquals(100, $redeemed->points);
        
        $totalPoints = LoyaltyPoint::where('customer_id', $customer->id)->sum('points');
        $this->assertEquals(100, $totalPoints); // 200 earned - 100 redeemed
    }
}