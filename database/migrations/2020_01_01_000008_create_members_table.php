<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('members')) {
            return;
        }

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_name', 255);
            $table->string('pos_name', 255);
            $table->string('pos_name_en', 255);
            $table->string('province_code', 255);
            $table->string('district_code', 255)->nullable();
            $table->string('address', 255);
            $table->string('email');
            $table->string('phone', 255);
            $table->string('avatar', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->integer('parent_id')->nullable()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
