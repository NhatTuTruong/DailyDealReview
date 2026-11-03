<?php

namespace App\Services;

use App\Libs\UserAgentParser;
use App\Libs\Util;
use App\Models\Store;
use App\Models\StoreViewLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class StoreViewLogger
{
    public function queue(
        Store $store,
        Request $request,
        bool $immediate = false,
        string $eventType = 'view',
        ?string $targetUrl = null,
        ?int $durationSeconds = null
    ): void {
        if ($eventType === 'leave') {
            return;
        }

        if ($this->shouldSkipIp($request->ip() ?: '')) {
            return;
        }

        if ($eventType === 'view' && $this->isPrefetch($request)) {
            return;
        }

        $referer = substr((string) $request->headers->get('referer', ''), 0, 500);
        $gclid = $this->extractGoogleClickId($request);
        $fromGoogleAds = $this->isGoogleAdsRequest($request, $referer, $gclid);
        $isOfferPopup = $eventType === 'view'
            && ($request->filled('offer') || $request->filled('coupon'));
        $storePath = (string) (parse_url($store->getUrl(), PHP_URL_PATH) ?: '');

        $payload = [
            'store_id' => $store->id,
            'store_name' => $store->name,
            'ip' => $request->ip() ?: '0.0.0.0',
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'event_type' => $eventType,
            'target_url' => $targetUrl ? substr($targetUrl, 0, 500) : null,
            'referer' => $referer !== '' ? $referer : null,
            'gclid' => $gclid,
            'from_google_ads' => $fromGoogleAds,
            'duration_seconds' => $durationSeconds,
            'is_offer_popup' => $isOfferPopup,
            'store_path' => $storePath,
        ];

        if ($immediate) {
            $this->record($payload);
            return;
        }

        dispatch(function () use ($payload) {
            app(StoreViewLogger::class)->record($payload);
        })->afterResponse();
    }

    public function record(array $payload): void
    {
        $ip = $payload['ip'];
        $userAgent = $payload['user_agent'] ?? '';
        $eventType = $payload['event_type'] ?? 'view';
        $fromGoogleAds = (bool) ($payload['from_google_ads'] ?? false);

        if ($this->shouldSkipIp($ip) || $eventType === 'leave') {
            return;
        }

        $storeId = (int) $payload['store_id'];

        if ($eventType === 'view' && $this->hasRecentEvent($storeId, $ip, 'view', 3)) {
            return;
        }

        if (
            $eventType === 'view'
            && !empty($payload['is_offer_popup'])
            && (
                $this->refererIsSameStore($payload['referer'] ?? null, $payload['store_path'] ?? null)
                || $this->hasRecentEvent($storeId, $ip, 'view', 1800)
            )
        ) {
            return;
        }

        if ($eventType === 'view' && !$this->acquireViewLock($storeId, $ip)) {
            return;
        }

        if (!$fromGoogleAds) {
            $fromGoogleAds = $this->ipHasRecentGoogleAds($ip);
        }

        $country = ['country' => null, 'country_code' => null];

        try {
            $parsed = UserAgentParser::parse($userAgent);
            $country = $this->resolveCountry($ip);

            StoreViewLog::create([
                'store_id' => $payload['store_id'],
                'store_name' => $payload['store_name'] ?? null,
                'event_type' => $eventType,
                'ip' => $ip,
                'device' => $parsed['device'],
                'browser' => $parsed['browser'],
                'os' => $parsed['os'],
                'country' => $country['country'] ?? null,
                'country_code' => $country['country_code'] ?? null,
                'user_agent' => $userAgent ?: null,
                'target_url' => $payload['target_url'] ?? null,
                'referer' => $payload['referer'] ?? null,
                'gclid' => $payload['gclid'] ?? null,
                'from_google_ads' => $fromGoogleAds,
                'duration_seconds' => $payload['duration_seconds'] ?? null,
            ]);
        } catch (Throwable) {
            return;
        }

        try {
            app(SuspiciousIpDetector::class)->inspect(
                $ip,
                $userAgent ?: null,
                $country['country'] ?? null
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function shouldSkipIp(?string $ip): bool
    {
        $ip = trim((string) $ip);
        if ($ip === '') {
            return false;
        }

        return in_array($ip, config('store_view_log.skip_ips', []), true);
    }

    private function isPrefetch(Request $request): bool
    {
        $purpose = strtolower(trim(implode(' ', array_filter([
            (string) $request->headers->get('Sec-Purpose', ''),
            (string) $request->headers->get('Purpose', ''),
            (string) $request->headers->get('X-Moz', ''),
        ]))));

        return str_contains($purpose, 'prefetch') || str_contains($purpose, 'prerender');
    }

    private function acquireViewLock(int $storeId, string $ip): bool
    {
        if ($storeId < 1 || $ip === '' || $ip === '0.0.0.0') {
            return true;
        }

        return Cache::add('store_view_lock:' . $ip . ':' . $storeId, 1, 3);
    }

    private function refererIsSameStore(?string $referer, ?string $storePath): bool
    {
        if (!$referer || !$storePath) {
            return false;
        }

        $refererPath = rtrim((string) (parse_url($referer, PHP_URL_PATH) ?: ''), '/');
        $storePath = rtrim($storePath, '/');

        return $storePath !== '' && ($refererPath === $storePath || str_ends_with($refererPath, $storePath));
    }

    private function hasRecentEvent(int $storeId, string $ip, string $eventType, int $withinSeconds): bool
    {
        if ($storeId < 1 || $ip === '' || $ip === '0.0.0.0') {
            return false;
        }

        return StoreViewLog::query()
            ->where('store_id', $storeId)
            ->where('ip', $ip)
            ->where('event_type', $eventType)
            ->where('created_at', '>=', now()->subSeconds($withinSeconds))
            ->exists();
    }

    private function extractGoogleClickId(Request $request): ?string
    {
        foreach (['gclid', 'gbraid', 'wbraid', 'gad_campaignid'] as $key) {
            $value = trim((string) $request->query($key, ''));
            if ($value !== '') {
                return substr($value, 0, 128);
            }
        }

        return null;
    }

    private function isGoogleAdsRequest(Request $request, string $referer, ?string $gclid): bool
    {
        if ($gclid) {
            return true;
        }

        if ($request->query('gad_source') || $request->query('gad_campaignid')) {
            return true;
        }

        return $referer !== '' && (bool) preg_match(
            '/googleads|google\.com\/aclk|doubleclick\.net|googlesyndication|googleadservices/i',
            $referer
        );
    }

    private function ipHasRecentGoogleAds(string $ip): bool
    {
        return StoreViewLog::query()
            ->where('ip', $ip)
            ->where('from_google_ads', true)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->exists();
    }

    private function resolveCountry(string $ip): array
    {
        if ($this->isPrivateIp($ip)) {
            return [
                'country' => 'Local',
                'country_code' => 'LO',
            ];
        }

        $cached = Cache::remember('ipinfo:' . $ip, 86400, function () use ($ip) {
            return Util::getCountryFromIp($ip) ?: ['country' => null, 'country_code' => null];
        });

        return is_array($cached) ? $cached : ['country' => null, 'country_code' => null];
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
