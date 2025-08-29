<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Customer;

class POSDataSeeder extends Seeder
{
    public function run()
    {
        // Create Categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic items'],
            ['name' => 'Clothing', 'description' => 'Clothing items'],
            ['name' => 'Food & Beverages', 'description' => 'Food and drink items'],
            ['name' => 'Books', 'description' => 'Books and magazines'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create Brands
        $brands = [
            ['name' => 'Samsung'],
            ['name' => 'Nike'],
            ['name' => 'Coca Cola'],
            ['name' => 'Generic'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand['name']], $brand);
        }

        // Create Warehouses
        $warehouses = [
            ['name' => 'Main Warehouse', 'location' => 'Main Street'],
            ['name' => 'Secondary Warehouse', 'location' => 'Second Street'],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate(['name' => $warehouse['name']], $warehouse);
        }

        // Get created IDs
        $electronicsCategory = Category::where('name', 'Electronics')->first();
        $clothingCategory = Category::where('name', 'Clothing')->first();
        $foodCategory = Category::where('name', 'Food & Beverages')->first();
        $booksCategory = Category::where('name', 'Books')->first();
        
        $samsungBrand = Brand::where('name', 'Samsung')->first();
        $nikeBrand = Brand::where('name', 'Nike')->first();
        $cocaColaBrand = Brand::where('name', 'Coca Cola')->first();
        $genericBrand = Brand::where('name', 'Generic')->first();
        
        $mainWarehouse = Warehouse::where('name', 'Main Warehouse')->first();

        // Create Products
        $products = [
            [
                'name' => 'Samsung Galaxy Phone',
                'description' => 'Latest Samsung smartphone',
                'price' => 699.99,
                'sale_price' => 699.99,
                'cost_price' => 500.00,
                'quantity' => 50,
                'stock_alert_level' => 10,
                'category_id' => $electronicsCategory->id,
                'brand_id' => $samsungBrand->id,
                'warehouse_id' => $mainWarehouse->id,
                'barcode' => 'PROD-000001',
            ],
            [
                'name' => 'Nike Running Shoes',
                'description' => 'Comfortable running shoes',
                'price' => 129.99,
                'sale_price' => 129.99,
                'cost_price' => 80.00,
                'quantity' => 30,
                'stock_alert_level' => 5,
                'category_id' => $clothingCategory->id,
                'brand_id' => $nikeBrand->id,
                'warehouse_id' => $mainWarehouse->id,
                'barcode' => 'PROD-000002',
            ],
            [
                'name' => 'Coca Cola 500ml',
                'description' => 'Refreshing cola drink',
                'price' => 2.99,
                'sale_price' => 2.99,
                'cost_price' => 1.50,
                'quantity' => 100,
                'stock_alert_level' => 20,
                'category_id' => $foodCategory->id,
                'brand_id' => $cocaColaBrand->id,
                'warehouse_id' => $mainWarehouse->id,
                'barcode' => 'PROD-000003',
            ],
            [
                'name' => 'Programming Book',
                'description' => 'Learn programming basics',
                'price' => 49.99,
                'sale_price' => 49.99,
                'cost_price' => 30.00,
                'quantity' => 25,
                'stock_alert_level' => 5,
                'category_id' => $booksCategory->id,
                'brand_id' => $genericBrand->id,
                'warehouse_id' => $mainWarehouse->id,
                'barcode' => 'PROD-000004',
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['barcode' => $product['barcode']], $product);
        }

        // Create Customers
        $customers = [
            [
                'name' => 'Walk-in Customer',
                'email' => 'walkin@customer.com',
                'phone' => '0000000000',
                'address' => 'N/A',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '0987654321',
                'address' => '456 Oak Ave',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(['email' => $customer['email']], $customer);
        }
    }
}