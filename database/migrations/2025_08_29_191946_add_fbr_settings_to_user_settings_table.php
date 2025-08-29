<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->boolean('fbr_enabled')->default(false);
            $table->string('fbr_pos_id')->nullable();
            $table->string('fbr_username')->nullable();
            $table->string('fbr_password')->nullable();
            $table->string('fbr_api_url')->default('https://esp.fbr.gov.pk');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn([
                'fbr_enabled',
                'fbr_pos_id',
                'fbr_username',
                'fbr_password',
                'fbr_api_url'
            ]);
        });
    }
};