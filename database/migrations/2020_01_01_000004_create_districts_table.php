<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('districts')) {
            return;
        }

        Schema::create('districts', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('name');
            $table->string('name_en', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('full_name_en', 255)->nullable();
            $table->string('code_name', 255)->nullable();
            $table->string('province_code', 20)->nullable();
            $table->integer('administrative_unit_id')->nullable();
            $table->index('province_code', 'idx_districts_province');
            $table->index('administrative_unit_id', 'idx_districts_unit');
            $table->foreign('administrative_unit_id', 'districts_administrative_unit_id_fkey')->references('id')->on('units');
            $table->foreign('province_code', 'districts_province_code_fkey')->references('code')->on('provinces');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
