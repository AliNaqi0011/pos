<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = ['products', 'categories', 'brands', 'warehouses', 'customers', 'sales', 'purchases'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable();
                        $table->foreign('created_by')->references('id')->on('users');
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = ['products', 'categories', 'brands', 'warehouses', 'customers', 'sales', 'purchases'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->dropForeign(['created_by']);
                        $table->dropColumn('created_by');
                    }
                });
            }
        }
    }
};