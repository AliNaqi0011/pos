<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quotation;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class QuotationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'seller']);
        $this->seller = User::factory()->create();
        $this->seller->assignRole('seller');
    }

    public function test_quotation_creation()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($this->seller)->post('/quotations', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ],
            'subtotal' => 200,
            'total' => 200,
            'quotation_date' => now()->format('Y-m-d')
        ]);

        $this->assertDatabaseHas('quotations', ['customer_id' => $customer->id]);
    }

    public function test_quotation_to_sale_conversion()
    {
        $quotation = Quotation::factory()->create();

        $response = $this->actingAs($this->seller)->post("/quotations/{$quotation->id}/convert");

        $this->assertDatabaseHas('sales', ['quotation_id' => $quotation->id]);
    }

    public function test_quotation_pdf_generation()
    {
        $quotation = Quotation::factory()->create();

        $response = $this->actingAs($this->seller)->get("/quotations/{$quotation->id}/pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_quotation_expiry_check()
    {
        $expiredQuotation = Quotation::factory()->create([
            'valid_until' => now()->subDay(),
            'status' => 'pending'
        ]);

        $response = $this->get('/check-quotations');

        $expiredQuotation->refresh();
        $this->assertEquals('expired', $expiredQuotation->status);
    }
}