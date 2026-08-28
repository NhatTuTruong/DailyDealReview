<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stores')) {
            return;
        }

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name', 500);
            $table->string('slug', 500);
            $table->integer('cat_id')->default(0);
            $table->integer('event_id')->default(0);
            $table->string('image', 255)->nullable();
            $table->integer('priority')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->text('about_store')->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->boolean('allow_search')->nullable()->default(1);
            $table->text('how_to_apply')->nullable();
            $table->text('faqs')->nullable();
            $table->integer('user_id')->nullable()->default(0);
            $table->integer('ads_user_id')->nullable()->default(0);
            $table->string('ads_email', 255)->nullable();
            $table->string('ads_status', 255)->nullable();
            $table->integer('view_num')->nullable()->default(0);
            $table->string('af_net', 255)->nullable();
            $table->string('af_website', 255)->nullable();
            $table->enum('af_flag', ['not_registered', 'pending', 'approved', 'requesting_code', 'completed', 'rejected', 'store_is_dead', 'ads_running', 'no_paypal', 'low_traffic', 'cannot_run_ads'])->nullable();
            $table->integer('af_id')->nullable()->default(0);
            $table->integer('af_visit')->nullable()->default(0);
            $table->string('currency', 5)->nullable();
            $table->string('af_portal', 255)->nullable();
            $table->string('af_account', 255)->nullable();
            $table->string('max_offer', 255)->nullable();
            $table->integer('cookie_duration')->nullable()->default(0);
            $table->string('commission_type', 30)->nullable()->default('percentage');
            $table->integer('commission_amount')->nullable()->default(0);
            $table->string('commission_on', 30)->nullable()->default('order');
            $table->string('note', 1000)->nullable();
            $table->text('meta_data')->nullable();
            $table->string('language', 2)->nullable()->default('vn');
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
