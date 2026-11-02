<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suspicious_ips', function (Blueprint $table) {
            $table->json('store_names')->nullable()->after('unique_store_count');
        });
    }

    public function down(): void
    {
        Schema::table('suspicious_ips', function (Blueprint $table) {
            $table->dropColumn('store_names');
        });
    }
};
