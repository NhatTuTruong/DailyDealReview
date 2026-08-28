<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('regions')) {
            return;
        }

        Schema::create('regions', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->string('name_en', 255);
            $table->string('code_name', 255)->nullable();
            $table->string('code_name_en', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
