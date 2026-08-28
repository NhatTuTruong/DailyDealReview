<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts')) {
            return;
        }

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->integer('cat_id')->default(0);
            $table->integer('store_id')->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('priority')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('source', 255)->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->boolean('is_hot')->nullable()->default(0);
            $table->integer('view_num')->nullable()->default(0);
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('language', 2)->nullable()->default('vn');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
