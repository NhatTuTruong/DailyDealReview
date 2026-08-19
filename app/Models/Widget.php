<?php

namespace App\Models;

use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class Widget extends Model
{
    use HasImageCleanup;

    protected $table = 'widgets';

    protected array $imageCleanupConfig = [
        'image' => 'path',
    ];

    public static function getAll()
    {
        $language = App::getLocale();
        return Widget::where('language', $language)
            ->where('status', 1)
            ->orderBy('position')
            ->orderBy('priority')
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function getAllWidgetHome(): array
    {
        $widgets = self::getAll();
        $home_widgets = [];
        foreach ($widgets as $widget) {
            if ($widget->position == 'HOME_BANNER') {
                $home_widgets['home_banner'][] = $widget;
            }
            if ($widget->position == 'HOME_SIDEBAR') {
                $home_widgets['home_sidebar'][] = $widget;
            }
        }
        return $home_widgets;
    }

    public static function getByPosition($position = '', $cat_id = 0)
    {
        $language = App::getLocale();
        if ($position == '') {
            $widget = [];
        } else {
            $query = Widget::where('position', $position)
                ->where('language', $language);
            if ($cat_id > 0) {
                $query->where('cat_id', $cat_id);
            }
            return $query->orderBy('priority')
                ->orderBy('id', 'desc')
                ->limit(10)->get();
        }
        return $widget;
    }

    public static function getOneByPosition($position = '')
    {
        $language = App::getLocale();
        return Widget::where('position', $position)->where('language', $language)->first();
    }

    public static function makeOptionColumnButton(): array
    {
        $options = [];

        foreach (['edit', 'delete', 'clone'] as $action) {
            if (Gate::allows('widget/' . $action)) {
                $options[$action] = [
                    'route' => 'backend_widget_' . $action,
                ];
            }
        }

        return $options;
    }
}
