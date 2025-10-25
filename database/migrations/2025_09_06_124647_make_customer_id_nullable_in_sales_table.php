<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['customer_id']);
            
            // Make customer_id nullable
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            
            // Re-add foreign key constraint with nullable
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['customer_id']);
            
            // Make customer_id not nullable
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            
            // Re-add foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};