<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes for better performance
        // Skip index creation in testing environment
        if (app()->environment('testing')) {
            return;
        }
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->index(['tenant_id', 'created_at']);
            }
            if (Schema::hasColumn('users', 'created_by')) {
                $table->index(['created_by']);
            }
            $table->index(['email']);
        });

        if (Schema::hasColumn('products', 'tenant_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['category_id', 'brand_id']);
                $table->index(['warehouse_id', 'quantity']);
                $table->index(['created_at', 'tenant_id']);
            });
        }

        if (Schema::hasColumn('sales', 'tenant_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->index(['customer_id', 'sale_date']);
                $table->index(['user_id', 'status']);
                $table->index(['sale_date', 'tenant_id']);
                $table->index(['payment_status', 'final_total']);
            });
        }

        if (!app()->environment('testing')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->index(['product_id', 'sale_id']);
                $table->index(['sale_id', 'quantity']);
            });
        }

        if (Schema::hasColumn('customers', 'tenant_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->index(['email', 'tenant_id']);
                $table->index(['phone', 'tenant_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'created_at']);
            $table->dropIndex(['created_by', 'tenant_id']);
            $table->dropIndex(['email', 'tenant_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'brand_id']);
            $table->dropIndex(['warehouse_id', 'quantity']);
            $table->dropIndex(['created_at', 'tenant_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'sale_date']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['sale_date', 'tenant_id']);
            $table->dropIndex(['payment_status', 'final_total']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'sale_id']);
            $table->dropIndex(['sale_id', 'quantity']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['email', 'tenant_id']);
            $table->dropIndex(['phone', 'tenant_id']);
        });
    }
};