<?php

namespace app\Imports;

use App\Models\Category;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class StoreOfferImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsEmptyRows, WithMultipleSheets
{
    protected $stores = [];
    protected $lastStore = null;
    protected $lastCategoryName = null;
    protected $storeOfferCount = [];
    protected $currentRowIndex = 0;

    protected $offerNameTemplates = [
        'Get %s – Shop Now & Save',
        'Save %s on Your Order – Claim This Offer Today',
        'Enjoy %s Your Purchase – Don\'t Miss This Deal',
        'Claim %s Everything – Start Saving Today',
        'Unlock %s Your Order – Shop This Special Deal',
        'Save %s on Your Next Purchase – Get the Deal Now',
        'Get %s Off Your Order – Take Advantage of This Offer',
        'Enjoy %s Today – Grab This Offer Before It\'s Gone',
        'Unlock %s Your Purchase – Start Saving on Your Order',
        'Take %s Off Your Order – Use This Deal & Save More',
    ];

    public function headingRow(): int
    {
        return 2;
    }

    public function sheets(): array
    {
        return [
            'Import' => $this,
        ];
    }

    public function rules(): array
    {
        return [
            'store_name' => 'nullable|string|max:500',
            'ten_cua_hang' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:255',
            'danh_muc' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:5000',
            'gioi_thieu' => 'nullable|string|max:5000',
            'max_offer' => 'nullable|string|max:100',
            'offer_name' => 'nullable|string|max:255',
            'ten_offer' => 'nullable|string|max:255',
            'coupon_code' => 'nullable|string|max:100',
            'ma_coupon' => 'nullable|string|max:100',
            'promo_content' => 'nullable|string|max:500',
            'noi_dung_khuyen_mai' => 'nullable|string|max:500',
            'logo_url' => 'nullable|string|max:500',
            'domain' => 'nullable|string|max:255',
            'url' => 'nullable|url',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'url.url' => 'Đường dẫn URL không đúng định dạng.',
        ];
    }

    /**
     * Lấy logo từ domain sử dụng Google Favicon API
     */
    protected function fetchLogoFromDomain(string $domain): ?string
    {
        $domain = str_replace(['https://', 'http://', 'www.'], '', $domain);
        $domain = trim($domain, '/');

        // Sử dụng Google Favicon API
        $logoUrl = "https://www.google.com/s2/favicons?domain={$domain}&sz=256";

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 15,
                    'ignore_errors' => true,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]
            ]);

            $response = @file_get_contents($logoUrl, false, $context);

            if ($response !== false && !empty($response) && strlen($response) > 100) {
                // Tạo thư mục upload nếu chưa có
                $uploadPath = public_path('uploads/store');
                if (!File::exists($uploadPath)) {
                    File::makeDirectory($uploadPath, 0755, true);
                }

                // Tạo tên file unique
                $filename = Str::slug($domain) . '_' . time() . '.png';
                $fullPath = $uploadPath . '/' . $filename;

                // Lưu file
                File::put($fullPath, $response);

                // Trả về path để lưu vào DB (public path)
                return 'uploads/store/' . $filename;
            }
        } catch (\Exception $e) {
            // Log error nếu cần
        }

        return null;
    }

    /**
     * Generate Meta SEO theo template
     */
    protected function generateMetaSEO(string $storeName, ?string $maxOffer = null, string $type = 'title'): string
    {
        $maxOfferStr = $maxOffer ?: '50% Off';
        $storeNameUpper = strtoupper($storeName);
        $storeNameTitle = ucwords($storeName);

        switch ($type) {
            case 'meta_keywords':
                return "Promo code {$storeNameTitle}, Coupon code {$storeNameTitle}, Discount code {$storeNameTitle}, Discount {$storeNameTitle}";
            case 'meta_description':
                return "{$maxOfferStr} {$storeNameUpper} Coupons & Promo Codes 2025 With This Exclusive {$storeNameUpper} Coupon Code, Shop Till You Drop And Enjoy {$maxOfferStr} Apply these {$storeNameUpper} Discount At Checkout And Save. Tested Daily. Get {$storeNameUpper} Promo Code At Checkout And Enjoy {$maxOfferStr}. Don't Hesitate Anymore.";
            default: // meta_title
                return "Save {$maxOfferStr} Discount Code Best {$storeNameTitle} Discount Best {$storeNameTitle} Coupons Top {$storeNameTitle} Coupons";
        }
    }

    /**
     * Tạo tên offer ngẫu nhiên từ promo content
     */
    protected function generateOfferName(string $promoContent): string
    {
        $template = $this->offerNameTemplates[array_rand($this->offerNameTemplates)];
        return sprintf($template, $promoContent);
    }

    public function onRow(Row $row)
    {
        $this->currentRowIndex++;
        $rowIndex = $row->getIndex();
        $row = $row->toArray();

        // Hỗ trợ cả tiếng Anh và tiếng Việt
        $storeName = trim($row['store_name'] ?? $row['ten_cua_hang'] ?? '');
        $categoryName = trim($row['category'] ?? $row['danh_muc'] ?? '');
        $maxOffer = trim($row['max_offer'] ?? '');
        $aboutStore = trim($row['about'] ?? $row['gioi_thieu'] ?? '');
        $offerName = trim($row['offer_name'] ?? $row['ten_offer'] ?? '');
        $couponCode = trim($row['coupon_code'] ?? $row['ma_coupon'] ?? '');
        $promoContent = trim($row['promo_content'] ?? $row['noi_dung_khuyen_mai'] ?? '');
        $logoUrl = $row['logo_url'] ?? null;
        $domain = trim($row['domain'] ?? '');
        $url = trim($row['url'] ?? '');

        // Nếu có thông tin cửa hàng mới → tạo/update store mới
        if (!empty($storeName)) {
            $category = null;
            if ($categoryName) {
                $category = Category::where('name', $categoryName)
                    ->where('language', app()->getLocale())
                    ->where('type', Category::CATEGORY_TYPE_STORE)
                    ->first();
            }

            // Xử lý logo: ưu tiên domain > logo_url
            $finalLogoUrl = $logoUrl;
            if (empty($finalLogoUrl) && !empty($domain)) {
                $fetchedLogo = $this->fetchLogoFromDomain($domain);
                if ($fetchedLogo) {
                    $finalLogoUrl = $fetchedLogo;
                }
            }

            $storeData = [
                'slug' => Str::slug($storeName),
                'cat_id' => $category?->id ?? 0,
                'image' => $finalLogoUrl,
                'description' => $aboutStore ?: Setting::getRandomShortDescription($storeName),
                'about_store' => $aboutStore ?: null,
                'max_offer' => $maxOffer ?: null,
                'priority' => 0,
                'status' => 1,
                'user_id' => Auth::id() ?? 1,
                'language' => \App::getLocale(),
                'meta_title' => $this->generateMetaSEO($storeName, $maxOffer),
                'meta_keywords' => $this->generateMetaSEO($storeName, $maxOffer, 'meta_keywords'),
                'meta_description' => $this->generateMetaSEO($storeName, $maxOffer, 'meta_description'),
                // Giá trị mặc định
                'ads_status' => 'default',
                'af_flag' => 'approved',
                'af_net' => '',
            ];

            $store = Store::firstOrNew(['name' => $storeName]);
            $store->fill($storeData);
            $store->save();
            $this->stores[$storeName] = $store;
            $this->lastStore = $store;
            $this->lastCategoryName = $categoryName;
            $this->storeOfferCount[$storeName] = 0;
        }
        // Nếu không có tên cửa hàng → dùng store cuối cùng
        elseif ($this->lastStore) {
            $store = $this->lastStore;

            // Cập nhật thêm max_offer nếu chưa có
            if ($maxOffer && empty($store->max_offer)) {
                $store->max_offer = $maxOffer;
                $store->save();
            }
        }
        // Không có store nào phía trên → bỏ qua hàng này
        else {
            return;
        }

        // Auto-generate offer name nếu trống
        if (empty($offerName) && !empty($promoContent)) {
            $offerName = $this->generateOfferName($promoContent);
        } elseif (empty($offerName)) {
            $offerName = Setting::getRandomShortDescription($store->name);
        }

        // Auto-fill URL: lấy từ offers đã có của store hoặc store URL
        if (empty($url)) {
            $existingOffer = Offer::where('store_id', $store->id)
                ->whereNotNull('url')
                ->where('url', '!=', '')
                ->where('url', 'NOT LIKE', '%example.com%')
                ->where('url', 'NOT LIKE', '%undefined%')
                ->where('url', 'NOT LIKE', '%null%')
                ->first();

            if ($existingOffer) {
                $url = $existingOffer->url;
            } elseif (!empty($store->name)) {
                $url = 'https://www.google.com/search?q=' . urlencode($store->name . ' coupon code');
            }
        }

        // Priority: offer đầu tiên trong Excel = priority cao nhất
        $this->storeOfferCount[$store->name] = ($this->storeOfferCount[$store->name] ?? 0) + 1;
        $priority = 100 - $this->storeOfferCount[$store->name]; // Dòng 1 = 99, dòng 2 = 98...

        // XỬ LÝ OFFER
        Offer::create([
            'store_id' => $store->id,
            'name' => $offerName,
            'code' => $couponCode ?: null,
            'offer' => $promoContent ?: null,
            'url' => $url,
            'description' => Setting::getRandomShortDescription($store->name),
            'status' => 1,
            'verified' => 1,
            'priority' => $priority,
            'user_id' => Auth::id() ?? 1,
            'language' => \App::getLocale(),
        ]);
    }
}