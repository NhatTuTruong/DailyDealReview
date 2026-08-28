<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('category_post_rel')) {
            return;
        }

        Schema::create('category_post_rel', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->default(0);
            $table->integer('post_id')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post_rel');
    }
};
