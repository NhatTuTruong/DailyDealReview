<?php

namespace App\Http\Controllers;

use App\Libs\Util;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\App;

class StoreController extends Controller
{
    public function index(Request $request, $slug)
    {
        $clsStore = new Store();
        $offer_id = $request->get('offer');

        $store = Store::language()->where('slug', $slug)->active()->firstOrFail();

        $offer_modal = null;
        if ($offer_id) {
            $offer_modal = Offer::where('id', $offer_id)->first();
        }

        $popular_stores = $clsStore->getPopularStores(21);
        $offers = Offer::getAllByStoreId($store->id);
        $best_offer = $offers['best_offer'] ?? new Offer();
        $store->increment('view_num');

        $store->how_to_apply = $store->how_to_apply ?: Setting::getStoreHowToApply($store->name);
        $store->faqs = $store->faqs ?: Setting::getStoreFAQ($store->name);

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = $store->meta_title ?: $setting['meta_title'];
        $setting['meta_keywords'] = data_get($store, 'meta_keywords', $setting['meta_keywords']);
        $setting['meta_description'] = data_get($store, 'meta_description', $setting['meta_description']);
        $setting['canonical'] = Util::url_store($store);

        return view('frontend.store.index',
            compact(
                'store',
                'popular_stores',
                'offers',
                'offer_modal',
                'best_offer',
                'setting'
            )
        );
    }

    public function allStore(Request $request)
    {
        $lang_code = App::getLocale();
        $category = new Category();
        $clsStore = new Store();

        $paginate = 14;
        $query_store = Store::where('state', 1)
            ->where('lang_code', $lang_code)
            ->orderBy('order_no')
            ->orderBy('view_num', 'desc')
            ->orderBy('id', 'desc');
        $stores = $query_store->simplePaginate($paginate);

        $aggregate_offer = $clsStore->aggregateOffer();

        $list_sub_category = $category->getSubCategory();

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = 'All Stores';

        return view('frontend.coupon.index',
            compact(
                'category',
                'stores',
                'list_sub_category',
                'aggregate_offer',
                'setting'
            )
        );
    }

    public function createStore(Request $request)
    {

//        Store::createStoreGooAppPro($limit = 100, $offset = 1);
//        Store::createStoreUppromote();//done page 6 / 100

        dd('done');
    }
}
