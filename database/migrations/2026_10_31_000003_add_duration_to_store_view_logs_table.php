<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->unsignedInteger('duration_seconds')->nullable()->after('from_google_ads');
        });
    }

    public function down(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });
    }
};
