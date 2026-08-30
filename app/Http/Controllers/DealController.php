<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * Sample deals for the /deal landing page.
     * When a matching store exists in DB, brand image and store URL are used automatically.
     *
     * @return array<int, array<string, mixed>>
     */
    private function sampleDeals(): array
    {
        return [
            [
                'brand' => 'Amazon',
                'slug' => 'amazon',
                'title' => '20% Off Select Electronics',
                'code' => 'AMZSAVE20',
                'badge' => 'Verified',
                'description' => 'Save on laptops, headphones, and smart home devices.',
            ],
            [
                'brand' => 'Nike',
                'slug' => 'nike',
                'title' => 'Extra 15% Off Sale Items',
                'code' => 'NIKE15',
                'badge' => 'Hot Deal',
                'description' => 'Members get an extra discount on clearance styles.',
            ],
            [
                'brand' => 'Walmart',
                'slug' => 'walmart',
                'title' => 'Free Shipping on Orders $35+',
                'code' => '',
                'badge' => 'Free Shipping',
                'description' => 'No minimum hassle — shop groceries and essentials online.',
            ],
            [
                'brand' => 'Target',
                'slug' => 'target',
                'title' => '$10 Off $50 Home & Decor',
                'code' => 'TARGET10',
                'badge' => 'Limited Time',
                'description' => 'Refresh your space with seasonal home favorites.',
            ],
            [
                'brand' => 'Best Buy',
                'slug' => 'best-buy',
                'title' => 'Up to $200 Off Laptops',
                'code' => 'BBTECH200',
                'badge' => 'Verified',
                'description' => 'Top picks for students and remote work setups.',
            ],
            [
                'brand' => 'Sephora',
                'slug' => 'sephora',
                'title' => 'Gift With Purchase $75+',
                'code' => '',
                'badge' => 'Beauty',
                'description' => 'Discover bestsellers from premium beauty brands.',
            ],
        ];
    }

    public function index(Request $request)
    {
        $deals = collect($this->sampleDeals())
            ->map(fn (array $deal) => $this->hydrateDeal($deal))
            ->values();

        $setting = Setting::getAllSetting();
        $pageTitle = 'Deals';
        $setting['meta_title'] = $pageTitle . ' | ' . ($setting['site_name'] ?? '');
        $setting['meta_description'] = 'Hand-picked coupon codes and deals from top brands like Amazon, Nike, Walmart, and more.';
        $setting['body_class'] = trim(($setting['body_class'] ?? '') . ' deals archive page');

        return view('frontend.deal.index', compact('deals', 'setting', 'pageTitle'));
    }

    /**
     * @param array<string, mixed> $deal
     * @return array<string, mixed>
     */
    private function hydrateDeal(array $deal): array
    {
        $store = Store::query()
            ->active()
            ->language()
            ->where(function ($query) use ($deal) {
                $query->where('slug', $deal['slug'])
                    ->orWhere('name', $deal['brand']);
            })
            ->first();

        $deal['image'] = $deal['image'] ?? '/images/store.webp';
        $deal['store_url'] = route('store_all');
        $deal['affiliate_url'] = null;

        if ($store) {
            $deal['image'] = $store->image ?: $deal['image'];
            $deal['store_url'] = $store->getUrl();

            $offer = Offer::query()
                ->where('store_id', $store->id)
                ->active()
                ->language()
                ->orderByDesc('verified')
                ->orderByDesc('priority')
                ->orderByDesc('id')
                ->first();

            if ($offer) {
                $deal['title'] = $offer->name ?: ($offer->offer ?: $deal['title']);
                $deal['code'] = $offer->code ?: $deal['code'];
                $deal['description'] = strip_tags($offer->description ?? '') ?: $deal['description'];
                $deal['affiliate_url'] = $offer->url;
                if ($offer->verified) {
                    $deal['badge'] = 'Verified';
                }
            }
        }

        $deal['cta_url'] = $deal['affiliate_url'] ?: $deal['store_url'];

        return $deal;
    }
}
