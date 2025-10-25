<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class BarcodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_barcode_generation()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->get("/products/{$product->id}/barcode");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_barcode_scanning()
    {
        $product = Product::factory()->create(['barcode' => '1234567890']);

        $response = $this->actingAs($this->admin)->get('/barcode/scan/1234567890');

        $response->assertStatus(200);
        $response->assertJson(['product' => $product->toArray()]);
    }

    public function test_bulk_barcode_generation()
    {
        $products = Product::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->post('/barcodes/bulk', [
            'product_ids' => $products->pluck('id')->toArray()
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}