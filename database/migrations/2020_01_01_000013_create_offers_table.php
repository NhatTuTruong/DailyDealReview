<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offers')) {
            return;
        }

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 255)->nullable();
            $table->string('offer', 255)->nullable();
            $table->string('url', 500)->nullable();
            $table->integer('store_id')->default(0);
            $table->boolean('status')->nullable()->default(0);
            $table->integer('user_id')->default(0);
            $table->boolean('verified')->nullable()->default(0);
            $table->integer('priority')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('language', 2)->nullable()->default('vn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
