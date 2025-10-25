<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customer Groups
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('payment_terms_days')->default(0);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
        });

        // Loyalty Programs
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('points_per_currency', 8, 2)->default(1);
            $table->decimal('currency_per_point', 8, 4)->default(0.01);
            $table->integer('min_points_redeem')->default(100);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
        });

        // Customer Loyalty Points
        Schema::create('customer_loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('loyalty_program_id');
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->integer('points_balance')->default(0);
            $table->date('last_activity');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('loyalty_program_id')->references('id')->on('loyalty_programs');
        });

        // Point Transactions
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'adjusted']);
            $table->integer('points');
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id');
            $table->text('description');
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        // Gift Cards
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->decimal('initial_value', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'used', 'expired', 'cancelled']);
            $table->unsignedBigInteger('issued_by');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        // Promotions
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->enum('type', ['percentage', 'fixed_amount', 'buy_x_get_y', 'free_shipping']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_purchase_amount', 10, 2)->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
        });

        // Customer Communications
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('type', ['email', 'sms', 'whatsapp', 'call', 'meeting']);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed']);
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_communications');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('customer_loyalty_points');
        Schema::dropIfExists('loyalty_programs');
        Schema::dropIfExists('customer_groups');
    }
};