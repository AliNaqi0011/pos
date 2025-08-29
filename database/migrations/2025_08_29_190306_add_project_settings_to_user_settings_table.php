<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('app_name')->nullable();
            $table->string('app_logo')->nullable();
            $table->string('app_favicon')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i:s');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'app_name',
                'app_logo', 
                'app_favicon',
                'company_name',
                'company_address',
                'company_phone',
                'company_email',
                'company_website',
                'tax_number',
                'currency',
                'timezone',
                'date_format',
                'time_format'
            ]);
        });
    }
};