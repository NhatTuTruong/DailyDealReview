<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->boolean('is_super_admin')->nullable()->default(0);
            $table->boolean('status')->nullable()->default(0);
            $table->integer('group_id')->nullable()->default(0);
            $table->timestamps();
            $table->unique('email', 'users_email_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
