<?php

namespace App\Services\AI;

use App\Libs\ImageWebpConverter;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str as StrHelper;

class PostGeneratorService
{
    private GeminiService $gemini;
    private ApifyService $apify;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->apify = new ApifyService();
    }

    public function generateFromStore(int $storeId): array
    {
        $store = Store::with('category')->findOrFail($storeId);
        $offers = Offer::where('store_id', $storeId)
            ->active()
            ->language()
            ->orderBy('verified', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        if ($offers->isEmpty()) {
            throw new \Exception("Store '{$store->name}' chưa có offer nào.");
        }

        // 1. Lấy URL website store (dùng làm query Apify)
        $domain = $this->extractDomain($store->af_website);
        if (empty($domain)) {
            $domain = StrHelper::slug($store->name) . '.com';
        }
        // 2. Tìm ảnh bằng Apify — query chỉ dùng domain (vd: aurora-music.com)
        $images = [];
        $featuredImage = null;
        $apifyStatus = 'pending';
        try {
            $query = $domain;
            $imageResult = $this->apify->fetchAndStoreImages($query, 3);
            $featuredImage = $imageResult['featured'] ?: $store->image;
            $images = $imageResult['content'];
            $apifyStatus = !empty($imageResult['content']) ? 'ok' : 'no_images';
        } catch (\Exception $e) {
            Log::warning('Apify fetch failed: ' . $e->getMessage(), ['store_id' => $storeId, 'query' => $query ?? null]);
            $apifyStatus = 'failed';
            $featuredImage = $store->image;
        }

        // 3. Chuẩn bị context cho Gemini
        $siteName = Setting::siteName();
        $language = App::getLocale() === 'vi' ? 'Vietnamese' : 'English';

        $offersInfo = $offers->map(function ($o) {
            return [
                'name' => $o->name,
                'code' => $o->code,
                'offer' => $o->offer,
                'description' => strip_tags($o->description ?? ''),
                'url' => $o->url,
                'verified' => (bool)$o->verified,
            ];
        })->toArray();

        // 4. Prompt Gemini sinh nội dung bài viết
        $categoryName = ($store->category->name ?? null) ?: 'General';
        $descriptionText = ($store->description ?? null) ?: 'Online store';

        $prompt = $this->buildPostPrompt($store, $offersInfo, $siteName, $language, $domain, $categoryName, $descriptionText);

        $generated = $this->gemini->generateJson($prompt);

        // 5. Tạo HTML content có chứa ảnh xen kẽ
        $content = $this->buildHtmlContent(
            $generated['content'] ?? '',
            $images,
            $offersInfo,
            $store
        );

        // 6. Ảnh đại diện đã được Apify download về server thành URL local (WebP)
        $postImage = ImageWebpConverter::ensureWebpForPost($featuredImage ?: $store->image, 'featured');

        // 7. Chuẩn bị slug
        $baseSlug = StrHelper::slug($generated['name'] ?? $store->name);
        if (empty($baseSlug)) {
            $baseSlug = StrHelper::slug($store->name);
        }

        $postCategoryId = Category::getPostCategoryIdFromStoreCategory($store->cat_id ?? 0);

        return [
            'name' => $generated['name'] ?? $store->name . ' - Review & Coupons',
            'slug' => $baseSlug,
            'description' => $generated['description'] ?? '',
            'content' => $content,
            'image' => $postImage ?? $store->image,
            'meta_title' => $generated['meta_title'] ?? '',
            'meta_keywords' => $generated['meta_keywords'] ?? '',
            'meta_description' => $generated['meta_description'] ?? '',
            'store_id' => $store->id,
            'cat_id' => $postCategoryId ?? 0,
            'cat_ids' => $postCategoryId ? [$postCategoryId] : [],
        ];
    }

    private function buildPostPrompt(Store $store, array $offers, string $siteName, string $language, string $domain, string $categoryName, string $descriptionText): string
    {
        $offersText = '';
        foreach ($offers as $i => $o) {
            $offersText .= sprintf(
                "%d. Title: %s | Code: %s | Discount: %s | Description: %s | Affiliate URL: %s | Verified: %s\n",
                $i + 1,
                $o['name'],
                $o['code'] ?? '(no code)',
                $o['offer'],
                $o['description'],
                $o['url'],
                $o['verified'] ? 'Yes' : 'No'
            );
        }

        return <<<PROMPT
You are a skilled content writer for {$siteName}, a trusted coupon and deals platform. Write a compelling, SEO-optimized blog post about the online store "{$store->name}" in English.

GOAL: Create a persuasive article that introduces readers to {$store->name}, highlights the benefits of shopping there, showcases the best deals and coupon codes available, and encourages readers to make a purchase through your affiliate links.

STORE INFORMATION:
- Store Name: {$store->name}
- Website: https://{$domain}
- Category: {$categoryName}
- About: {$descriptionText}

AVAILABLE DEALS & COUPONS:
{$offersText}

ARTICLE REQUIREMENTS:
1. Write in English only — engaging, professional, and persuasive tone
2. Return ONLY a valid JSON object with no markdown, no explanations outside JSON
3. Use double quotes for all JSON string values
4. Escape any double quotes inside strings properly (use \\")
5. Article length: 1200-2000 words
6. Structure: Start with a compelling hook → introduce the store → explain its benefits and why it's worth shopping → mention 1-2 best deals naturally in the text → end with a short call-to-action
7. Do NOT create a long coupon list or "Exclusive Deals" section — a compact coupon block will be inserted automatically in the middle of the article
8. Do NOT repeat every coupon code individually in the body — weave only the top 1-2 codes into the narrative
9. For deals without codes: mention them briefly in context
10. Do NOT add affiliate link blocks at the very start or end — they will be inserted automatically
11. Use proper HTML structure: <h2>, <h3>, <p>, <ul>, <li>, <strong>, <em>
12. Include at least 4 major sections with clear headings
13. Write as if you're genuinely recommending this store to a friend — enthusiastic but credible
14. Do NOT insert images in the HTML — images will be added automatically between paragraphs

JSON OUTPUT FORMAT (exact structure):
{
  "name": "Compelling article title (50-70 characters, include store name)",
  "description": "Engaging meta description that makes people want to click (150-160 characters)",
  "content": "Full HTML article with <h2>, <h3>, <p>, <ul>, <li>, <strong>, <em>, <a href='...'> tags. Focus on store benefits and shopping appeal — do NOT list all coupons.",
  "meta_title": "SEO title (50-60 chars, include store name and main keyword)",
  "meta_keywords": "store name coupons, store name deals, store name promo code, best store name offers, [category] discounts",
  "meta_description": "SEO meta description (150-160 chars)"
}
PROMPT;
    }

    private function buildHtmlContent(string $content, array $images, array $offers, Store $store): string
    {
        $affUrl = $this->getBestAffiliateUrl($offers, $store);
        $html = $content;

        $pEnds = $this->findParagraphEndPositions($html);
        $insertions = [];

        if (!empty($images) && !empty($pEnds)) {
            $imagePoints = $this->calculateImageInsertPoints(count($pEnds), count($images));
            foreach ($imagePoints as $i => $paraIndex) {
                if (!isset($images[$i], $pEnds[$paraIndex])) {
                    continue;
                }
                $pos = $pEnds[$paraIndex];
                $insertions[$pos] = ($insertions[$pos] ?? '') . $this->buildImageFigure($images[$i], $store, $affUrl);
            }
        }

        $couponBlock = $this->buildCompactCouponsBlock($offers, $affUrl);
        if ($couponBlock !== '' && !empty($pEnds)) {
            $midIndex = (int) floor(count($pEnds) / 2);
            $pos = $pEnds[$midIndex];
            $insertions[$pos] = ($insertions[$pos] ?? '') . $couponBlock;
        }

        if (!empty($insertions)) {
            $html = $this->insertAtPositions($html, $insertions);
        }

        $html = $this->wrapHeadingsWithAffLink($html, $affUrl);

        return $this->buildAffiliateIntro($store, $affUrl) . $html . $this->buildAffiliateOutro($store, $affUrl);
    }

    private function wrapHeadingsWithAffLink(string $html, string $affUrl): string
    {
        if ($affUrl === '') {
            return $html;
        }

        return preg_replace_callback(
            '/<h2([^>]*)>(.*?)<\/h2>/is',
            static function (array $matches) use ($affUrl): string {
                $inner = trim($matches[2]);
                if ($inner === '' || stripos($inner, '<a ') !== false) {
                    return $matches[0];
                }

                return sprintf(
                    '<h2%s><a href="%s" target="_blank" rel="nofollow noopener" class="ai-heading-aff-link">%s</a></h2>',
                    $matches[1],
                    htmlspecialchars($affUrl),
                    $inner
                );
            },
            $html
        ) ?? $html;
    }

    /**
     * Ảnh đầu tiên sau đoạn <p> đầu, các ảnh còn lại phân bố đều giữa bài.
     *
     * @return array<int, int>
     */
    private function calculateImageInsertPoints(int $paragraphCount, int $imageCount): array
    {
        if ($paragraphCount <= 0 || $imageCount <= 0) {
            return [];
        }

        if ($imageCount === 1) {
            return [0];
        }

        $points = [0];
        $remaining = $imageCount - 1;
        for ($i = 1; $i <= $remaining; $i++) {
            $points[] = (int) floor($i * ($paragraphCount - 1) / ($remaining + 1));
        }

        sort($points);

        return array_values(array_unique($points));
    }

    /**
     * @return array<int, int>
     */
    private function findParagraphEndPositions(string $html): array
    {
        if (!preg_match_all('/<\/p>/i', $html, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        return array_map(static fn(array $match): int => $match[1] + strlen($match[0]), $matches[0]);
    }

    /**
     * @param array<int, string> $insertions
     */
    private function insertAtPositions(string $html, array $insertions): string
    {
        krsort($insertions, SORT_NUMERIC);
        foreach ($insertions as $pos => $block) {
            $html = substr($html, 0, $pos) . $block . substr($html, $pos);
        }

        return $html;
    }

    private function buildImageFigure(string $imgUrl, Store $store, string $affUrl): string
    {
        $img = sprintf(
            '<img src="%s" alt="%s image" loading="lazy"/>',
            htmlspecialchars($imgUrl),
            htmlspecialchars($store->name)
        );

        if ($affUrl === '') {
            return '<figure class="ai-content-image">' . $img . '</figure>';
        }

        return sprintf(
            '<figure class="ai-content-image"><a href="%s" target="_blank" rel="nofollow noopener" class="ai-image-aff-link">%s</a></figure>',
            htmlspecialchars($affUrl),
            $img
        );
    }

    private function getBestAffiliateUrl(array $offers, Store $store): string
    {
        foreach ($offers as $offer) {
            if (!empty($offer['url'])) {
                return $offer['url'];
            }
        }

        return $this->getStoreWebsiteUrl($store);
    }

    private function buildAffiliateIntro(Store $store, string $affUrl): string
    {
        if ($affUrl === '') {
            return '';
        }

        return sprintf(
            '<p class="ai-aff-intro">Ready to save? Visit <a href="%s" target="_blank" rel="nofollow noopener" class="ai-aff-text-link">%s</a> for the latest deals and exclusive offers.</p>',
            htmlspecialchars($affUrl),
            htmlspecialchars($store->name)
        );
    }

    private function buildAffiliateOutro(Store $store, string $affUrl): string
    {
        if ($affUrl === '') {
            return '';
        }

        return sprintf(
            '<p class="ai-aff-outro"><a href="%s" target="_blank" rel="nofollow noopener" class="ai-cta-btn">Shop Now at %s &raquo;</a></p>',
            htmlspecialchars($affUrl),
            htmlspecialchars($store->name)
        );
    }

    private function getStoreWebsiteUrl(Store $store, ?string $domain = null): string
    {
        $url = trim($store->af_website ?? '');
        if ($url !== '') {
            return $this->normalizeWebsiteUrl($url);
        }

        $domain = $domain ?: StrHelper::slug($store->name) . '.com';

        return 'https://' . $domain . '/';
    }

    private function normalizeWebsiteUrl(string $url): string
    {
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        return str_ends_with($url, '/') ? $url : $url . '/';
    }

    /**
     * Danh sách coupon ngắn gọn: "10% Off — Code: XXX — Offer title"
     */
    private function buildCompactCouponsBlock(array $offers, string $affUrl = ''): string
    {
        if (empty($offers)) {
            return '';
        }

        $items = [];
        $seenCodes = [];
        foreach ($offers as $offer) {
            $code = trim($offer['code'] ?? '');
            if ($code !== '') {
                $dedupeKey = strtolower($code);
                if (isset($seenCodes[$dedupeKey])) {
                    continue;
                }
                $seenCodes[$dedupeKey] = true;
            }

            if (count($items) >= 6) {
                break;
            }

            $items[] = $offer;
        }

        if (empty($items)) {
            return '';
        }

        $html = '<div class="ai-coupons-compact">';
        $html .= '<h3>Available Coupons</h3>';
        $html .= '<ul class="ai-coupon-compact-list">';

        foreach ($items as $offer) {
            $code = trim($offer['code'] ?? '');
            $title = trim($offer['name'] ?? '');
            $discount = trim($offer['offer'] ?? '');
            $offerUrl = trim($offer['url'] ?? '') ?: $affUrl;

            $html .= '<li class="ai-coupon-compact-item">';

            if ($offerUrl !== '') {
                $labelParts = array_filter([$discount, $title]);
                $label = !empty($labelParts) ? implode(' — ', $labelParts) : 'Get This Deal';
                $html .= sprintf(
                    '<a href="%s" target="_blank" rel="nofollow noopener" class="ai-coupon-title-link">%s</a>',
                    htmlspecialchars($offerUrl),
                    htmlspecialchars($label)
                );
            } else {
                $textParts = array_filter([$discount, $title]);
                $html .= htmlspecialchars(implode(' — ', $textParts));
            }

            if ($code !== '') {
                $html .= sprintf(
                    ' <button type="button" class="ai-coupon-copy" data-code="%s" aria-label="Copy coupon code %s">Code: %s</button>',
                    htmlspecialchars($code),
                    htmlspecialchars($code),
                    htmlspecialchars($code)
                );
            }

            $html .= '</li>';
        }

        $html .= '</ul></div>';

        return $html;
    }

    private function extractDomain(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return null;
        }

        // Remove www.
        return preg_replace('/^www\./', '', $host);
    }
}