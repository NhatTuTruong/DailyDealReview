<?php

namespace App\Libs;

class UserAgentParser
{
    private const SEARCH_ENGINE_PATTERN = '/Googlebot|Google-InspectionTool|Googlebot-Image|Storebot-Google|Bingbot|bingbot|msnbot|adidxbot|BingPreview|DuckDuckBot|DuckAssistBot|Yahoo! Slurp|Slurp|YandexBot|YandexImages|Baiduspider|Applebot|Sogou|SeznamBot|Qwantify|Naverbot|Yeti/i';

    private const GOOGLE_ADS_BOT_PATTERN = '/AdsBot-Google|AdsBot-Google-Mobile|Mediapartners-Google|Google-Adwords|Google-Ads|GoogleAssociationService|APIs-Google|FeedFetcher-Google|Google-Read-Aloud|DuplexWeb-Google|Google-Safety|GoogleProducer|Google-Site-Verification|GoogleOther|Google Web Preview|Google-Adwords-Instant/i';

    private const SOCIAL_PREVIEW_PATTERN = '/facebookexternalhit|Facebot|Twitterbot|LinkedInBot|WhatsApp|TelegramBot|Slackbot|Discordbot|Pinterestbot|vkShare/i';

    private const SCRAPER_TOOL_PATTERN = '/curl|wget|python-requests|python-urllib|Scrapy|scrapy|libwww-perl|Go-http-client|aiohttp|HttpClient|httpie|PostmanRuntime|insomnia/i';

    private const GENERIC_BOT_PATTERN = '/bot|crawler|spider|slurp|scraper/i';

    public static function parse(?string $userAgent): array
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return [
                'device' => 'Unknown',
                'browser' => 'Unknown',
                'os' => 'Unknown',
            ];
        }

        return [
            'device' => self::detectDevice($ua),
            'browser' => self::detectBrowser($ua),
            'os' => self::detectOs($ua),
        ];
    }

    public static function isBot(?string $userAgent): bool
    {
        $ua = (string) $userAgent;
        if ($ua === '') {
            return false;
        }

        return self::isTrustedCrawler($ua)
            || (bool) preg_match(self::SCRAPER_TOOL_PATTERN, $ua)
            || (bool) preg_match(self::GENERIC_BOT_PATTERN, $ua);
    }

    public static function isSearchEngineBot(?string $userAgent): bool
    {
        return (bool) preg_match(self::SEARCH_ENGINE_PATTERN, (string) $userAgent);
    }

    public static function isGoogleAdsBot(?string $userAgent): bool
    {
        return (bool) preg_match(self::GOOGLE_ADS_BOT_PATTERN, (string) $userAgent);
    }

    public static function isTrustedCrawler(?string $userAgent): bool
    {
        return self::isSearchEngineBot($userAgent)
            || self::isGoogleAdsBot($userAgent)
            || self::isSocialPreviewBot($userAgent);
    }

    public static function isSocialPreviewBot(?string $userAgent): bool
    {
        return (bool) preg_match(self::SOCIAL_PREVIEW_PATTERN, (string) $userAgent);
    }

    public static function isBlockedScraper(?string $userAgent): bool
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return false;
        }

        if (self::isTrustedCrawler($ua)) {
            return false;
        }

        return (bool) preg_match(self::SCRAPER_TOOL_PATTERN, $ua)
            || (bool) preg_match(self::GENERIC_BOT_PATTERN, $ua);
    }

    public static function isAbnormalUserAgent(?string $userAgent): bool
    {
        $ua = trim((string) $userAgent);
        if ($ua === '') {
            return true;
        }

        if (self::isTrustedCrawler($ua)) {
            return false;
        }

        if (strlen($ua) < 20) {
            return true;
        }

        if (preg_match('/HeadlessChrome|PhantomJS|Selenium|Puppeteer|Playwright|jsdom/i', $ua)) {
            return true;
        }

        if (self::isBlockedScraper($ua)) {
            return true;
        }

        $looksLikeBrowser = (bool) preg_match('/Mozilla|Chrome|Safari|Firefox|Edg|OPR|Opera|AppleWebKit/i', $ua);
        if (!$looksLikeBrowser) {
            return true;
        }

        $parsed = self::parse($ua);

        return $parsed['browser'] === 'Unknown' && $parsed['os'] === 'Unknown';
    }

    private static function detectDevice(string $ua): string
    {
        if (self::isBot($ua)) {
            return 'Bot';
        }

        if (preg_match('/iPad|Tablet|PlayBook|Silk/i', $ua)
            || (preg_match('/Android/i', $ua) && !preg_match('/Mobile/i', $ua))) {
            return 'Tablet';
        }

        if (preg_match('/Mobile|iPhone|iPod|Android|webOS|BlackBerry|Opera Mini|IEMobile|Windows Phone/i', $ua)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private static function detectBrowser(string $ua): string
    {
        $botName = self::detectBotName($ua);
        if ($botName) {
            return $botName;
        }

        $browsers = [
            'Edg' => 'Edge',
            'OPR' => 'Opera',
            'Opera' => 'Opera',
            'SamsungBrowser' => 'Samsung Internet',
            'UCBrowser' => 'UC Browser',
            'CriOS' => 'Chrome',
            'FxiOS' => 'Firefox',
            'Chrome' => 'Chrome',
            'Firefox' => 'Firefox',
            'MSIE' => 'Internet Explorer',
            'Trident' => 'Internet Explorer',
            'Safari' => 'Safari',
        ];

        foreach ($browsers as $needle => $label) {
            if (stripos($ua, $needle) !== false) {
                return $label;
            }
        }

        return 'Unknown';
    }

    private static function detectBotName(string $ua): ?string
    {
        $bots = [
            'Googlebot' => 'Googlebot',
            'AdsBot-Google' => 'AdsBot-Google',
            'Mediapartners-Google' => 'Mediapartners-Google',
            'Google-Adwords' => 'Google Ads',
            'Google-InspectionTool' => 'Google Inspection',
            'Bingbot' => 'Bingbot',
            'bingbot' => 'Bingbot',
            'DuckDuckBot' => 'DuckDuckBot',
            'YandexBot' => 'YandexBot',
            'Baiduspider' => 'Baiduspider',
            'Applebot' => 'Applebot',
            'Slurp' => 'Yahoo Slurp',
            'facebookexternalhit' => 'Facebook',
            'Twitterbot' => 'Twitterbot',
            'curl' => 'curl',
            'wget' => 'wget',
            'python-requests' => 'python-requests',
            'Scrapy' => 'Scrapy',
        ];

        foreach ($bots as $needle => $label) {
            if (stripos($ua, $needle) !== false) {
                return $label;
            }
        }

        if (self::isBot($ua)) {
            return 'Bot';
        }

        return null;
    }

    private static function detectOs(string $ua): string
    {
        if (preg_match('/Android/i', $ua)) {
            return 'Android';
        }
        if (preg_match('/iPhone|iPad|iPod/i', $ua)) {
            return 'iOS';
        }
        if (preg_match('/Windows NT/i', $ua)) {
            return 'Windows';
        }
        if (preg_match('/Mac OS X|Macintosh/i', $ua)) {
            return 'macOS';
        }
        if (preg_match('/Linux/i', $ua)) {
            return 'Linux';
        }
        if (preg_match('/CrOS/i', $ua)) {
            return 'Chrome OS';
        }

        return 'Unknown';
    }
}
