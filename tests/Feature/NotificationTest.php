<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\UserCreateNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
    }

    public function test_user_creation_sends_notification()
    {
        Notification::fake();
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->post('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '1234567890',
            'password' => 'password123'
        ]);

        Notification::assertSentTo(
            [$admin],
            UserCreateNotification::class
        );
    }

    public function test_notification_marking_as_read()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Create a notification
        $user->notify(new UserCreateNotification(User::factory()->create()));

        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->post("/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertTrue($notification->fresh()->read());
    }

    public function test_notification_list_display()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $user->notify(new UserCreateNotification(User::factory()->create()));

        $response = $this->actingAs($user)->get('/notifications');

        $response->assertStatus(200);
        $response->assertViewHas('notifications');
    }
}