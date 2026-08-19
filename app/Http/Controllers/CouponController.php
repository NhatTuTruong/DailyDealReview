<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Category;

class CouponController extends Controller
{
    public function categories()
    {
        $categories = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_COUPON);
        $categories = array_chunk($categories, 4);

        $setting = Setting::getAllSetting();

        return view('frontend.coupon.category',
            compact(
                'categories',
                'setting'
            )
        );
    }

    public function index(Request $request, Category $category, $options = [])
    {
        $is_season_category = !empty($options['season']);

        $clsCategory = new Category();
        $clsCategory->getParentArray();
        $cat_ids = $clsCategory->getAllCatStr($category->id);
        $cat_ids[] = (int)$category->id;

        $filter = $request->get('filter');
        if (!in_array($filter, ['coupons', 'deals'])) {
            $filter = 'all';
        }

        $query_coupon = Offer::with('store')
            ->active()
            ->notExpired()
            ->when($filter !== 'all', function ($query) use ($filter) {
                return $filter === 'coupons'
                    ? $query->whereNotNull('code')->where('code', '!=', '')
                    : $query->where(function ($q) {
                        $q->whereNull('code')->orWhere('code', '');
                    });
            });
        if ($is_season_category) {
            $query_coupon->whereIn('season_id', $cat_ids);
        } else {
            $store_ids = Store::active()
                ->language()
                ->whereIn('cat_id', $cat_ids)
                ->pluck('id')
                ->toArray();

            $query_coupon->whereIn('store_id', $store_ids);
        }

        // Lấy tất cả coupon đã lọc và chỉ giữ lại coupon mới nhất mỗi store
        $all_coupons = $query_coupon
            ->orderBy('priority')
            ->orderByDesc('id') // Quan trọng: id giảm dần để unique lấy coupon mới nhất
            ->get()
            ->unique('store_id')
            ->values(); // reset lại key

        // Manual pagination (vì dùng collection)
        $page = request()->get('page', 1);
        $perPage = 60;
        $offset = ($page - 1) * $perPage;

        $coupons = new \Illuminate\Pagination\LengthAwarePaginator(
            $all_coupons->slice($offset, $perPage),
            $all_coupons->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        $coupon_modal = null;
        $coupon_id = $request->get('coupon');
        if ($coupon_id) {
            $coupon_modal = Offer::with('store')->where('id', $coupon_id)->first();
        }

        $child_categories = $category->getChildrenCategories(Category::CATEGORY_TYPE_COUPON);
        $child_cat_ids = $child_categories->pluck('id')->toArray();

        $popular_stores = Store::language()->active()->whereIn('cat_id', $child_cat_ids)->limit(12)->get();

        //SEO MOZ
        $setting = Setting::getAllSetting();
        $setting['meta_title'] = ($category->meta_title) ?: $category->name;
        $setting['meta_keywords'] = ($category->meta_keywords) ?: $setting['meta_keywords'];
        $setting['meta_description'] = ($category->meta_description) ?: $setting['meta_description'];
        $setting['og_image'] = ($category->image) ?: ($setting['og_image'] ?? '');

        return view('frontend.coupon.index',
            compact(
                'category',
                'coupons',
                'coupon_modal',
                'child_categories',
                'popular_stores',
                'filter',
                'setting'
            )
        );
    }

    public function season(Request $request, Category $category)
    {
        return $this->index($request, $category, ['season' => true]);
    }
}
