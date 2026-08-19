<?php

namespace App\Models;

use App\Traits\HasGlobalScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Offer extends Model
{
    use HasGlobalScopes;

    protected $fillable = [
        'store_id',
        'name',
        'code',
        'offer',
        'url',
        'status',
        'user_id',
        'verified',
        'priority',
        'language',
        'description',
    ];

    protected $table = 'offers';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    const array STATUS_ARRAY = [
        0 => 'Chưa duyệt',
        1 => 'Đã duyệt',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($post) {
//            Slug::deleteSlug(Slug::MODULE['POST'], $post->id);
        });

        static::saved(function ($post) {
//            Slug::insertOrUpdateSlug($post->slug, Slug::MODULE['POST'], $post->id);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

    public static function aggregatedCoupons(array $store_ids = [], array $season_ids = []): ?object
    {
        $query = Offer::query()
            ->active();

        if (!empty($season_ids)) {
            $query->whereIn('season_id', $season_ids);
        } elseif (!empty($store_ids)) {
            $query->whereIn('store_id', $store_ids);
        } else {
            return null;
        }

        return $query->selectRaw("COUNT(CASE WHEN code IS NOT NULL AND code != '' THEN 1 END) AS with_code, 
                     COUNT(CASE WHEN code IS NULL OR code = '' THEN 1 END) AS without_code")
            ->first();
    }

    public static function getOneCouponEachStore($storeIds, $limit = 30)
    {
        return Offer::with('store')
            ->whereIn('store_id', $storeIds)
            ->active()
            ->language()
            ->orderBy('priority')
            ->orderByDesc('id')
            ->get()
            ->unique('store_id')
            ->take($limit)
            ->values();
    }

    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->name : '--';
    }

    public static function makeListType($selected = ''): string
    {
        $html = '';

        foreach (['Off', 'Ship', 'Deal'] as $type) {
            $selected_attr = ($type == $selected) ? 'selected' : '';
            $html .= "<option value=\"$type\" $selected_attr>" . $type . "</option>";
        }
        return $html;
    }

    public static function getAllByStoreId($store_id = 0, $limit = 100): array
    {
        $offers = Offer::with('store')
            ->where('status', 1)
            ->language()
            ->where('store_id', $store_id)
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)->get();

        $all = [];
        $total_verified = $total_codes = $total_deals = 0;
        $best_offer = null;
        $max_value = 0;

        foreach ($offers as $offer) {
            $all[] = $offer;
            if ($offer->verified) {
                $total_verified += 1;
            }
            if ($offer->code) {
                $total_codes += 1;
            } else {
                $total_deals += 1;
            }
            $offer_value = preg_replace('/[^0-9]+/', '', $offer->offer);
            if ($offer_value > $max_value || !$best_offer) {
                $max_value = $offer_value;
                $best_offer = $offer;
            }
        }
        return [
            'all' => $all,
            'total_verified' => $total_verified,
            'total_codes' => $total_codes,
            'total_deals' => $total_deals,
            'best_offer' => $best_offer,
        ];
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];
        foreach (['edit', 'delete', 'clone'] as $action) {
            if (Gate::allows('offer/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_offer_' . $action,
                ];
            }
        }

        return $options;
    }
}
