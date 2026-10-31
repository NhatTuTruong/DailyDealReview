<?php

namespace Tests\Unit;

use App\Libs\UserAgentParser;
use App\Models\StoreViewLog;
use App\Models\SuspiciousIp;
use App\Services\SuspiciousIpDetector;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuspiciousIpDetectorTest extends TestCase
{
    private const CHROME = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    private const ADSBOT = 'Mozilla/5.0 (compatible; AdsBot-Google; +http://www.google.com/adsbot.html)';
    private const GOOGLEBOT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
    private const PREFIX = '203.0.113.';

    private SuspiciousIpDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureTables();
        $this->detector = app(SuspiciousIpDetector::class);
        $this->cleanup();
    }

    protected function tearDown(): void
    {
        $this->cleanup();
        parent::tearDown();
    }

    public function test_trusted_crawlers_are_not_scrapers(): void
    {
        $this->assertTrue(UserAgentParser::isTrustedCrawler(self::ADSBOT));
        $this->assertTrue(UserAgentParser::isTrustedCrawler(self::GOOGLEBOT));
        $this->assertTrue(UserAgentParser::isGoogleAdsBot(self::ADSBOT));
        $this->assertFalse(UserAgentParser::isBlockedScraper(self::ADSBOT));
        $this->assertFalse(UserAgentParser::isBlockedScraper(self::GOOGLEBOT));
        $this->assertFalse(UserAgentParser::isBlockedScraper(self::CHROME));
        $this->assertTrue(UserAgentParser::isBlockedScraper('curl/8.4.0'));
        $this->assertTrue(UserAgentParser::isAbnormalUserAgent(''));
        $this->assertTrue(UserAgentParser::isAbnormalUserAgent('curl/8.4.0'));
        $this->assertFalse(UserAgentParser::isAbnormalUserAgent(self::CHROME));
        $this->assertFalse(UserAgentParser::isAbnormalUserAgent(self::ADSBOT));
    }

    public function test_adsbot_inspect_is_skipped(): void
    {
        $ip = self::PREFIX . '10';
        $this->addLog($ip, ['user_agent' => self::ADSBOT]);
        $this->detector->inspect($ip, self::ADSBOT);
        $this->assertSame(0, SuspiciousIp::query()->where('ip', $ip)->count());
    }

    public function test_empty_ip_is_skipped(): void
    {
        $this->detector->inspect('', self::CHROME);
        $this->detector->inspect('0.0.0.0', self::CHROME);
        $this->assertSame(0, SuspiciousIp::query()->whereIn('ip', ['', '0.0.0.0'])->count());
    }

    public function test_chrome_with_few_views_scores_zero(): void
    {
        $ip = self::PREFIX . '11';
        $this->addLog($ip, ['referer' => 'https://google.com']);
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertSame(0, $result['score']);
        $this->assertSame([], $result['reasons']);
    }

    public function test_bot_signature_and_invalid_ua(): void
    {
        $ip = self::PREFIX . '12';
        $result = $this->detector->evaluate($ip, 'curl/8.4.0');
        $this->assertContains('bot_signature', $result['reasons']);
        $this->assertContains('abnormal_ua', $result['reasons']);
        $this->assertSame(65, $result['score']);
        $this->detector->inspect($ip, 'curl/8.4.0');
        $this->assertSame(1, SuspiciousIp::query()->where('ip', $ip)->count());
    }

    public function test_rapid_clicks(): void
    {
        $ip = self::PREFIX . '13';
        $this->addLog($ip, ['event_type' => 'view', 'referer' => 'https://example.com']);
        $start = now()->subMinutes(2);
        for ($i = 0; $i < 6; $i++) {
            $this->addLog($ip, [
                'event_type' => 'affiliate_click',
                'target_url' => 'https://aff.example/' . $i,
                'created_at' => $start->copy()->addSeconds($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('rapid_clicks', $result['reasons']);
        $this->assertGreaterThanOrEqual(40, $result['score']);
        $this->assertNotContains('click_without_view', $result['reasons']);
    }

    public function test_affiliate_spray_without_rapid_clicks(): void
    {
        $ip = self::PREFIX . '14';
        $this->addLog($ip, ['event_type' => 'view', 'referer' => 'https://example.com']);
        $start = now()->subMinutes(4);
        for ($i = 0; $i < 10; $i++) {
            $this->addLog($ip, [
                'event_type' => 'affiliate_click',
                'target_url' => 'https://aff.example/' . $i,
                'created_at' => $start->copy()->addSeconds($i * 20),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('affiliate_spray', $result['reasons']);
        $this->assertNotContains('rapid_clicks', $result['reasons']);
    }

    public function test_click_without_view(): void
    {
        $ip = self::PREFIX . '15';
        $start = now()->subMinutes(4);
        for ($i = 0; $i < 3; $i++) {
            $this->addLog($ip, [
                'event_type' => 'affiliate_click',
                'target_url' => 'https://aff.example/' . $i,
                'created_at' => $start->copy()->addMinutes($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('click_without_view', $result['reasons']);
        $this->assertNotContains('rapid_clicks', $result['reasons']);
        $this->assertSame(35, $result['score']);
    }

    public function test_ua_rotation(): void
    {
        $ip = self::PREFIX . '16';
        foreach (['Chrome/120', 'Firefox/121', 'Safari/17', 'Edg/120'] as $i => $ua) {
            $this->addLog($ip, [
                'user_agent' => 'Mozilla/5.0 TestBrowser ' . $ua . ' AppleWebKit/537.36',
                'referer' => 'https://example.com',
                'created_at' => now()->subMinutes(2)->addSeconds($i * 10),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('ua_rotation', $result['reasons']);
    }

    public function test_no_interaction_without_burst(): void
    {
        $ip = self::PREFIX . '17';
        for ($i = 0; $i < 15; $i++) {
            $this->addLog($ip, [
                'store_id' => ($i % 5) + 1,
                'referer' => 'https://example.com',
                'created_at' => now()->subMinutes(9)->addSeconds($i * 36),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('no_interaction', $result['reasons']);
        $this->assertNotContains('burst_traffic', $result['reasons']);
    }

    public function test_burst_with_many_stores(): void
    {
        $ip = self::PREFIX . '18';
        for ($i = 0; $i < 15; $i++) {
            $this->addLog($ip, [
                'store_id' => ($i % 5) + 1,
                'referer' => 'https://example.com',
                'created_at' => now()->subMinutes(2)->addSeconds($i * 5),
            ]);
        }
        $this->addLog($ip, [
            'event_type' => 'affiliate_click',
            'store_id' => 1,
            'target_url' => 'https://aff.example/1',
        ]);
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('burst_traffic', $result['reasons']);
        $this->assertContains('many_brands', $result['reasons']);
        $this->assertNotContains('no_interaction', $result['reasons']);
    }

    public function test_ads_repeat(): void
    {
        $ip = self::PREFIX . '19';
        for ($i = 0; $i < 3; $i++) {
            $this->addLog($ip, [
                'from_google_ads' => true,
                'gclid' => 'abc' . $i,
                'referer' => 'https://google.com/aclk',
                'store_id' => 1,
                'created_at' => now()->subMinutes(8)->addMinutes($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertTrue($result['from_google_ads']);
        $this->assertContains('ads_repeat', $result['reasons']);
        $this->assertGreaterThanOrEqual(20, $result['score']);
        $this->detector->inspect($ip, self::CHROME);
        $this->assertSame(0, SuspiciousIp::query()->where('ip', $ip)->count(), '3 ads visits alone must stay below ads threshold 55');
    }

    public function test_short_bounce_without_deal_click(): void
    {
        $ip = self::PREFIX . '20';
        $start = now()->subMinutes(5);
        for ($i = 0; $i < 3; $i++) {
            $this->addLog($ip, [
                'event_type' => 'leave',
                'store_id' => $i + 1,
                'duration_seconds' => 3,
                'created_at' => $start->copy()->addMinutes($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('short_bounce', $result['reasons']);
    }

    public function test_short_bounce_ignored_when_deal_clicked(): void
    {
        $ip = self::PREFIX . '21';
        $at = now()->subMinutes(2);
        $this->addLog($ip, [
            'event_type' => 'affiliate_click',
            'store_id' => 7,
            'target_url' => 'https://aff.example/x',
            'created_at' => $at,
        ]);
        $this->addLog($ip, [
            'event_type' => 'leave',
            'store_id' => 7,
            'duration_seconds' => 2,
            'created_at' => $at->copy()->addSeconds(2),
        ]);
        $this->addLog($ip, [
            'event_type' => 'leave',
            'store_id' => 8,
            'duration_seconds' => 2,
            'created_at' => $at->copy()->addMinutes(1),
        ]);
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertNotContains('short_bounce', $result['reasons']);
    }

    public function test_repeat_same_url(): void
    {
        $ip = self::PREFIX . '22';
        for ($i = 0; $i < 4; $i++) {
            $this->addLog($ip, [
                'store_id' => 9,
                'referer' => 'https://example.com',
                'created_at' => now()->subMinutes(6)->addMinutes($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('repeat_same_url', $result['reasons']);
        $this->assertNotContains('burst_traffic', $result['reasons']);
    }

    public function test_periodic_access(): void
    {
        $ip = self::PREFIX . '23';
        $start = now()->subMinutes(8);
        for ($i = 0; $i < 6; $i++) {
            $this->addLog($ip, [
                'store_id' => ($i % 3) + 1,
                'referer' => 'https://example.com',
                'created_at' => $start->copy()->addSeconds($i * 5),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('periodic_access', $result['reasons']);
        $this->assertNotContains('many_brands', $result['reasons']);
        $this->assertNotContains('repeat_same_url', $result['reasons']);
    }

    public function test_many_brands(): void
    {
        $ip = self::PREFIX . '24';
        for ($i = 1; $i <= 4; $i++) {
            $this->addLog($ip, [
                'store_id' => $i,
                'referer' => 'https://example.com',
                'created_at' => now()->subMinutes(2)->addSeconds($i * 8),
            ]);
            $this->addLog($ip, [
                'event_type' => 'affiliate_click',
                'store_id' => $i,
                'target_url' => 'https://aff.example/' . $i,
                'created_at' => now()->subMinutes(2)->addSeconds($i * 8 + 3),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('many_brands', $result['reasons']);
        $this->assertNotContains('rapid_clicks', $result['reasons']);
    }

    public function test_ads_visitor_not_flagged_for_f5_only(): void
    {
        $ip = self::PREFIX . '25';
        for ($i = 0; $i < 8; $i++) {
            $this->addLog($ip, [
                'from_google_ads' => true,
                'gclid' => 'same',
                'referer' => 'https://google.com/aclk',
                'store_id' => 1,
                'created_at' => now()->subMinutes(3)->addSeconds($i * 10),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertTrue($result['from_google_ads']);
        $this->assertNotContains('burst_traffic', $result['reasons']);
        $this->assertNotContains('no_interaction', $result['reasons']);
        $this->assertNotContains('periodic_access', $result['reasons']);
        $this->detector->inspect($ip, self::CHROME);
        $this->assertSame(0, SuspiciousIp::query()->where('ip', $ip)->count());
    }

    public function test_ads_fraud_combination_reaches_threshold(): void
    {
        $ip = self::PREFIX . '26';
        for ($i = 0; $i < 3; $i++) {
            $this->addLog($ip, [
                'from_google_ads' => true,
                'gclid' => 'g' . $i,
                'referer' => 'https://google.com/aclk',
                'store_id' => 1,
                'created_at' => now()->subMinutes(8)->addMinutes($i),
            ]);
        }
        for ($i = 0; $i < 3; $i++) {
            $this->addLog($ip, [
                'event_type' => 'leave',
                'store_id' => $i + 2,
                'duration_seconds' => 2,
                'from_google_ads' => true,
                'created_at' => now()->subMinutes(4)->addMinutes($i),
            ]);
        }
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('ads_repeat', $result['reasons']);
        $this->assertContains('short_bounce', $result['reasons']);
        $this->assertGreaterThanOrEqual(35, $result['score']);
        $this->addLog($ip, [
            'store_id' => 1,
            'from_google_ads' => true,
            'referer' => 'https://google.com/aclk',
            'created_at' => now()->subMinutes(1),
        ]);
        $result = $this->detector->evaluate($ip, self::CHROME);
        $this->assertContains('repeat_same_url', $result['reasons']);
        $this->assertGreaterThanOrEqual(55, $result['score']);
        $this->detector->inspect($ip, self::CHROME);
        $this->assertSame(1, SuspiciousIp::query()->where('ip', $ip)->count());
    }

    private function addLog(string $ip, array $over = []): void
    {
        $createdAt = $over['created_at'] ?? now();
        unset($over['created_at']);

        $row = StoreViewLog::create(array_merge([
            'store_id' => 1,
            'store_name' => 'TestStore',
            'event_type' => 'view',
            'ip' => $ip,
            'device' => 'Desktop',
            'browser' => 'Chrome',
            'os' => 'Windows',
            'user_agent' => self::CHROME,
            'from_google_ads' => false,
        ], $over));

        if ($createdAt instanceof Carbon) {
            StoreViewLog::query()->where('id', $row->id)->update([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    private function cleanup(): void
    {
        StoreViewLog::query()->where('ip', 'like', self::PREFIX . '%')->delete();
        SuspiciousIp::query()->where('ip', 'like', self::PREFIX . '%')->delete();
    }

    private function ensureTables(): void
    {
        if (!Schema::hasTable('store_view_logs')) {
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

        if (!Schema::hasTable('suspicious_ips')) {
            Schema::create('suspicious_ips', function (Blueprint $table) {
                $table->id();
                $table->string('ip', 45)->unique();
                $table->unsignedSmallInteger('score')->default(0);
                $table->json('reasons')->nullable();
                $table->unsignedInteger('view_count_window')->default(0);
                $table->unsignedInteger('affiliate_count_window')->default(0);
                $table->unsignedInteger('unique_store_count')->default(0);
                $table->string('country')->nullable();
                $table->text('last_user_agent')->nullable();
                $table->string('status', 20)->default('open');
                $table->timestamp('first_detected_at')->nullable();
                $table->timestamp('last_detected_at')->nullable();
                $table->timestamps();
            });
        }
    }
}
