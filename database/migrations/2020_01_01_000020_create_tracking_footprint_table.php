<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tracking_footprint')) {
            return;
        }

        Schema::create('tracking_footprint', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ukey', 255);
            $table->string('request', 500)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('ip', 255)->default('');
            $table->string('country', 100)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('user_agent', 500)->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_footprint');
    }
};
