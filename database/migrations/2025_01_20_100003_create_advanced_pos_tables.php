<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // POS Terminals
        Schema::create('pos_terminals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->unsignedBigInteger('warehouse_id');
            $table->json('settings');
            $table->boolean('is_active')->default(true);
            $table->datetime('last_sync')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });

        // Hold Orders (Layaway)
        Schema::create('hold_orders', function (Blueprint $table) {
            $table->id();
            $table->string('hold_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2);
            $table->date('hold_date');
            $table->date('expiry_date');
            $table->enum('status', ['active', 'completed', 'expired', 'cancelled']);
            $table->json('items');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        // Split Payments
        Schema::create('split_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable();
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed']);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('sale_id')->references('id')->on('sales');
        });

        // Price Overrides
        Schema::create('price_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_item_id');
            $table->decimal('original_price', 10, 2);
            $table->decimal('override_price', 10, 2);
            $table->text('reason');
            $table->unsignedBigInteger('authorized_by');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('sale_item_id')->references('id')->on('sale_items');
            $table->foreign('authorized_by')->references('id')->on('users');
        });

        // Kitchen Orders (for restaurants)
        Schema::create('kitchen_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->string('order_number');
            $table->json('items');
            $table->enum('status', ['pending', 'preparing', 'ready', 'served']);
            $table->datetime('ordered_at');
            $table->datetime('prepared_at')->nullable();
            $table->datetime('served_at')->nullable();
            $table->text('special_instructions')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('sale_id')->references('id')->on('sales');
        });

        // Table Management (for restaurants)
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->integer('capacity');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance']);
            $table->unsignedBigInteger('current_sale_id')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('current_sale_id')->references('id')->on('sales');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });

        // Reservations
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('table_id');
            $table->datetime('reservation_time');
            $table->integer('party_size');
            $table->enum('status', ['confirmed', 'seated', 'completed', 'cancelled', 'no_show']);
            $table->text('special_requests')->nullable();
            $table->string('contact_phone');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('table_id')->references('id')->on('tables');
        });

        // Receipt Templates
        Schema::create('receipt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('header_content');
            $table->text('footer_content');
            $table->json('settings');
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_templates');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('kitchen_orders');
        Schema::dropIfExists('price_overrides');
        Schema::dropIfExists('split_payments');
        Schema::dropIfExists('hold_orders');
        Schema::dropIfExists('pos_terminals');
    }
};