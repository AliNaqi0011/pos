<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard.view',

            // User
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Brand
            'brand.view',
            'brand.create',
            'brand.update',
            'brand.delete',

            // Product
            'product.view',
            'product.create',
            'product.update',
            'product.delete',

            // Category
            'category.view',
            'category.create',
            'category.update',
            'category.delete',

            // Warehouse
            'warehouse.view',
            'warehouse.create',
            'warehouse.update',
            'warehouse.delete',

            // Customer
            'customer.view',
            'customer.create',
            'customer.update',
            'customer.delete',

            // Sale
            'sale.view',
            'sale.create',
            'sale.update',
            'sale.delete',

            // Blog
            'blog.view',
            'blog.create',
            'blog.update',
            'blog.delete',

            // Barcode
            'barcode.view',
            'barcode.create',
            'barcode.update',
            'barcode.delete',

            // Documentation (optional)
            'documentation.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $seller = Role::firstOrCreate(['name' => 'seller']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $sales = Role::firstOrCreate(['name' => 'sales']);

        // Super Admin gets all permissions
        $superAdmin->syncPermissions($permissions);
        
        // Admin gets most permissions (cannot manage super admin users)
        $admin->syncPermissions($permissions);

        // Manager permissions (cannot delete)
        $manager->syncPermissions([
            'dashboard.view',

            'user.view',
            'user.create',
            'user.update',

            'brand.view',
            'brand.create',
            'brand.update',

            'product.view',
            'product.create',
            'product.update',

            'category.view',
            'category.create',
            'category.update',

            'warehouse.view',
            'warehouse.create',
            'warehouse.update',

            'customer.view',
            'customer.create',
            'customer.update',

            'sale.view',
            'sale.create',
            'sale.update',

            'blog.view',
            'blog.create',
            'blog.update',

            'barcode.view',
            'barcode.create',
            'barcode.update',

            'documentation.view',
        ]);

        // Seller permissions (POS and sales focused)
        $seller->syncPermissions([
            'dashboard.view',
            'product.view',
            'customer.view',
            'customer.create',
            'customer.update',
            'sale.view',
            'sale.create',
            'sale.update',
            'barcode.view',
        ]);
        
        // Sales permissions (mostly sales-related only)
        $sales->syncPermissions([
            'dashboard.view',
            'sale.view',
            'sale.create',
            'sale.update',
        ]);
    }
}
