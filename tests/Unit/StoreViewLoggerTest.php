<?php

namespace Tests\Unit;

use App\Models\StoreViewLog;
use App\Services\StoreViewLogger;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StoreViewLoggerTest extends TestCase
{
    private const IP = '203.0.113.80';
    private const CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    private StoreViewLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureTables();
        Cache::flush();
        $this->logger = app(StoreViewLogger::class);
        StoreViewLog::query()->where('ip', self::IP)->delete();
    }

    protected function tearDown(): void
    {
        StoreViewLog::query()->where('ip', self::IP)->delete();
        parent::tearDown();
    }

    public function test_event_type_labels_are_distinct(): void
    {
        $view = new StoreViewLog(['event_type' => StoreViewLog::TYPE_VIEW]);
        $click = new StoreViewLog(['event_type' => StoreViewLog::TYPE_AFFILIATE_CLICK]);
        $leave = new StoreViewLog(['event_type' => StoreViewLog::TYPE_LEAVE]);

        $this->assertSame('Xem store', $view->eventTypeLabel());
        $this->assertSame('Click deal', $click->eventTypeLabel());
        $this->assertSame('Rời trang', $leave->eventTypeLabel());
        $this->assertNotSame($view->eventTypeLabel(), $leave->eventTypeLabel());
    }

    public function test_leave_is_not_recorded(): void
    {
        $this->logger->record($this->payload('view'));
        $this->logger->record($this->payload('leave', ['duration_seconds' => 2]));

        $rows = StoreViewLog::query()->where('ip', self::IP)->get();
        $this->assertCount(1, $rows);
        $this->assertSame('view', $rows[0]->event_type);
    }

    public function test_duplicate_view_within_three_seconds_is_ignored(): void
    {
        $this->logger->record($this->payload('view'));
        $this->logger->record($this->payload('view'));

        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->where('event_type', 'view')->count());
    }

    public function test_offer_popup_after_view_is_not_a_second_view(): void
    {
        $this->logger->record($this->payload('view'));
        Cache::flush();
        $this->logger->record($this->payload('view', ['is_offer_popup' => true]));

        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->where('event_type', 'view')->count());
    }

    public function test_offer_popup_from_same_store_referer_is_not_a_view(): void
    {
        $this->logger->record($this->payload('view', [
            'is_offer_popup' => true,
            'referer' => 'https://example.test/store/nhat',
            'store_path' => '/store/nhat',
        ]));

        $this->assertSame(0, StoreViewLog::query()->where('ip', self::IP)->count());
    }

    public function test_first_offer_landing_from_google_is_still_a_view(): void
    {
        $this->logger->record($this->payload('view', [
            'is_offer_popup' => true,
            'from_google_ads' => true,
            'referer' => 'https://google.com/aclk',
            'store_path' => '/store/nhat',
        ]));

        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->where('event_type', 'view')->count());
    }

    public function test_prefetch_view_is_not_logged(): void
    {
        $request = Request::create('/store/test10', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => self::CHROME,
            'REMOTE_ADDR' => self::IP,
            'HTTP_SEC_PURPOSE' => 'prefetch',
        ]);

        $store = new \App\Models\Store();
        $store->id = 80;
        $store->name = 'Test10';
        $store->exists = true;

        $this->logger->queue($store, $request, true);

        $this->assertSame(0, StoreViewLog::query()->where('ip', self::IP)->count());
    }

    public function test_googlebot_is_logged_as_bot_device(): void
    {
        $this->logger->record($this->payload('view', [
            'user_agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ]));

        $row = StoreViewLog::query()->where('ip', self::IP)->first();
        $this->assertNotNull($row);
        $this->assertSame('Bot', $row->device);
        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->visitor(StoreViewLog::VISITOR_BOT)->count());
        $this->assertSame(0, StoreViewLog::query()->where('ip', self::IP)->visitor(StoreViewLog::VISITOR_HUMAN)->count());
    }

    public function test_visitor_scope_separates_humans_and_bots(): void
    {
        StoreViewLog::create([
            'store_id' => 80,
            'store_name' => 'Test10',
            'event_type' => 'view',
            'ip' => self::IP,
            'device' => 'Desktop',
            'user_agent' => self::CHROME,
        ]);
        StoreViewLog::create([
            'store_id' => 80,
            'store_name' => 'Test10',
            'event_type' => 'view',
            'ip' => self::IP,
            'device' => 'Bot',
            'user_agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ]);
        StoreViewLog::create([
            'store_id' => 80,
            'store_name' => 'Test10',
            'event_type' => 'view',
            'ip' => self::IP,
            'device' => null,
            'user_agent' => '',
        ]);

        $this->assertSame(2, StoreViewLog::query()->where('ip', self::IP)->visitor(StoreViewLog::VISITOR_HUMAN)->count());
        $this->assertSame(1, StoreViewLog::query()->where('ip', self::IP)->visitor(StoreViewLog::VISITOR_BOT)->count());
        $this->assertSame(3, StoreViewLog::query()->where('ip', self::IP)->visitor(StoreViewLog::VISITOR_ALL)->count());
        $this->assertSame(2, StoreViewLog::query()->where('ip', self::IP)->visitor('unknown')->count());
    }

    public function test_leave_queue_does_not_write(): void
    {
        $this->logger->record($this->payload('leave', ['duration_seconds' => 1]));

        $this->assertSame(0, StoreViewLog::query()->where('ip', self::IP)->count());
    }

    private function payload(string $eventType, array $over = []): array
    {
        return array_merge([
            'store_id' => 80,
            'store_name' => 'Test10',
            'ip' => self::IP,
            'user_agent' => self::CHROME,
            'event_type' => $eventType,
            'from_google_ads' => false,
        ], $over);
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
