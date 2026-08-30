<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Support\Facades\App;

class PostController extends Controller
{
    public function index(Request $request, Category $category)
    {
        $clsPost = new Post();

        $paginate = 15;
        $category_ids = $category->allChildrenIds();

        $posts = Post::whereHas('categories', function ($q) use ($category_ids) {
            $q->whereIn('categories.id', $category_ids);
        })
            ->with('categories')
            ->active()
            ->language()
            ->orderBy('priority')
            ->orderBy('id', 'desc')
            ->simplePaginate($paginate);

        $list_latest_post = $clsPost->getListLatestPost();
        $list_category = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_POST);

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = ($category->meta_title) ?: $category->name;
        $setting['meta_keywords'] = ($category->meta_keywords) ?: $setting['meta_keywords'];
        $setting['meta_description'] = ($category->meta_description) ?: $setting['meta_description'];

        return view('frontend.post.index',
            compact(
                'category',
                'posts',
                'list_latest_post',
                'list_category',
                'setting'
            )
        );
    }

    public function all(Request $request)
    {
        $clsPost = new Post();

        $posts = Post::with('categories')
            ->active()
            ->language()
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->paginate(15);

        $list_latest_post = $clsPost->getListLatestPost();
        $list_category = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_POST);

        $setting = Setting::getAllSetting();
        $pageTitle = 'Blog';
        $setting['meta_title'] = $pageTitle . ' | ' . ($setting['site_name'] ?? '');
        $setting['meta_description'] = $setting['meta_description'] ?? 'Browse all blog posts, deals guides, and store reviews.';
        $setting['body_class'] = trim(($setting['body_class'] ?? '') . ' blog archive page');

        return view('frontend.post.index', compact(
            'posts',
            'list_latest_post',
            'list_category',
            'setting',
            'pageTitle',
        ));
    }

    public function search(Request $request)
    {
        $clsPost = new Post();

        $key = trim((string) $request->get('key', ''));
        if ($key === '') {
            return redirect()->route('home_page');
        }

        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $key) . '%';

        $posts = Post::with('categories')
            ->active()
            ->language()
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            })
            ->orderByDesc('priority')
            ->orderByDesc('id')
            ->paginate(8);

        $category = new Category();
        $category->name = 'Search results for "' . $key . '"';

        $list_latest_post = $clsPost->getListLatestPost();
        $list_category = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_POST);

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = 'Search: ' . $key;

        return view('frontend.post.index', compact(
            'posts',
            'key',
            'category',
            'list_latest_post',
            'list_category',
            'setting'
        ));
    }

    public function detail(Request $request, $slug, $id)
    {
        /* @var $post Post */
        $clsPost = new Post();

        $post = Post::with(['categories', 'store'])
            ->active()
            ->language()
            ->where('id', $id)
            ->firstOrFail();

        $postAffUrl = optional($post->store)->af_website ?? '';

        $category = Category::where('id', data_get($post, 'cat_id'))->first();
        $other_posts = $post->getOtherPost(3);
        $list_latest_post = $clsPost->getListLatestPost();
        $list_category = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_POST);

        $post->increment('view_num');

        //SEO MOZ
        $setting = Setting::getAllSetting();
        $setting['meta_title'] = ($post->meta_title) ?: $post->name;
        $setting['meta_keywords'] = ($post->meta_keywords) ?: $setting['meta_keywords'];
        $setting['meta_description'] = ($post->meta_description) ?: $setting['meta_description'];
        $setting['og_image'] = ($post->image) ?: ($setting['og_image'] ?? '');
        $setting['body_class'] = 'post-template-default single single-post single-format-standard';

        return view('frontend.post.detail',
            compact(
                'setting',
                'post',
                'category',
                'list_latest_post',
                'list_category',
                'other_posts',
                'postAffUrl',
            )
        );
    }

    public function tag(Request $request, $tag)
    {
        if (!$tag) {
            abort(404);
        }
        $clsPost = new Post();

        $paginate = 15;
        $query_post = Post::with('category')
            ->active()
            ->language()
            ->where(fn($query) => $query->where('meta_keywords', 'like', '%' . $tag . '%')
                ->orWhere('slug', 'like', '%' . $tag . '%'))
            ->orderBy('priority')
            ->orderBy('id', 'desc');
        $posts = $query_post->simplePaginate($paginate);

        $list_latest_post = $clsPost->getListLatestPost();
        $list_category = Category::getAllMenuLink(0, Category::CATEGORY_TYPE_POST);

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = 'Tag: ' . $tag;

        return view('frontend.post.index',
            compact(
                'posts',
                'tag',
                'list_latest_post',
                'list_category',
                'setting'
            )
        );
    }
}
