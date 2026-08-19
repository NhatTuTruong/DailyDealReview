<?php

namespace App\Http\Controllers\Backend;

use App\Models\Offer;
use App\Models\Post;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $total['store'] = Store::where('status', 1)->count();
        $total['offer'] = Offer::where('status', 1)->count();
        $total['deal'] = 15;
        $total['post'] = Post::count();
        $summarize_store = Store::summarizeStoresByAffiliateFlag();
        $summarize_ads_status = Store::summarizeStoresByAdsStatus();
        $chart_summarize_store = [
            'labels' => array_column($summarize_store, 'label'),
            'totals' => array_column($summarize_store, 'total'),
        ];
        $chart_summarize_ads_status = [
            'labels' => array_column($summarize_ads_status, 'label'),
            'totals' => array_column($summarize_ads_status, 'total'),
        ];
        return view('backend.dashboard.index', compact('total', 'chart_summarize_store', 'chart_summarize_ads_status'));
    }
}

