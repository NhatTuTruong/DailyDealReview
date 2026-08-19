<?php

namespace App\Http\Controllers;

use App\Libs\Util;
use App\Libs\Validate;
use App\Models\Category;
use App\Models\Post;
use App\Models\Feedback;
use App\Models\Page;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $clsPost = new Post();

        $top_tags = Post::topTags();
        $list_latest_post = $clsPost->getListLatestPost(9);
        $list_category_post = Category::getCategoryPostHome();

        $setting = Setting::getAllSetting();

        return view('frontend.home.index',
            compact(
                'setting',
                'top_tags',
                'list_latest_post',
                'list_category_post',
            )
        );
    }

    public function page(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)
            ->language()
            ->firstOrFail();

        $list_page = Page::language()->active()->get();

        //SEO MOZ Cấu hình SEO
        $setting = Setting::getAllSetting();
        $setting['meta_title'] = $page->meta_title ?: $page->name;
        $setting['meta_description'] = $page->meta_description ?: $setting['meta_description'];
        $setting['body_class'] = 'post-template-default single single-post single-format-standard';

        return view('frontend.home.page', compact('page', 'list_page', 'setting'));

    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        if (!$keyword) {
            return response()->json(['message' => 'No results'], 400);
        }

        $setting = Setting::getAllSetting();

        $limit = 20;
        $minStore = 5;

        // Nếu dùng LIKE, escape % _ để tránh match quá rộng
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $keyword) . '%';

        // --- Tìm trong STORES trước ---
        $allowSearchStore = !empty($setting['allow_search_store']);
        $stores = collect();
        if ($allowSearchStore) {
            $stores = DB::table('stores')
                ->select('id', 'name', 'slug', 'image', 'allow_search') // thêm cột bạn cần
                ->where(function ($qq) use ($like) {
                    $qq->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like);
                })
                ->where('status', 1)
                ->where('allow_search', 1)
                ->orderByDesc('id')
                ->limit($limit)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'store',
                        'id' => $row->id,
                        'name' => $row->name,
                        'slug' => $row->slug,
                        'image' => $row->image,
                        'url' => route('store_detail', ['slug' => $row->slug]),
                    ];
                });
        }

        $items = $stores;
        $storeCount = $stores->count();

        // --- Nếu store < 5 thì mới tìm thêm POST ---
        if (!$allowSearchStore || $storeCount < $minStore) {
            $remain = $allowSearchStore ? max(0, $limit - $storeCount) : $limit;

            if ($remain > 0) {
                $posts = DB::table('posts')
                    ->select('id', 'name', 'slug', 'image') // thêm cột bạn cần
                    ->where(function ($qq) use ($like) {
                        $qq->where('name', 'like', $like)
                            ->orWhere('slug', 'like', $like);
                    })
                    ->where('status', 1)
                    ->orderByDesc('id')
                    ->limit($remain)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'type' => 'post',
                            'id' => $row->id,
                            'name' => $row->name,
                            'slug' => $row->slug,
                            'image' => $row->image,
                            'url' => route('post_detail', ['slug' => $row->slug, 'id' => $row->id]),
                        ];
                    });

                $items = $stores->concat($posts)->values();
            }
        }
        if ($items->count() < 1) {
            return '<p class="text-muted">No results</p>';
        }

        return view('frontend.home.search_result', compact('items'))->render();
    }

    protected function validateCustomAttributes(Request $request, $validator): void
    {
        $validator->after(function ($validator) use ($request) {
            if (!Validate::validatePhoneNumber($request->get('phone'))) {
                $validator->errors()->add('phone', 'Số điện thoại không đúng');
            }
        });
    }

    public function contact(Request $request, Feedback $feedback)
    {
        $setting = Setting::getAllSetting();
        if ($request->isMethod('post')) {
            $feedback->name = $request->get('name');
            $feedback->kananame = $request->get('kananame');
            $feedback->phone = $request->get('phone');
            $feedback->email = $request->get('email');
            $feedback->content = $request->get('content');
            $feedback->save();
            return redirect()->route('contact')->with('success', __('app.contact.success'));
        }

        //SEO MOZ Cấu hình SEO
        $setting['meta_title'] = 'お問合せ';
        $setting['menu_active'] = 'お問合せ';

        return view('frontend.home.contact', compact('setting'));

    }

    public function subscribe(Request $request, Feedback $feedback)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email:rfc,dns', 'unique:feedbacks,email']
        ]);

        if (RateLimiter::tooManyAttempts('subscribe:' . $request->ip(), 5)) {
            return response()->json(['message' => 'Too many requests. Try again later.'], 429);
        }
        RateLimiter::hit('subscribe:' . $request->ip(), 60);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Email is invalid, or already registered.'
            ], 422);
        }

        $feedback->name = 'Subscribe';
        $feedback->email = $request->get('email');
        $feedback->save();

        return response()->json([
            'message' => 'Registration successful! Thank you.'
        ]);
    }

    public function siteMap(Request $request)
    {
        $lang_code = App::getLocale();
        $data = [];
        $time_cache = 24 * 60 * 60;

        //System
        $data['contact']['loc'] = route('contact');
        $data['contact']['lastmod'] = Carbon::now();

        //Category
        $key_categories = 'site_map_categories_' . $lang_code;
        $categories = Cache::remember($key_categories, $time_cache, function () {
            return Category::active()
                ->language()
                ->select(['id', 'name', 'slug', 'updated_at'])->get();
        });
        foreach ($categories as $key_c => $category) {
            $data['category' . $key_c]['loc'] = $category->getUrl();
            $data['category' . $key_c]['lastmod'] = $category->updated_at;
        }

        //Store
        $key_stores = 'site_map_stores_' . $lang_code;
        $stores = Cache::remember($key_stores, $time_cache, function () {
            return Store::active()
                ->language()
                ->select(['id', 'name', 'slug', 'updated_at'])->get();
        });
        foreach ($stores as $key_pr => $store) {
            $data['store' . $key_pr]['loc'] = $store->getUrl();
            $data['store' . $key_pr]['lastmod'] = $store->updated_at;
        }

        //Post
        $key_posts = 'site_map_posts_' . $lang_code;
        $posts = Cache::remember($key_posts, $time_cache, function () {
            return Post::active()
                ->language()
                ->select(['id', 'name', 'slug', 'updated_at'])->get();
        });
        foreach ($posts as $key_po => $post) {
            $data['post' . $key_po]['loc'] = Util::url_post($post);
            $data['post' . $key_po]['lastmod'] = $post->updated_at;
        }

        //Page
        $key_pages = 'site_map_pages_' . $lang_code;
        $pages = Cache::remember($key_pages, $time_cache, function () {
            return Page::active()
                ->language()
                ->select(['id', 'name', 'slug', 'updated_at'])->get();
        });
        foreach ($pages as $key_pa => $page) {
            $data['page' . $key_pa]['loc'] = Util::url_page($page);
            $data['page' . $key_pa]['lastmod'] = $page->updated_at;
        }
        $last_mod = data_get($data, 'category0.lastmod', Carbon::now());

        return response()->view('frontend.home.sitemap', compact('data', 'last_mod'))
            ->header('Content-Type', 'text/xml');
    }

    public function testSendMail()
    {
        $template = 'email.test';
        $data = ['name' => 'Nguyễn Đức'];
        Util::sendEmail($template, $data, 'Nguyễn Đức Test', 'huuductin1k12@gmail.com');
    }

}
