<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pages')) {
            return;
        }

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('image', 255)->nullable();
            $table->integer('priority')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('language', 2)->nullable()->default('vn');
            $table->timestamps();
            $table->unique('slug', 'category_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
