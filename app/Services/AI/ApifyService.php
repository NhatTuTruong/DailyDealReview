<?php

namespace App\Services\AI;

use App\Libs\ImageWebpConverter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApifyService
{
    private MultiKeyManager $keyManager;

    /** @var array<string, string> normalized URL => local path */
    private array $downloadCache = [];

    /** @var array<string, string> content hash => local path */
    private array $contentHashCache = [];

    private const ACTOR_ID = 'bvAQMqCbp6wE53JzK';
    private const ACTOR_BASE_URL = 'https://api.apify.com/v2/acts/' . self::ACTOR_ID . '/run-sync-get-dataset-items';
    private const UPLOAD_DIR = 'uploads/images/blog';
    private const MIN_IMAGE_WIDTH = 600;
    private const MIN_IMAGE_HEIGHT = 400;

    public function __construct()
    {
        $this->keyManager = new MultiKeyManager('apify_api_keys');
    }

    /**
     * Tìm ảnh trên Google bằng Apify actor, download về server, trả mảng URL local.
     *
     * @param string $query        Từ khóa tìm kiếm (vd: tên store + từ khóa)
     * @param int    $contentCount Số ảnh chèn vào nội dung (2-3)
     * @return array{featured: ?string, content: array}  URL local của ảnh đại diện + danh sách ảnh chèn content
     */
    public function fetchAndStoreImages(string $query, int $contentCount = 3): array
    {
        if (!$this->keyManager->hasKeys()) {
            throw new \Exception('Không có Apify API key nào được cấu hình.');
        }

        $contentCount = max(2, min(3, $contentCount));
        $query = $this->normalizeImageQuery($query);
        $this->downloadCache = [];
        $this->contentHashCache = [];
        $images = [];

        $error = $this->keyManager->call(function (string $apiKey) use ($query, &$images) {
            $payload = [
                'queries' => [$query],
                'maxResultsPerQuery' => 10,
                'gl' => 'us',
                'hl' => 'en',
            ];

            $response = Http::timeout(180)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(self::ACTOR_BASE_URL . '?token=' . urlencode($apiKey), $payload);

            if ($response->failed()) {
                throw new \Exception("Apify API error (HTTP {$response->status()}): " . substr($response->body(), 0, 200));
            }

            $items = $response->json();
            if (!is_array($items) || empty($items)) {
                throw new \Exception("Apify không trả về dữ liệu cho query: {$query}");
            }

            $images = $this->extractImages($items);
            if (empty($images)) {
                throw new \Exception("Không tìm được ảnh nào cho query: {$query}");
            }
        });

        if ($error !== null) {
            throw $error;
        }

        $featured = $this->pickBestImage($images);
        $contentImages = $this->pickContentImages($images, $featured, $contentCount);

        $localFeatured = $this->downloadToServer($featured['url'] ?? null, 'featured');
        $localContent = [];
        $usedPaths = [];
        if ($localFeatured !== '') {
            $usedPaths[$localFeatured] = true;
        }
        foreach ($contentImages as $i => $image) {
            $path = $this->downloadToServer($image['url'] ?? null, 'content-' . ($i + 1));
            if ($path !== '' && !isset($usedPaths[$path])) {
                $localContent[] = $path;
                $usedPaths[$path] = true;
            }
        }

        return [
            'featured' => $localFeatured,
            'content'  => $localContent,
        ];
    }

    /**
     * Trích metadata ảnh từ response actor johnvc/google-images-api.
     *
     * @return array<int, array{url: string, width: int, height: int, score: int}>
     */
    private function extractImages(array $items): array
    {
        $images = [];
        $seen = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $url = $item['imageUrl'] ?? $item['url'] ?? null;
            if (empty($url)) {
                continue;
            }

            if (!$this->isDirectImageUrl($url)) {
                continue;
            }

            if (str_ends_with(strtolower(parse_url($url, PHP_URL_PATH) ?? ''), '.gif')) {
                continue;
            }

            $normalizedUrl = $this->normalizeImageUrl($url);
            if (isset($seen[$normalizedUrl])) {
                continue;
            }
            $seen[$normalizedUrl] = true;

            $width = (int)($item['imageWidth'] ?? $item['width'] ?? 0);
            $height = (int)($item['imageHeight'] ?? $item['height'] ?? 0);

            if ($width > 0 && $width < self::MIN_IMAGE_WIDTH) {
                continue;
            }
            if ($height > 0 && $height < self::MIN_IMAGE_HEIGHT) {
                continue;
            }

            $images[] = [
                'url' => $url,
                'width' => $width,
                'height' => $height,
                'score' => $this->scoreImage($url, $width, $height),
            ];
        }

        usort($images, fn($a, $b) => $b['score'] <=> $a['score']);

        return $images;
    }

    /**
     * Lọc ra URL ảnh trực tiếp (không phải crawler/redirect của FB, IG, encrypted google thumb).
     */
    private function isDirectImageUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) return false;

        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (str_contains($host, 'lookaside.fbsbx.com')) return false;        // FB proxy
        if (str_contains($host, 'lookaside.instagram.com')) return false;   // IG proxy
        if (str_contains($host, 'encrypted-tbn')) return false;             // Google thumb proxy
        if (str_contains($host, 'gstatic.com')) return false;                // Google static proxy
        if (str_contains($host, 'google.com/imgres')) return false;          // Google redirect

        // Bỏ URL chứa query string quá phức tạp (thường là tracking)
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        if (!str_contains($path, '.')) return false; // Không có extension

        return true;
    }

    /**
     * Chọn ảnh đại diện chất lượng cao nhất.
     */
    private function pickBestImage(array $images): ?array
    {
        return $images[0] ?? null;
    }

    /**
     * Chọn 2-3 ảnh content tiếp theo, tránh trùng domain/URL với ảnh đại diện.
     *
     * @return array<int, array{url: string, width: int, height: int, score: int}>
     */
    private function pickContentImages(array $images, ?array $featured, int $count): array
    {
        if (empty($images)) {
            return [];
        }

        $featuredKey = !empty($featured['url']) ? $this->normalizeImageUrl($featured['url']) : null;
        $selected = [];
        $usedKeys = [];

        foreach ($images as $image) {
            $url = $image['url'] ?? null;
            if (empty($url)) {
                continue;
            }

            $urlKey = $this->normalizeImageUrl($url);
            if ($featuredKey !== null && $urlKey === $featuredKey) {
                continue;
            }
            if (isset($usedKeys[$urlKey])) {
                continue;
            }

            $selected[] = $image;
            $usedKeys[$urlKey] = true;

            if (count($selected) >= $count) {
                break;
            }
        }

        return $selected;
    }

    private function scoreImage(string $url, int $width, int $height): int
    {
        $score = 0;
        $pixels = $width > 0 && $height > 0 ? $width * $height : 0;

        if ($pixels >= 1920 * 1080) {
            $score += 20;
        } elseif ($pixels >= 1280 * 720) {
            $score += 15;
        } elseif ($pixels >= 800 * 600) {
            $score += 10;
        } elseif ($pixels > 0) {
            $score += 5;
        }

        $path = strtolower(parse_url($url, PHP_URL_PATH) ?? '');
        if (str_ends_with($path, '.jpg') || str_ends_with($path, '.jpeg')) {
            $score += 3;
        } elseif (str_ends_with($path, '.webp')) {
            $score += 2;
        } elseif (str_ends_with($path, '.png')) {
            $score += 2;
        }

        $query = [];
        parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
        $queryWidth = (int)($query['width'] ?? 0);
        if ($queryWidth >= 1200) {
            $score += 4;
        } elseif ($queryWidth >= 800) {
            $score += 2;
        }

        return $score;
    }

    /**
     * Download ảnh từ URL về server, lưu vào public/uploads/images/blog/.
     * Mỗi URL/nội dung ảnh chỉ lưu 1 lần trong cùng một request.
     */
    private function downloadToServer(?string $url, string $prefix): string
    {
        if (empty($url)) {
            return '';
        }

        $cacheKey = $this->normalizeImageUrl($url);
        if (isset($this->downloadCache[$cacheKey])) {
            return $this->downloadCache[$cacheKey];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept'     => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])
                ->get($url);

            if ($response->failed() || empty($response->body())) {
                Log::warning("Apify download failed ({$prefix}): HTTP {$response->status()} - {$url}");
                return '';
            }

            $body = $response->body();
            if (strlen($body) > 8 * 1024 * 1024) {
                Log::warning("Apify download skipped ({$prefix}): image too large - {$url}");
                return '';
            }

            $contentHash = md5($body);
            if (isset($this->contentHashCache[$contentHash])) {
                $this->downloadCache[$cacheKey] = $this->contentHashCache[$contentHash];
                return $this->contentHashCache[$contentHash];
            }

            $localPath = ImageWebpConverter::saveAsWebp($body, self::UPLOAD_DIR, $prefix);
            $this->downloadCache[$cacheKey] = $localPath;
            $this->contentHashCache[$contentHash] = $localPath;

            return $localPath;
        } catch (\Exception $e) {
            Log::warning("Apify download exception ({$prefix}): " . $e->getMessage() . " - {$url}");
            return '';
        }
    }

    /**
     * Chuẩn hóa URL ảnh để so sánh trùng (bỏ scheme, www, query string).
     */
    private function normalizeImageUrl(string $url): string
    {
        $url = trim($url);
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return strtolower($url);
        }

        $host = strtolower($parsed['host']);
        $host = preg_replace('/^www\./', '', $host);
        $path = strtolower($parsed['path'] ?? '');

        return $host . $path;
    }

    /**
     * Chuẩn hóa query ảnh: chỉ giữ domain, bỏ http(s)://, path, www.
     */
    private function normalizeImageQuery(string $query): string
    {
        $query = trim($query);
        if ($query === '') {
            return $query;
        }

        if (preg_match('#^https?://#i', $query)) {
            $host = parse_url($query, PHP_URL_HOST);
            if ($host) {
                $query = $host;
            }
        } else {
            $query = preg_replace('#/.*$#', '', $query);
            $query = preg_replace('#^www\.#i', '', $query);
        }

        return preg_replace('/^www\./i', '', $query);
    }

    /**
     * Backward-compatible: lấy 1 ảnh đầu tiên (cũ).
     */
    public function getFirstImage(string $domain): ?string
    {
        try {
            $result = $this->fetchAndStoreImages($domain, 1);
            return $result['featured'];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Backward-compatible: lấy danh sách ảnh content (cũ).
     */
    public function getContentImages(string $domain, int $count = 3): array
    {
        try {
            $result = $this->fetchAndStoreImages($domain, $count);
            return $result['content'];
        } catch (\Exception $e) {
            return [];
        }
    }
}