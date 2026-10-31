<?php

namespace App\Services;

use App\Libs\UserAgentParser;
use App\Models\StoreViewLog;
use App\Models\SuspiciousIp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SuspiciousIpDetector
{
    public function inspect(string $ip, ?string $userAgent = null, ?string $country = null): void
    {
        if ($ip === '' || $ip === '0.0.0.0') {
            return;
        }

        if (config('suspicious_ip.skip_google_ads_bots') && UserAgentParser::isTrustedCrawler($userAgent)) {
            return;
        }

        $result = $this->evaluate($ip, $userAgent);
        $threshold = $result['from_google_ads']
            ? (int) config('suspicious_ip.ads_score_threshold', 70)
            : (int) config('suspicious_ip.score_threshold', 50);

        if ($result['score'] < $threshold) {
            return;
        }

        $existing = SuspiciousIp::query()->where('ip', $ip)->first();
        $now = now();

        $payload = [
            'score' => $result['score'],
            'reasons' => $result['reasons'],
            'view_count_window' => $result['view_count_window'],
            'affiliate_count_window' => $result['affiliate_count_window'],
            'unique_store_count' => $result['unique_store_count'],
            'country' => $country,
            'last_user_agent' => $userAgent,
            'last_detected_at' => $now,
        ];

        if ($existing) {
            if ($existing->status !== SuspiciousIp::STATUS_IGNORED) {
                $payload['status'] = SuspiciousIp::STATUS_OPEN;
            }
            $existing->fill($payload)->save();
            return;
        }

        SuspiciousIp::create($payload + [
            'ip' => $ip,
            'status' => SuspiciousIp::STATUS_OPEN,
            'first_detected_at' => $now,
        ]);
    }

    public function evaluate(string $ip, ?string $userAgent = null): array
    {
        $now = now();
        $reasons = [];
        $score = 0;
        $fromGoogleAds = $this->hasGoogleAdsContext($ip, $now->copy()->subMinutes(30));
        $normalBrowser = $userAgent && !UserAgentParser::isAbnormalUserAgent($userAgent)
            && !UserAgentParser::isBlockedScraper($userAgent);

        $burstMinutes = (int) config('suspicious_ip.burst_minutes', 5);
        $burstSince = $now->copy()->subMinutes($burstMinutes);
        $viewsInBurst = $this->countEvents($ip, 'view', $burstSince);
        $uniqueStoresBurst = $this->countDistinctStores($ip, $burstSince);

        $hardBot = UserAgentParser::isBlockedScraper($userAgent)
            || ($userAgent && preg_match('/HeadlessChrome|PhantomJS|Selenium|Puppeteer|Playwright|jsdom/i', $userAgent));

        if ($viewsInBurst >= (int) config('suspicious_ip.burst_hits', 15)) {
            $extra = $hardBot
                || $uniqueStoresBurst >= (int) config('suspicious_ip.burst_unique_stores', 5)
                || $this->allRecentViewsLackReferer($ip, $burstSince);
            if ($extra && !($fromGoogleAds && $normalBrowser && !$hardBot)) {
                $reasons[] = 'burst_traffic';
                $score += 30;
            }
        }

        $affiliateMinutes = (int) config('suspicious_ip.affiliate_minutes', 5);
        $affiliateSince = $now->copy()->subMinutes($affiliateMinutes);
        $affiliateHits = $this->countEvents($ip, 'affiliate_click', $affiliateSince);
        $uniqueAffiliateUrls = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'affiliate_click')
            ->where('created_at', '>=', $affiliateSince)
            ->whereNotNull('target_url')
            ->where('target_url', '<>', '')
            ->distinct()
            ->count('target_url');

        $rapidClicks = $this->countRapidAffiliateClicks(
            $ip,
            $affiliateSince,
            (int) config('suspicious_ip.rapid_click_seconds', 2)
        );

        if ($rapidClicks >= (int) config('suspicious_ip.rapid_click_hits', 5)) {
            $reasons[] = 'rapid_clicks';
            $score += 40;
        } elseif (
            !$fromGoogleAds
            && ($affiliateHits >= (int) config('suspicious_ip.affiliate_hits', 10)
                || $uniqueAffiliateUrls >= (int) config('suspicious_ip.affiliate_unique_urls', 8))
        ) {
            $reasons[] = 'affiliate_spray';
            $score += 30;
        }

        $viewsForClicks = $this->countEvents($ip, 'view', $affiliateSince);
        if ($affiliateHits >= 3 && $viewsForClicks === 0) {
            $reasons[] = 'click_without_view';
            $score += 35;
        }

        if ($hardBot) {
            $reasons[] = 'bot_signature';
            $score += 35;
        }

        if (UserAgentParser::isAbnormalUserAgent($userAgent)) {
            $reasons[] = 'abnormal_ua';
            $score += 30;
        }

        $uaCount = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('created_at', '>=', $burstSince)
            ->whereNotNull('user_agent')
            ->distinct()
            ->count('user_agent');
        if ($uaCount >= (int) config('suspicious_ip.ua_rotation_count', 4) && !$fromGoogleAds) {
            $reasons[] = 'ua_rotation';
            $score += 15;
        }

        $idleMinutes = (int) config('suspicious_ip.no_interaction_minutes', 10);
        $idleSince = $now->copy()->subMinutes($idleMinutes);
        $idleViews = $this->countEvents($ip, 'view', $idleSince);
        $idleClicks = $this->countEvents($ip, 'affiliate_click', $idleSince);
        $uniqueStores = $this->countDistinctStores($ip, $idleSince);

        if (
            !$fromGoogleAds
            && $idleClicks === 0
            && $idleViews >= (int) config('suspicious_ip.no_interaction_views', 15)
            && $uniqueStores >= (int) config('suspicious_ip.no_interaction_unique_stores', 5)
        ) {
            $reasons[] = 'no_interaction';
            $score += 20;
        }

        $adsSince = $now->copy()->subMinutes((int) config('suspicious_ip.ads_repeat_minutes', 10));
        $adsViews = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'view')
            ->where('from_google_ads', true)
            ->where('created_at', '>=', $adsSince)
            ->count();
        if ($adsViews >= (int) config('suspicious_ip.ads_repeat_hits', 3)) {
            $reasons[] = 'ads_repeat';
            $score += 20;
        }

        $bounceSince = $now->copy()->subMinutes((int) config('suspicious_ip.short_bounce_minutes', 10));
        if ($this->countShortBounces($ip, $bounceSince) >= (int) config('suspicious_ip.short_bounce_hits', 3)) {
            $reasons[] = 'short_bounce';
            $score += 15;
        }

        $repeatSince = $now->copy()->subMinutes((int) config('suspicious_ip.repeat_same_url_minutes', 10));
        $maxSameStoreRow = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'view')
            ->where('created_at', '>=', $repeatSince)
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('store_id')
            ->orderByDesc('aggregate')
            ->first();
        $maxSameStore = (int) ($maxSameStoreRow?->aggregate ?? 0);
        if ($maxSameStore >= (int) config('suspicious_ip.repeat_same_url_hits', 4)) {
            $reasons[] = 'repeat_same_url';
            $score += 20;
        }

        $periodicSince = $now->copy()->subMinutes((int) config('suspicious_ip.periodic_minutes', 10));
        if ($this->isPeriodicAccess($ip, $periodicSince)) {
            $reasons[] = 'periodic_access';
            $score += 25;
        }

        $brandSince = $now->copy()->subMinutes((int) config('suspicious_ip.many_brands_minutes', 5));
        $brands = (int) StoreViewLog::query()
            ->where('ip', $ip)
            ->whereIn('event_type', ['view', 'affiliate_click'])
            ->where('created_at', '>=', $brandSince)
            ->selectRaw('COUNT(DISTINCT store_id) as aggregate')
            ->value('aggregate');
        if ($brands >= (int) config('suspicious_ip.many_brands_hits', 4)) {
            $reasons[] = 'many_brands';
            $score += 20;
        }

        return [
            'score' => min($score, 100),
            'reasons' => array_values(array_unique($reasons)),
            'view_count_window' => $viewsInBurst,
            'affiliate_count_window' => $affiliateHits,
            'unique_store_count' => $uniqueStores,
            'from_google_ads' => $fromGoogleAds,
        ];
    }

    private function hasGoogleAdsContext(string $ip, Carbon $since): bool
    {
        return StoreViewLog::query()
            ->where('ip', $ip)
            ->where('created_at', '>=', $since)
            ->where('from_google_ads', true)
            ->exists();
    }

    private function allRecentViewsLackReferer(string $ip, Carbon $since): bool
    {
        $views = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'view')
            ->where('created_at', '>=', $since)
            ->count();

        if ($views < 5) {
            return false;
        }

        $withReferer = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'view')
            ->where('created_at', '>=', $since)
            ->whereNotNull('referer')
            ->where('referer', '<>', '')
            ->count();

        return $withReferer === 0;
    }

    private function countRapidAffiliateClicks(string $ip, Carbon $since, int $maxGapSeconds): int
    {
        $times = $this->eventTimes($ip, 'affiliate_click', $since);

        return $this->countTightIntervals($times, $maxGapSeconds);
    }

    private function countTightIntervals(Collection $times, int $maxGapSeconds): int
    {
        $rapid = 0;
        $previous = null;
        foreach ($times as $time) {
            if ($previous instanceof Carbon && $time instanceof Carbon) {
                if ($previous->diffInSeconds($time) <= $maxGapSeconds) {
                    $rapid++;
                }
            }
            $previous = $time;
        }

        return $rapid;
    }

    private function countDistinctStores(string $ip, Carbon $since): int
    {
        return (int) StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'view')
            ->where('created_at', '>=', $since)
            ->selectRaw('COUNT(DISTINCT store_id) as aggregate')
            ->value('aggregate');
    }

    private function countShortBounces(string $ip, Carbon $since): int
    {
        $maxSeconds = (int) config('suspicious_ip.short_bounce_seconds', 5);
        $leaves = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'leave')
            ->where('created_at', '>=', $since)
            ->where('duration_seconds', '<=', $maxSeconds)
            ->get(['store_id', 'created_at']);

        if ($leaves->isEmpty()) {
            return 0;
        }

        $clicks = StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', 'affiliate_click')
            ->where('created_at', '>=', $since->copy()->subSeconds(5))
            ->get(['store_id', 'created_at']);

        $count = 0;
        foreach ($leaves as $leave) {
            $hadDealClick = $clicks->contains(function ($click) use ($leave) {
                if ((int) $click->store_id !== (int) $leave->store_id) {
                    return false;
                }
                if (!$click->created_at instanceof Carbon || !$leave->created_at instanceof Carbon) {
                    return false;
                }

                return $click->created_at->between(
                    $leave->created_at->copy()->subSeconds(5),
                    $leave->created_at
                );
            });
            if (!$hadDealClick) {
                $count++;
            }
        }

        return $count;
    }

    private function isPeriodicAccess(string $ip, Carbon $since): bool
    {
        $times = $this->eventTimes($ip, 'view', $since);

        if ($times->count() < (int) config('suspicious_ip.periodic_min_views', 6)) {
            return false;
        }

        if ($this->countDistinctStores($ip, $since) < 2) {
            return false;
        }

        $intervals = [];
        $previous = null;
        foreach ($times as $time) {
            if ($previous instanceof Carbon && $time instanceof Carbon) {
                $intervals[] = $previous->diffInSeconds($time);
            }
            $previous = $time;
        }

        if (count($intervals) < 5) {
            return false;
        }

        $mean = array_sum($intervals) / count($intervals);
        if ($mean < 2 || $mean > 45) {
            return false;
        }

        $variance = 0.0;
        foreach ($intervals as $interval) {
            $variance += ($interval - $mean) ** 2;
        }
        $stddev = sqrt($variance / count($intervals));

        return $stddev <= max(1.0, $mean * 0.15);
    }

    private function countEvents(string $ip, string $eventType, Carbon $since): int
    {
        return StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', $eventType)
            ->where('created_at', '>=', $since)
            ->count();
    }

    private function eventTimes(string $ip, string $eventType, Carbon $since): Collection
    {
        return StoreViewLog::query()
            ->where('ip', $ip)
            ->where('event_type', $eventType)
            ->where('created_at', '>=', $since)
            ->orderBy('created_at')
            ->pluck('created_at')
            ->map(fn ($time) => $this->asCarbon($time))
            ->filter();
    }

    private function asCarbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
