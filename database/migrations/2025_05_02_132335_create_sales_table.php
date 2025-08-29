<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
        
            // 🔑 Correct foreign key definition
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
            $table->integer('total_items')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_charges', 15, 2)->default(0);
            $table->decimal('final_total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final'])->default('final');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->dateTime('sale_date');
            $table->text('notes')->nullable();
        
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
