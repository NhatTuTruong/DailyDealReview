<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_view_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('store_name')->nullable();
            $table->string('ip', 45)->index();
            $table->string('device', 50)->nullable()->index();
            $table->string('browser', 80)->nullable();
            $table->string('os', 80)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 8)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_view_logs');
    }
};
