<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('widgets')) {
            return;
        }

        Schema::create('widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image', 255)->nullable();
            $table->string('link', 255)->nullable();
            $table->string('position', 255);
            $table->integer('priority')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->string('language', 2)->nullable()->default('vn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};
