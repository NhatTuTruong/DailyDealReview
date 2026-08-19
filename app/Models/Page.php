<?php

namespace App\Models;

use App\Libs\Util;
use App\Traits\HasGlobalScopes;
use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Page extends Model
{
    use HasGlobalScopes;
    use HasImageCleanup;

    protected array $imageCleanupConfig = [
        'image'   => 'path',
        'content' => 'html',
    ];
    public static function makeListPage($selected_id = 0, $include_default = false)
    {
        $language = App::getLocale();
        $pages = Page::where('language', $language)->get(['id', 'name']);
        $html = '';

        if ($include_default) {
            $html .= "<option value='0'>__ROOT__</option>";
        }

        foreach ($pages as $page) {
            $selected = ($page->id == $selected_id) ? 'selected' : '';
            $html .= "<option value=\"$page->id\" $selected>" . $page->name . "</option>";
        }
        return $html;

    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($page) {
            Slug::deleteSlug(Slug::MODULE['PAGE'], $page->id);
        });

        static::saved(function ($page) {
            Slug::insertOrUpdateSlug($page->slug, Slug::MODULE['PAGE'], $page->id);
        });
    }

    public function getOtherPage()
    {
        return Page::where('id', '<>', $this->id)
            ->orderBy('priority')
            ->latest()->get();
    }

    public static function makeArrayListPage(): array
    {
        $language = App::getLocale();
        $pages = Page::where('status', 1)->where('language', $language)->get(['id', 'name']);
        $results = [];
        foreach ($pages as $page) {
            $results[$page->id] = $page->name;
        }
        return $results;
    }

    public function getUrl()
    {
        return route('page_content', $this->slug);
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete'] as $action) {
            if (Gate::allows('page/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_page_' . $action,
                ];
            }
        }

        return $options;
    }
}
