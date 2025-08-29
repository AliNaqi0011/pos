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
    Schema::create('purchases', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->unsignedBigInteger('warehouse_id');
        $table->decimal('tax_rate', 8, 2)->default(0);
        $table->decimal('tax_amount', 12, 2)->default(0);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('shipping', 12, 2)->default(0);
        $table->decimal('grand_total', 12, 2);
        $table->decimal('received_amount', 12, 2)->default(0);
        $table->decimal('paid_amount', 12, 2)->default(0);
        $table->string('payment_type')->nullable(); // cash, card, etc.
        $table->string('status')->default('received'); // pending, ordered, received
        $table->string('payment_status')->default('unpaid'); // unpaid, paid, partial
        $table->text('notes')->nullable();
        $table->string('reference_code')->nullable();
        $table->timestamps();

        // Foreign keys
        $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
