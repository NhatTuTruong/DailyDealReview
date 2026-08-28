<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('slugs')) {
            return;
        }

        Schema::create('slugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable();
            $table->string('module', 255);
            $table->integer('module_id')->default(0);
            $table->timestamps();
            $table->unique('slug', 'slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slugs');
    }
};
