<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('provinces')) {
            return;
        }

        Schema::create('provinces', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('name');
            $table->string('name_en', 255)->nullable();
            $table->string('full_name', 255);
            $table->string('full_name_en', 255)->nullable();
            $table->string('code_name', 255)->nullable();
            $table->integer('unit_id')->nullable();
            $table->integer('region_id')->nullable();
            $table->index('region_id', 'idx_provinces_region');
            $table->index('unit_id', 'idx_provinces_unit');
            $table->foreign('region_id', 'provinces_administrative_region_id_fkey')->references('id')->on('regions');
            $table->foreign('unit_id', 'provinces_administrative_unit_id_fkey')->references('id')->on('units');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
