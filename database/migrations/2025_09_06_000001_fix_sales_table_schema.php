<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add missing columns to sales table
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'total')) {
                    $table->decimal('total', 15, 2)->default(0)->after('final_total');
                }
            });
        }

        // Fix subscriptions table
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'status')) {
                    $table->string('status')->default('active')->after('ends_at');
                }
                if (!Schema::hasColumn('subscriptions', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('status');
                }
            });
        }

        // Create missing tables
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_warehouse_id')->constrained('warehouses');
                $table->foreignId('to_warehouse_id')->constrained('warehouses');
                $table->foreignId('product_id')->constrained('products');
                $table->integer('quantity');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warehouse_products')) {
            Schema::create('warehouse_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained('warehouses');
                $table->foreignId('product_id')->constrained('products');
                $table->integer('quantity')->default(0);
                $table->timestamps();
                
                $table->unique(['warehouse_id', 'product_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_products');
        Schema::dropIfExists('stock_transfers');
        
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn(['status', 'expires_at']);
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('total');
            });
        }
    }
};