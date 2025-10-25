<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super_admin');
    }

    public function test_plan_creation()
    {
        $response = $this->actingAs($this->superAdmin)->post('/plans', [
            'name' => 'Basic Plan',
            'price' => 29.99,
            'slug' => 'basic-plan',
            'stripe_plan' => 'plan_basic',
            'description' => 'Basic plan description'
        ]);

        $this->assertDatabaseHas('plans', ['name' => 'Basic Plan']);
    }

    public function test_subscription_creation()
    {
        $plan = Plan::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)->post('/subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id
        ]);
    }

    public function test_subscription_status_update()
    {
        $subscription = Subscription::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->superAdmin)->put("/subscriptions/{$subscription->id}", [
            'status' => 'active'
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'active'
        ]);
    }

    public function test_subscription_expiry_check()
    {
        $expiredSubscription = Subscription::factory()->create([
            'expires_at' => now()->subDay(),
            'status' => 'active'
        ]);

        $response = $this->get('/check-subscriptions');

        $expiredSubscription->refresh();
        $this->assertEquals('expired', $expiredSubscription->status);
    }
}