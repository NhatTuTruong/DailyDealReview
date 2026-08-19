<?php

namespace App\Models;

use App\Libs\Util;
use App\Traits\HasGlobalScopes;
use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Category extends Model
{
    use HasGlobalScopes;
    use HasImageCleanup;

    protected $table = "categories";
    public array $parents = [];

    const int CATEGORY_TYPE_POST = 0;
    const int CATEGORY_TYPE_STORE = 1;
    const int CATEGORY_TYPE_EVENT = 2;

    protected $fillable = ['slug', 'name', 'type'];

    protected array $imageCleanupConfig = [
        'image' => 'path',
    ];

    const array OPTIONS_CATEGORY = [
        self::CATEGORY_TYPE_POST => 'Danh mục bài viết',
        self::CATEGORY_TYPE_STORE => 'Danh mục store',
        self::CATEGORY_TYPE_EVENT => 'Danh mục sự kiện',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            //Slug::deleteSlug(Slug::MODULE['CATEGORY'], $category->id);
        });

        static::saved(function ($category) {
            //Slug::insertOrUpdateSlug($category->slug, Slug::MODULE['CATEGORY'], $category->id);
        });
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'category_post_rel', 'category_id', 'post_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allChildrenIds()
    {
        $ids = collect([$this->id]);
        foreach ($this->children as $child) {
            $ids = $ids->merge($child->allChildrenIds());
        }
        return $ids;
    }

    public function getSimpleField()
    {
        return [
            'id',
            'name',
            'slug',
            'type',
            'priority',
            'parent_id',
            'image',
        ];
    }

    function getColorLabel()
    {
        $cat_id = $this->id ?: 0;
        $list_color = ['#dd2088', '#c98622', '#f46302', '#ec0909', '#1ca1b5', '#31a599', '#cc8528', '#e80f88'];
        static $map = [];

        // Nếu đã có thì trả luôn
        if (isset($map[$cat_id])) {
            return $map[$cat_id];
        }

        // Nếu chưa có thì random
        $color = $list_color[array_rand($list_color)];
        $map[$cat_id] = $color;

        return $color;
    }

    public function getUrl(): string
    {
        return Util::url_category($this);
    }

    public function getAll($type = -1)
    {
        $language = App::getLocale();
        $query = Category::select($this->getSimpleField())
            ->where('status', 1)
            ->where('language', $language);
        if ($type > -1) {
            $query->where('type', $type);
        }
        return $query->orderBy('type')
            ->orderBy('priority')
            ->orderBy('name')
            ->get();
    }

    public function showCategories($categories, $parent_id = 0, $char = '')
    {
        foreach ($categories as $key => $category) {
            if ($category->parent_id == $parent_id) {
                $categories1 = $categories->firstWhere('parent_id', $parent_id);
                if ($categories1) {
                    $categories1->name = $char . $categories1->name;
                    $categories[] = $categories1;
                }

                unset($categories[$key]);
                $this->showCategories($categories, data_get($category, 'id'), '&brvbar;--- ' . $char);
            }
        }
        return $categories->values();
    }

    public static function makeListCategory($parent_id = 0, $type = -1, $selected_id = '', $include_default = false)
    {
        $language = App::getLocale();
        $query = Category::where('language', $language);
        if ($type > -1) {
            $query = $query->where('type', $type);
        }
        $categories = $query->orderBy('priority')->orderBy('name')->get(['id', 'parent_id', 'name']);
        $html = '';

        if ($include_default) {
            $html .= "<option value='0'>__ROOT__</option>";
        }

        $list_categories = (new self())->showCategories($categories, $parent_id);
        foreach ($list_categories as $category) {
            if (is_array($selected_id)) {
                $selected = in_array($category->id, $selected_id) ? 'selected' : '';
            } else {
                $selected = ($category->id == $selected_id) ? 'selected' : '';
            }
            $html .= "<option value=\"$category->id\" $selected>" . $category->name . "</option>";
        }
        return $html;

    }

    public static function makeArrayListCategory($parent_id = 0, $type = -1): array
    {
        $query = Category::where('language', App::getLocale());

        if ($type > -1) {
            $query = $query->where('type', $type);
        }

        $categories = $query->orderBy('priority')->orderBy('name')->get(['id', 'parent_id', 'name']);
        $list_categories = (new self())->showCategories($categories, $parent_id, '');
        $results = [];
        if ($list_categories) {
            foreach ($list_categories as $category) {
                $results[$category->id] = $category->name;
            }
        }
        return $results;
    }

    public static function getAllMenuLink($parent_id = 0, $type = -1): array
    {
        static $cached = [];
        $cache_key = __FUNCTION__ . 'categories';
        if (isset($cached[$cache_key])) {
            $categories = $cached[$cache_key];
        } else {
            $categories = (new self)->getAll(-1);;
            $cached[$cache_key] = $categories;
        }
        $result = [];
        foreach ($categories as $key => $category) {
            if ($type > -1) {
                if ($category->type != $type) {
                    continue;
                }
            }
            $item = [];
            if ($category->parent_id == $parent_id) {
                $item['id'] = $category->id;
                $item['parent_id'] = $category->parent_id;
                $item['name'] = $category->name;
                $item['image'] = $category->image;
                $item['href'] = Util::url_category($category);
                $item['type'] = 'category';
                $item['children'] = self::getAllMenuLink($category->id, $category->type);
                $result[$key] = $item;
            }
        }
        return $result;
    }

    public function getOneLink($cat_id): array
    {
        $category = $this->find($cat_id);
        $result = [];
        $result['name'] = $category->name;
        $result['href'] = Util::url_category($category);
        return $result;
    }

    public function getParentArray(): array
    {
        if (!empty($this->parents)) {
            return $this->parents;
        }

        $list_categories = self::where('status', 1)->orderBy('priority')->select('id', 'name', 'parent_id')->get();
        if ($list_categories) {
            foreach ($list_categories as $v) {
                $this->parents[$v->id] = $v->parent_id;
            }
        }

        return $this->parents;
    }

    public function getAllCatStr($cat_id = 0): array
    {
        $arr_cat = [];
        foreach ($this->parents as $k => $v) {
            if ($v == $cat_id) {
                $arr_cat[] = $k;
                $arr_cat[] = $this->getAllCatStr($k);
            }
        }
        return Arr::flatten($arr_cat);
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('category/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_category_' . $action,
                ];
            }
        }

        return $options;
    }

    //code dự án
    public function getChildrenCategories($type = self::CATEGORY_TYPE_POST)
    {
        $children = Category::active()
            ->language()
            ->where([
                ['parent_id', $this->id],
                ['type', $type],
            ])
            ->orderByDesc('priority')
            ->orderBy('name')
            ->get();

        if ($children->isEmpty()) {
            $children = Category::active()
                ->language()
                ->where([
                    ['parent_id', $this->parent_id],
                    ['type', $type],
                ])
                ->orderByDesc('priority')
                ->orderBy('name')
                ->get();
        }

        return $children;
    }


    public function getParentCategory()
    {
        $language = App::getLocale();

        $parent_id = data_get($this, 'parent_id', 0);

        if (!$parent_id) {
            return null;
        }

        return Category::where('status', 1)
            ->where('id', $parent_id)
            ->where('language', $language)
            ->first();
    }

    public static function getCategoryPostHome($limit_post = 6)
    {
        return Category::where('at_home', 1)
            ->language()
            ->active()
            ->where('type', Category::CATEGORY_TYPE_POST)
            ->orderBy('priority', 'asc')
            ->with([
                'posts' => function ($q) use ($limit_post) {
                    $q->orderBy('priority', 'desc')->latest()->take($limit_post)->with('categories');
                }
            ])
            ->take(13)
            ->get();
    }

}
