<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menus')) {
            return;
        }

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('custom_link', 255)->nullable()->default('0');
            $table->string('image', 255)->nullable();
            $table->integer('page_id')->nullable()->default(0);
            $table->integer('cat_id')->nullable()->default(0);
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('priority')->nullable()->default(0);
            $table->boolean('status')->nullable()->default(0);
            $table->string('type', 30)->nullable()->default('0');
            $table->boolean('is_mega')->nullable()->default(0);
            $table->string('language', 2)->nullable()->default('vn');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
