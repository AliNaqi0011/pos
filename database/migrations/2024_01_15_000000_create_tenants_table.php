<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('database')->unique();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('subscription_plan_id')->references('id')->on('plans')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};