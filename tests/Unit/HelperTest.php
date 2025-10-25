<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_currency_helper_function()
    {
        $formatted = currency(1234.56);
        $this->assertEquals('PKR 1,234.56', $formatted);
    }

    public function test_percentage_calculation()
    {
        $percentage = calculatePercentage(25, 100);
        $this->assertEquals(25, $percentage);
    }

    public function test_tax_calculation()
    {
        $tax = calculateTax(1000, 17); // 17% tax
        $this->assertEquals(170, $tax);
    }

    public function test_discount_calculation()
    {
        $discount = calculateDiscount(1000, 10); // 10% discount
        $this->assertEquals(100, $discount);
    }

    public function test_date_formatting()
    {
        $date = formatDate('2024-01-15');
        $this->assertEquals('15 Jan 2024', $date);
    }

    public function test_number_formatting()
    {
        $formatted = formatNumber(1234567.89);
        $this->assertEquals('1,234,567.89', $formatted);
    }

    public function test_slug_generation()
    {
        $slug = generateSlug('Test Product Name');
        $this->assertEquals('test-product-name', $slug);
    }

    public function test_barcode_validation()
    {
        $this->assertTrue(isValidBarcode('1234567890123'));
        $this->assertFalse(isValidBarcode('invalid'));
    }
}