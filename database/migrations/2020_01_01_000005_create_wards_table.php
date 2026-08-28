<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wards')) {
            return;
        }

        Schema::create('wards', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('name');
            $table->string('name_en', 255)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->string('full_name_en', 255)->nullable();
            $table->string('code_name', 255)->nullable();
            $table->string('district_code', 20)->nullable();
            $table->integer('administrative_unit_id')->nullable();
            $table->index('district_code', 'idx_wards_district');
            $table->index('administrative_unit_id', 'idx_wards_unit');
            $table->foreign('administrative_unit_id', 'wards_administrative_unit_id_fkey')->references('id')->on('units');
            $table->foreign('district_code', 'wards_district_code_fkey')->references('code')->on('districts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
