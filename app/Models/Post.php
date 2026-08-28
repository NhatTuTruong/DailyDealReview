<?php

namespace App\Models;

use App\Libs\Util;
use App\Traits\HasGlobalScopes;
use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Post extends Model
{
    use SoftDeletes;
    use HasGlobalScopes;
    use HasImageCleanup;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'name',
        'slug',
        'cat_id',
        'store_id',
        'image',
        'priority',
        'description',
        'content',
        'source',
        'status',
        'is_hot',
        'view_num',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'language',
        'created_at',
        'updated_at',
    ];

    const array STATUS_ARRAY = [
        0 => 'Chưa duyệt',
        1 => 'Đã duyệt',
        2 => 'Đã xóa',
    ];

    protected array $imageCleanupConfig = [
        'image' => 'path',
        'content' => 'html',
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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post_rel', 'post_id', 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function getUrl(): string
    {
        return Util::url_post($this);
    }

    /**
     * Lấy top $limit tag xuất hiện nhiều nhất trong $top bài viết mới nhất.
     * Kết quả được cache 7 ngày.
     */
    public static function topTags(int $limit = 8, int $top = 1000): array
    {
        $cacheKey = "posts.top8-tags.$limit.$top";

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($limit, $top) {
            // Chỉ lấy đúng cột meta_keywords của 1000 bài mới nhất, tránh load thừa
            $rows = DB::table('posts')
                ->select('meta_keywords')
                ->orderByDesc('id')
                ->limit($top)
                ->pluck('meta_keywords');

            $counts = [];

            foreach ($rows as $csv) {
                if (!$csv) continue;

                // Tách theo dấu phẩy; normalize: trim, gộp khoảng trắng, về lowercase
                $tags = array_map(function ($t) {
                    $t = trim($t);
                    $t = preg_replace('/\s+/', ' ', $t);
                    return mb_strtolower($t);
                }, explode(',', $csv));

                // Loại rỗng và bỏ trùng trong cùng 1 bài viết để không đếm 2 lần
                $tags = array_values(array_unique(array_filter($tags)));

                foreach ($tags as $tag) {
                    $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                }
            }

            // Sắp xếp theo tần suất giảm dần và lấy top $limit
            arsort($counts);
            $top = array_slice($counts, 0, $limit, true);

            // Trả về cả dạng hiển thị Title Case và số lần xuất hiện
            $result = [];
            foreach ($top as $tag => $count) {
                $result[] = [
                    'tag' => Str::slug($tag),                     // normalized (lowercase)
                    'display' => Str::title($tag),         // Xxx Yyy
                    'count' => $count,                   // số bài có gắn tag
                ];
            }

            return $result;
        });
    }

    public function getAllTags(): array
    {
        $language = App::getLocale();
        $posts = Post::select('meta_keywords')
            ->where('language', $language)
            ->where('meta_keywords', '<>', '')
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
        $list_tags = [];
        foreach ($posts as $post) {
            $list_tags = array_merge($list_tags, preg_split("/\s?+,\s?+/", $post->meta_keywords));
        }

        $list_tags = array_filter($list_tags, function ($tag) {
            return !empty($tag);
        });

        return array_unique($list_tags);
    }

    public function getTags(): array
    {
        return $this->meta_keywords ? preg_split("/\s?+,\s?+/", $this->meta_keywords) : [];
    }

    public function getNextPost()
    {
        return Post::active()
            ->where('language', App::getLocale())
            ->where('id', '>', $this->id)
            ->orderBy('id')
            ->first();
    }

    public function getPreviousPost()
    {
        return Post::active()
            ->where('language', App::getLocale())
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function scopePopular($query, $limit = 5)
    {
        return Post::with('category')
            ->select($this->getSimpleField())
            ->language()
            ->active()
            ->orderBy('view_num', 'desc')
            ->limit($limit);
    }

    public function getOtherPost($limit)
    {
        return Post::with('category')
            ->select($this->getSimpleField())
            ->where('id', '<>', $this->id)
            ->where('cat_id', $this->cat_id)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getHotPostsInCategory($limit)
    {
        return Post::with('category')
            ->select($this->getSimpleField())
            ->where('id', '<>', $this->id)
            ->where('cat_id', $this->cat_id)
            ->orderBy('view_num', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getListPostHot($limit)
    {
        $language = App::getLocale();
        return Post::with('category')->select($this->getSimpleField())
            ->where('language', $language)
            ->active()
            ->where('is_hot', 1)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getListLatestPost($limit = 5)
    {
        return Post::with('categories')
            ->select($this->getSimpleField())
            ->language()
            ->active()
            ->limit($limit)
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getListMostViewPost($limit = 5)
    {
        return Post::with('categories')
            ->select($this->getSimpleField())
            ->language()
            ->active()
            ->limit($limit)
            ->orderBy('view_num', 'desc')
            ->get();
    }

    public function getListRandomPost($limit)
    {
        $language = App::getLocale();
        return Post::with('category')
            ->select($this->getSimpleField())
            ->where('language', $language)
            ->active()
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function getCategoryNameAttribute(): string
    {
        $categories_name = [];
        foreach ($this->categories as $category) {
            $categories_name[] = $category->name;
        }
        return implode(', ', $categories_name);
    }

    public function getListRelatedPostByKeyword($meta_keywords, $limit = 5): Collection|array
    {
        $keywords = $meta_keywords ? preg_split("/\s?+,\s?+/", $meta_keywords) : [];
        if (empty($keywords)) {
            return [];
        }

        $query = Post::with('category')
            ->where('language', App::getLocale())
            ->active();

        $query->where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhere('name', 'like', "%$keyword%");
            }
        });

        return $query->orderBy('id', 'DESC')
            ->limit($limit)->get();
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

    public function getAllPostByCatID($cat_id = 0, $limit = 5)
    {
        $clsCategory = new Category();
        $clsCategory->getParentArray();
        $cat_ids = $clsCategory->getAllCatStr($cat_id);
        $cat_ids[] = (int)$cat_id;
        return Post::with('category')
            ->select($this->getSimpleField())
            ->active()
            ->language()
            ->whereIn('cat_id', $cat_ids)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [
            'view' => [
                'route' => 'post_detail',
            ]
        ];

        foreach (['edit', 'delete', 'clone'] as $action) {
            if (Gate::allows('post/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_post_' . $action,
                ];
            }
        }

        return $options;
    }
}
