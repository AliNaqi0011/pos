<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\POSSetting;

class POSSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['key' => 'store_name', 'value' => 'My Store', 'type' => 'text'],
            ['key' => 'store_address', 'value' => '123 Main Street, City, State 12345', 'type' => 'textarea'],
            ['key' => 'store_phone', 'value' => '+1 (555) 123-4567', 'type' => 'text'],
            ['key' => 'store_email', 'value' => 'info@mystore.com', 'type' => 'email'],
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'number'],
            ['key' => 'currency', 'value' => '$', 'type' => 'text'],
            ['key' => 'receipt_footer', 'value' => 'Thank you for your business!', 'type' => 'textarea'],
            ['key' => 'auto_print_receipt', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'show_barcode_scanner', 'value' => '1', 'type' => 'boolean'],
            ['key' => 'default_customer', 'value' => 'Walk-in Customer', 'type' => 'text'],
            ['key' => 'low_stock_alert', 'value' => '10', 'type' => 'number'],
        ];

        foreach ($settings as $setting) {
            POSSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}