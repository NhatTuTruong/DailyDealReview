<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->string('referer', 500)->nullable()->after('target_url');
            $table->string('gclid', 128)->nullable()->after('referer');
            $table->boolean('from_google_ads')->default(false)->after('gclid')->index();
        });
    }

    public function down(): void
    {
        Schema::table('store_view_logs', function (Blueprint $table) {
            $table->dropColumn(['referer', 'gclid', 'from_google_ads']);
        });
    }
};
