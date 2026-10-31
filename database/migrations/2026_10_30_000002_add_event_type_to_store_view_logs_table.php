<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->string('event_type', 30)->default('view')->after('store_name')->index();
            $table->string('target_url', 500)->nullable()->after('user_agent');
            $table->index(['ip', 'created_at']);
            $table->index(['ip', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->dropIndex(['ip', 'created_at']);
            $table->dropIndex(['ip', 'event_type', 'created_at']);
            $table->dropColumn(['event_type', 'target_url']);
        });
    }
};
