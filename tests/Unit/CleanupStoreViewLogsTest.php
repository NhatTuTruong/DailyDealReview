<?php

namespace Tests\Unit;

use App\Models\StoreViewLog;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CleanupStoreViewLogsTest extends TestCase
{
    private const IP = '203.0.113.77';

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureTables();
        StoreViewLog::query()->where('ip', self::IP)->delete();
        Carbon::setTestNow(Carbon::parse('2026-11-02 10:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        StoreViewLog::query()->where('ip', self::IP)->delete();
        parent::tearDown();
    }

    public function test_cleanup_is_skipped_when_disabled(): void
    {
        config(['store_view_log.cleanup_enabled' => false]);
        $this->addLog(now()->subDays(10));

        $this->artisan('store-views:cleanup')
            ->expectsOutput('Đã tắt dọn lượt xem (STORE_VIEW_LOG_CLEANUP_ENABLED=false).')
            ->assertSuccessful();

        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->count());
    }

    public function test_cleanup_keeps_yesterday_and_today(): void
    {
        config([
            'store_view_log.cleanup_enabled' => true,
            'store_view_log.cleanup_chunk' => 2,
        ]);

        $old = $this->addLog(now()->subDays(8));
        $yesterday = $this->addLog(now()->subDay()->setTime(15, 30));
        $today = $this->addLog(now());

        $this->artisan('store-views:cleanup')->assertSuccessful();

        $ids = StoreViewLog::query()->where('ip', self::IP)->orderBy('id')->pluck('id')->all();
        $this->assertSame([$yesterday, $today], $ids);
        $this->assertFalse(in_array($old, $ids, true));
    }

    private function addLog(Carbon $createdAt): int
    {
        $row = StoreViewLog::create([
            'store_id' => 1,
            'store_name' => 'CleanupStore',
            'event_type' => StoreViewLog::TYPE_VIEW,
            'ip' => self::IP,
            'device' => 'Desktop',
            'user_agent' => 'Mozilla/5.0 Test',
        ]);

        StoreViewLog::query()->where('id', $row->id)->update([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return (int) $row->id;
    }

    private function ensureTables(): void
    {
        if (Schema::hasTable('store_view_logs')) {
            return;
        }

        Schema::create('store_view_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('store_name')->nullable();
            $table->string('event_type', 30)->default('view')->index();
            $table->string('ip', 45)->index();
            $table->string('device', 50)->nullable();
            $table->string('browser', 80)->nullable();
            $table->string('os', 80)->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 8)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('target_url', 500)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('gclid', 128)->nullable();
            $table->boolean('from_google_ads')->default(false);
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();
        });
    }
}
