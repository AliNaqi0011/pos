<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('purchase_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('purchase_id');
        $table->unsignedBigInteger('product_id');

        $table->decimal('product_cost', 12, 2);
        $table->decimal('net_unit_cost', 12, 2);

        $table->string('tax_type')->nullable(); // e.g., percentage or fixed
        $table->decimal('tax_value', 8, 2)->default(0);
        $table->decimal('tax_amount', 12, 2)->default(0);

        $table->string('discount_type')->nullable(); // e.g., percentage or fixed
        $table->decimal('discount_value', 8, 2)->default(0);
        $table->decimal('discount_amount', 12, 2)->default(0);

        $table->string('purchase_unit')->nullable();
        $table->integer('quantity');
        $table->decimal('sub_total', 12, 2);

        $table->timestamps();

        // Foreign Keys
        $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
