<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price', 10, 2)->default(0); // Final price used
            $table->decimal('cost_price', 10, 2)->nullable(); // Optional for internal use
            $table->decimal('sale_price', 10, 2)->nullable(); // Optional for offers
            $table->decimal('discount_price', 10, 2)->nullable(); // Optional

            // Inventory
            $table->integer('quantity')->default(0);
            $table->integer('stock_alert_level')->default(10);

            // Barcode
            $table->string('barcode')->nullable()->unique();

            // Image
            $table->string('image')->nullable(); // Stored path

            // Foreign keys
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
