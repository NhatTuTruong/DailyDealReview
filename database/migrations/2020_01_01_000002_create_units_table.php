<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('units')) {
            return;
        }

        Schema::create('units', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('full_name', 255)->nullable();
            $table->string('full_name_en', 255)->nullable();
            $table->string('short_name', 255)->nullable();
            $table->string('short_name_en', 255)->nullable();
            $table->string('code_name', 255)->nullable();
            $table->string('code_name_en', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
