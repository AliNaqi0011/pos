<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComprehensiveControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $seller;
    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'seller']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'sales']);

        // Create permissions
        Permission::create(['name' => 'manage_products']);
        Permission::create(['name' => 'manage_sales']);
        Permission::create(['name' => 'manage_customers']);
        Permission::create(['name' => 'view_reports']);

        // Create users
        $this->superAdmin = User::factory()->create(['email' => 'superadmin@test.com']);
        $this->superAdmin->assignRole('super_admin');

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->seller = User::factory()->create(['email' => 'seller@test.com']);
        $this->seller->assignRole('seller');
    }

    /** @test */
    public function test_pos_controller_index()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $warehouse = Warehouse::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'warehouse_id' => $warehouse->id
        ]);
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->seller)->get('/pos-system');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.pos.infy-pos');
        $response->assertViewHas(['categories', 'brands', 'warehouses', 'products', 'customers']);
    }

    /** @test */
    public function test_pos_checkout_functionality()
    {
        $product = Product::factory()->create([
            'sale_price' => 100,
            'quantity' => 10
        ]);
        $customer = Customer::factory()->create();

        $response = $this->actingAs($this->seller)->post('/pos/checkout', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 100
                ]
            ],
            'total_amount' => 200,
            'payment_method' => 'cash'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('sales', [
            'total_amount' => 200
        ]);
        
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Check stock reduction - refresh from database
        $product->refresh();
        $this->assertEquals(8, $product->quantity, 'Product stock should be reduced from 10 to 8 after selling 2 items');
    }

    /** @test */
    public function test_product_controller_crud()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $warehouse = Warehouse::factory()->create();

        // Test index
        $response = $this->actingAs($this->admin)->get('/products');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/products/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/products/store', [
            'name' => 'Test Product',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'warehouse_id' => $warehouse->id,
            'cost_price' => 50,
            'sale_price' => 100,
            'quantity' => 10,
            'description' => 'Test description'
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);

        // Test edit
        $product = Product::where('name', 'Test Product')->first();
        $response = $this->actingAs($this->admin)->get("/products/edit/{$product->id}");
        $response->assertStatus(200);

        // Test update
        $response = $this->actingAs($this->admin)->post('/products/update', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'warehouse_id' => $warehouse->id,
            'price' => 150,
            'cost_price' => 60,
            'sale_price' => 150,
            'quantity' => 15,
            'description' => 'Updated description'
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);

        // Test delete
        $response = $this->actingAs($this->admin)->delete("/products/delete/{$product->id}");
        $response->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function test_sale_controller_crud()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['quantity' => 10]);

        // Test index
        $response = $this->actingAs($this->admin)->get('/admin/sales');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/admin/sales/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/admin/sales/store', [
            'customer_id' => $customer->id,
            'user_id' => $this->admin->id,
            'total_amount' => 200,
            'final_total' => 200,
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'total' => 200
                ]
            ]
        ]);

        $response->assertRedirect('/admin/sales');
        $this->assertDatabaseHas('sales', ['customer_id' => $customer->id]);
    }

    /** @test */
    public function test_customer_controller_crud()
    {
        // Test index
        $response = $this->actingAs($this->admin)->get('/customers');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/customers/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/customers/store', [
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address'
        ]);

        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['name' => 'Test Customer']);
    }

    /** @test */
    public function test_category_controller_crud()
    {
        // Test index
        $response = $this->actingAs($this->admin)->get('/categories');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/categories/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/categories/store', [
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
    }

    /** @test */
    public function test_brand_controller_crud()
    {
        // Test index
        $response = $this->actingAs($this->admin)->get('/brands');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/brands/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/brands/store', [
            'name' => 'Test Brand',
            'description' => 'Test Description'
        ]);

        $response->assertRedirect('/brands');
        $this->assertDatabaseHas('brands', ['name' => 'Test Brand']);
    }

    /** @test */
    public function test_warehouse_controller_crud()
    {
        // Test index
        $response = $this->actingAs($this->admin)->get('/warehouses');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/warehouses/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/warehouses', [
            'name' => 'Test Warehouse',
            'location' => 'Test Location',
            'description' => 'Test Description'
        ]);

        $response->assertRedirect('/warehouses');
        $this->assertDatabaseHas('warehouses', ['name' => 'Test Warehouse']);
    }

    /** @test */
    public function test_expense_controller_crud()
    {
        $category = ExpenseCategory::factory()->create();

        // Test index
        $response = $this->actingAs($this->admin)->get('/expenses');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/expenses/create');
        $response->assertStatus(200);

        // Test store
        $response = $this->actingAs($this->admin)->post('/expenses/store', [
            'title' => 'Test Expense',
            'amount' => 100,
            'expense_category_id' => $category->id,
            'date' => now()->format('Y-m-d'),
            'description' => 'Test Description'
        ]);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', ['title' => 'Test Expense']);
    }

    /** @test */
    public function test_purchase_controller_crud()
    {
        $product = Product::factory()->create();

        // Test index
        $response = $this->actingAs($this->admin)->get('/admin/purchases');
        $response->assertStatus(200);

        // Test create
        $response = $this->actingAs($this->admin)->get('/admin/purchases/create');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_dashboard_access()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_reports_access()
    {
        $response = $this->actingAs($this->admin)->get('/reports');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_user_permissions()
    {
        // Test seller cannot access admin functions
        $response = $this->actingAs($this->seller)->get('/admin/users');
        $response->assertStatus(403);

        // Test admin can access seller functions
        $response = $this->actingAs($this->admin)->get('/pos-system');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_data_isolation()
    {
        // Create products for different users
        $product1 = Product::factory()->create(['created_by' => $this->admin->id]);
        $product2 = Product::factory()->create(['created_by' => $this->seller->id]);

        // Admin should only see their products
        $response = $this->actingAs($this->admin)->get('/products');
        $response->assertStatus(200);
        // Add assertions for data isolation
    }

    /** @test */
    public function test_validation_rules()
    {
        // Test product validation
        $response = $this->actingAs($this->admin)->post('/products/store', [
            'name' => '',
            'category_id' => 999,
            'quantity' => -1
        ]);

        $response->assertSessionHasErrors(['name', 'category_id', 'quantity']);

        // Test customer validation
        $response = $this->actingAs($this->admin)->post('/customers/store', [
            'name' => '',
            'email' => 'invalid-email'
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    /** @test */
    public function test_stock_management()
    {
        $product = Product::factory()->create(['quantity' => 5]);
        $customer = Customer::factory()->create();

        // Test insufficient stock
        $response = $this->actingAs($this->seller)->post('/pos/checkout', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'price' => 100
                ]
            ],
            'total_amount' => 1000
        ]);

        $response->assertStatus(400); // Should fail due to insufficient stock
    }
}