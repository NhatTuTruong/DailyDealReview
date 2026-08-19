<?php

namespace App\Models;

use App\Libs\Util;
use App\Traits\HasGlobalScopes;
use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Store extends Model
{
    use SoftDeletes;
    use HasGlobalScopes;
    use HasImageCleanup;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected array $imageCleanupConfig = [
        'image' => 'path',
    ];

    const array STATUS_ARRAY = [
        0 => 'Chưa duyệt',
        1 => 'Đã duyệt',
        2 => 'Đã xóa',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    protected static function boot()
    {
        parent::boot();

//        static::deleting(function ($store) {
////            Slug::deleteSlug(Slug::MODULE['POST'], $store->id);
//        });
//
//        static::saved(function ($store) {
////            Slug::insertOrUpdateSlug($store->slug, Slug::MODULE['POST'], $store->id);
//        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUrl(): string
    {
        return Util::url_store($this);
    }

    public function scopePopular($query, $limit = 5)
    {
        return self::with('category')
            ->select($this->getSimpleField())
            ->language()
            ->active()
            ->where('is_hot', 1)
            ->orderByDesc('priority')
            ->orderBy('id', 'desc')
            ->limit($limit);
    }

    public function getRelated($limit = 21)
    {
        return self::where('cat_id', $this->cat_id)
            ->where('id', '<>', $this->id)
            ->active()
            ->language()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPostRelated($limit = 6)
    {
        return Post::where('store_id', $this->id)
            ->active()
            ->language()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function scopeGetStoreByFirstLetter($query, $letter)
    {
        $letter = strtoupper($letter ?: 'A'); // Nếu rỗng thì mặc định 'A'

        if (!preg_match('/^[A-Z]$/', $letter) && $letter !== '0-9') {
            return $query->whereRaw('1 = 0'); // Trả về truy vấn rỗng, tránh sai kiểu dữ liệu
        }

        $query->with('category')
            ->select(self::getSimpleField())
            ->language()
            ->active()
            ->orderBy('name');

        return ($letter === '0-9')
            ? $query->where('name', 'REGEXP', '^[0-9]')
            : $query->where('name', 'LIKE', "{$letter}%");
    }


    public function getOtherStore($limit)
    {
        return Store::with('category')
            ->select($this->getSimpleField())
            ->where('id', '<>', $this->id)
            ->where('cat_id', $this->cat_id)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSimpleField()
    {
        return [
            'id',
            'name',
            'slug',
            'cat_id',
            'priority',
            'description',
            'image',
            'view_num',
            'created_at'
        ];
    }

    const STATE_ARRAY = [
        0 => 'Chưa duyệt',
        1 => 'Đã duyệt',
    ];

    const AFFILIATE_STATUS = [
        'not_registered' => 'Chưa đăng ký',
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'requesting_code' => 'Đang xin code',
        'completed' => 'Đã tổng hợp offers',
        'rejected' => 'Rejected',
        'store_is_dead' => 'Store chết',
//        'ads_running' => 'Đang chạy ads',
        'no_paypal' => 'Không có paypal',
        'confirm_email' => 'Xác nhận email',
        'country_no_support' => 'Country không hỗ trợ',
        'low_traffic' => 'Lượng truy cập ít',
    ];

    const AFFILIATE_NETS = [
        'goaffpro' => 'Go aff pro',
        'uppromote' => 'Up promote',
        'cj' => 'CJ Affiliate',
        'impact' => 'impact.com',
        'collabs_shopify' => 'collabs.shopify.com',
        'couponreals' => 'couponreals.com',
        'socialsnowball' => 'socialsnowball',
        'refersion' => 'Refersion',
        'affiliatly' => 'Affiliatly',
        'kickbooster' => 'Kickbooster.me',
        'leaddyno' => 'leaddyno',
        'self' => 'Self',
    ];

    const ADS_STATUS = [
        'default' => 'Chưa chạy Ads',
        'running' => 'Đang chạy',
        'stop' => 'Tạm ngưng',
        'low_commission' => 'Hoa hồng thấp',
        'not_allowed' => 'Sản phẩm cấm',
    ];

    protected $fillable = [
        'cat_id',
        'event_id',
        'name',
        'slug',
        'image',
        'status',
        'priority',
        'description',
        'about_store',
        'how_to_apply',
        'faqs',
        'user_id',
        'view_num',
        'af_net',
        'af_website',
        'af_flag',
        'af_id',
        'currency',
        'af_portal',
        'cookie_duration',
        'commission_type',
        'commission_amount',
        'commission_on',
        'tags',
        'lang_code',
        'meta_data',
        'created_at',
        'updated_at',
        'max_offer',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'language',
    ];
    /**
     * @var mixed
     */
    private $aggregate_offer;

    public function getUserNameAttribute()
    {
        return data_get($this, 'user.name', 'N/A');
    }

    public function getAfStatusAttribute()
    {
        return self::AFFILIATE_STATUS[$this->af_flag] ?? 'N/A';
    }


    public function getAdsStatusInfoAttribute()
    {
        return self::ADS_STATUS[$this->ads_status] ?? 'N/A';
    }

    public static function makeListAfFlag($selected_id = ''): string
    {
        $html = '';
        foreach (self::AFFILIATE_STATUS as $key => $value) {
            $selected = ($key == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$key\" $selected>" . $value . "</option>";
        }
        return $html;

    }

    public static function makeListAfFNet($selected_id = ''): string
    {
        $html = '';
        foreach (self::AFFILIATE_NETS as $key => $value) {
            $selected = ($key == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$key\" $selected>" . $value . "</option>";
        }
        return $html;

    }

    public static function makeListAdsStatus($selected_id = ''): string
    {
        $html = '';
        foreach (self::ADS_STATUS as $key => $value) {
            $selected = ($key == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$key\" $selected>" . $value . "</option>";
        }
        return $html;

    }

    public static function makeListStore($selected_id = '', $include_root = false)
    {
        $stores = self::language()->where('status', 1)->orderBy('name')->get(['id', 'name']);
        $html = $include_root ? '<option value="0">--Chọn store--</option>' : '';

        foreach ($stores as $store) {
            if (is_array($selected_id)) {
                $selected = in_array($store->id, $selected_id) ? 'selected' : '';
            } else {
                $selected = ($store->id == $selected_id) ? 'selected' : '';
            }
            $html .= "<option value=\"$store->id\" $selected>" . $store->name . "</option>";
        }
        return $html;

    }

    public static function summarizeStoresByAffiliateFlag(): array
    {
        // Truy vấn group by af_flag
        $result = Store::select('af_flag', DB::raw('COUNT(*) as total'))
            ->groupBy('af_flag')
            ->pluck('total', 'af_flag')
            ->toArray();

        // Duyệt theo danh sách định nghĩa sẵn
        $summary = [];
        foreach (self::AFFILIATE_STATUS as $flag => $label) {
            $summary[] = [
                'flag' => $flag,
                'label' => $label,
                'total' => $result[$flag] ?? 0,
            ];
        }

        return $summary;
    }

    public static function summarizeStoresByAdsStatus(): array
    {
        // Truy vấn group by af_flag
        $result = Store::select('ads_status', DB::raw('COUNT(*) as total'))
            ->groupBy('ads_status')
            ->pluck('total', 'ads_status')
            ->toArray();

        // Duyệt theo danh sách định nghĩa sẵn
        $summary = [];
        foreach (self::ADS_STATUS as $flag => $label) {
            $summary[] = [
                'flag' => $flag,
                'label' => $label,
                'total' => $result[$flag] ?? 0,
            ];
        }

        return $summary;
    }

    public function getPopularStores($limit = 15)
    {
        return Store::with('category')
            ->select($this->getSimpleField())
            ->language()
            ->where('status', 1)
            ->orderBy('priority')
            ->orderBy('view_num', 'desc')
            ->orderBy('name')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function aggregateOffer(): array
    {
        if (!$this->aggregate_offer) {
            $aggregates = DB::table('offers')->select('store_id', DB::raw('COUNT(*) as total'))
                ->groupBy('store_id')
                ->get();
            $data = [];

            foreach ($aggregates as $aggregate) {
                $data[$aggregate->store_id] = $aggregate->total;
            }
            $this->aggregate_offer = $data;
        }

        return $this->aggregate_offer;
    }

    public function getTotalOffer($store_id)
    {
        $aggregate = $this->aggregateOffer();
        if (isset($aggregate[$store_id])) {
            return $aggregate[$store_id];
        }
        return 0;
    }

    public function getCommissionAmount(): string
    {
        if ($this->commission_type == 'flat_rate') {
            return '$' . $this->commission_amount;
        }
        return $this->commission_amount . '%';
    }

    public function getCookiesDuration(): string
    {
        $seconds = $this->cookie_duration;
        // Thời gian cơ bản
        $minute = 60;
        $hour = 60 * $minute;
        $day = 24 * $hour;
        $week = 7 * $day;
        $month = 30 * $day;
        $year = 365 * $day;

        // Ưu tiên hiển thị theo thứ tự lớn → nhỏ
        if ($seconds % $year === 0) {
            return ($seconds / $year) . ' năm';
        }

        if ($seconds % $month === 0) {
            return ($seconds / $month) . ' tháng';
        }

        if ($seconds % $week === 0) {
            return ($seconds / $week) . ' tuần';
        }

        if ($seconds % $day === 0) {
            return ($seconds / $day) . ' ngày';
        }

        if ($seconds % $hour === 0) {
            return ($seconds / $hour) . ' giờ';
        }

        if ($seconds % $minute === 0) {
            return ($seconds / $minute) . ' phút';
        }

        return $seconds . ' giây';
    }

    public static function createStoreGooAppPro($limit = 500, $offset = 1, $country = '', $currency = 'USD'): void
    {
        //$url = "https://api-server-3.goaffpro.com/v1/public/sites?keyword=&country={$country}&currency={$currency}&category=&limit={$limit}&offset={$offset}";
        $url = "https://api-server-3.goaffpro.com/v1/public/sites?keyword=&country=&currency=&category=&limit={$limit}&offset={$offset}";

        // Gửi request GET
        $response = Http::get($url);

        if (!$response->successful()) {
            logger()->error('Không lấy được dữ liệu từ API affiliate', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return;
        }

        // Decode JSON
        $data = $response->json();
        if (!isset($data['stores']) || !is_array($data['stores'])) {
            logger()->error('Cấu trúc JSON sai hoặc không có trường "stores"');
            return;
        }

        foreach ($data['stores'] as $item) {
            $affiliateId = $item['id'];

            // Check nếu đã tồn tại
            if (Store::where('af_id', $affiliateId)->orWhere('name', $item['name'])->exists()) {
                Log::log('Info', 'Store is exists');
                continue; // Bỏ qua nếu đã có
            }

            // Tạo store mới
            Store::create([
                'cat_id' => 0,
                'event_id' => 0,
                'name' => Str::limit($item['name'], 480),
                'slug' => Str::slug(Str::limit($item['name'], 480)),
                'image' => $item['logo'],
                'status' => 0,
                'priority' => 0,
                'description' => Setting::getRandomShortDescription($item['name']),
                'about_store' => '',
                'how_to_apply' => '',
                'faqs' => '',
                'user_id' => 0,
                'view_num' => 0,
                'af_net' => 'goaffpro',
                'af_website' => $item['website'],
                'af_flag' => 'not_registered',
                'af_id' => $affiliateId,
                'currency' => $item['currency'] ?? 'USD',
                'af_portal' => $item['affiliatePortal'] ?? null,
                'cookie_duration' => $item['cookieDuration'] ?? null,
                'commission_type' => $item['commission']['type'] ?? null,
                'commission_amount' => $item['commission']['amount'] ?? null,
                'commission_on' => $item['commission']['on'] ?? null,
                'tags' => '',
                'lang_code' => App::getLocale(),
                'meta_data' => json_encode($item),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            Log::log('Info', 'Store created successfully');
        }

        dump('success', count($data['stores']));
    }

    public static function createStoreUppromote()
    {
        // 1. Kiểm tra sự tồn tại của file
        if (!Storage::disk('local')->exists('data/data.json')) {
            return response()->json(['message' => 'File data.json không tồn tại trong storage/app/data/'], 404);
        }

        // 2. Đọc nội dung file JSON
        $json = Storage::disk('local')->get('data/data.json');

        // 3. Chuyển JSON string thành mảng PHP (tham số thứ 2 là `true` để chuyển thành mảng kết hợp)
        $storesData = json_decode($json, true);

        // Kiểm tra nếu JSON không hợp lệ hoặc rỗng
        if (empty($storesData)) {
            return response()->json(['message' => 'Dữ liệu JSON không hợp lệ hoặc rỗng.'], 400);
        }

        $list_store = $storesData['data']['data'];

        // Decode JSON
        if (empty($list_store)) {
            logger()->error('No data found');
            return;
        }

        $importedCount = 0;
        $skippedCount = 0;

        foreach ($list_store as $item) {
            $shop_id = $item['shop_id'];

            // Check nếu đã tồn tại
            if (Store::where('af_id', $shop_id)->where('af_net', 'uppromote')->exists()) {
                Log::log('Info', 'Store is exists');
                $skippedCount++;
                continue; // Bỏ qua nếu đã có
            }

            $apply_url = $item['apply_url'];
            $parts = parse_url($apply_url);
            $af_portal = $parts['host'] . $parts['path'] . '?p=' . $item['program_id'];

            // Tạo store mới
            Store::create([
                'cat_id' => 0,
                'event_id' => 0,
                'name' => Str::limit($item['name'], 480),
                'slug' => Str::slug(Str::limit($item['name'], 480)),
                'image' => $item['logo'],
                'status' => 0,
                'priority' => 0,
                'description' => Setting::getRandomShortDescription($item['name']),
                'about_store' => $item['description'] ?? '',
                'how_to_apply' => '',
                'faqs' => '',
                'user_id' => 0,
                'view_num' => 0,
                'af_net' => 'uppromote',
                'af_website' => $item['myshopify_domain'],
                'af_flag' => 'not_registered',
                'af_id' => $shop_id,
                'currency' => $item['shop_info']['currency'] ?? 'USD',
                'af_portal' => $af_portal,
                'cookie_duration' => $item['cookie'] * 86400 ?? null,
                'commission_type' => ($item['commission_type'] ?? null) == 2 ? 'percentage' : 'fixed',
                'commission_amount' => intval($item['commission_amount'] ?? null),
                'commission_on' => $item['commission']['on'] ?? '',
                'tags' => '',
                'lang_code' => App::getLocale(),
//                'meta_data' => json_encode($item),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            Log::log('Info', 'Store created successfully');
            $importedCount++;
        }

        dump('success', count($list_store));

        dd([
            'message' => 'Hoàn tất quá trình import!',
            'stores_new' => $importedCount,
            'stores_skipped' => $skippedCount,
            'total' => count($list_store),
        ]);
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [
            'view' => [
                'route' => 'store_detail',
            ]
        ];

        foreach (['edit', 'clone'] as $action) {
            if (Gate::allows('store/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_store_' . $action,
                ];
            }
        }

        return $options;
    }
}
