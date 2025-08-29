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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->unsignedBigInteger('warehouse_id');
        $table->unsignedBigInteger('expense_category_id');
        $table->decimal('amount', 15, 2);
        $table->string('reference_code')->unique();
        $table->string('title');
        $table->text('details')->nullable();
        $table->timestamps();

        $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
