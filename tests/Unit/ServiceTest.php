<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CartService;
use App\Services\CurrencyService;
use App\Services\NotificationService;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_service_add_item()
    {
        $cartService = new CartService();
        $product = Product::factory()->create(['price' => 100]);

        $cartService->addItem($product->id, 2, 100);
        
        $this->assertEquals(200, $cartService->getTotal());
    }

    public function test_cart_service_remove_item()
    {
        $cartService = new CartService();
        $product = Product::factory()->create();

        $cartService->addItem($product->id, 2, 100);
        $cartService->removeItem($product->id);
        
        $this->assertEquals(0, $cartService->getTotal());
    }

    public function test_currency_service_formatting()
    {
        $currencyService = new CurrencyService();
        
        $formatted = $currencyService->format(1234.56);
        $this->assertEquals('PKR 1,234.56', $formatted);
    }

    public function test_notification_service_send()
    {
        $notificationService = new NotificationService();
        $user = User::factory()->create();

        $result = $notificationService->send($user, 'Test Message', 'info');
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'data' => json_encode(['message' => 'Test Message', 'type' => 'info'])
        ]);
    }

    public function test_service_dependency_injection()
    {
        $cartService = app(CartService::class);
        $this->assertInstanceOf(CartService::class, $cartService);
    }
}