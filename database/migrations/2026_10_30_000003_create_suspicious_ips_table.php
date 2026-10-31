<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suspicious_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->unique();
            $table->unsignedSmallInteger('score')->default(0);
            $table->json('reasons')->nullable();
            $table->unsignedInteger('view_count_window')->default(0);
            $table->unsignedInteger('affiliate_count_window')->default(0);
            $table->unsignedInteger('unique_store_count')->default(0);
            $table->string('country')->nullable();
            $table->text('last_user_agent')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamp('first_detected_at')->nullable();
            $table->timestamp('last_detected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suspicious_ips');
    }
};
