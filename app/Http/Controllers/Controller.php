<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use App\Models\Menu;

abstract class Controller
{
    use AuthorizesRequests;

    protected string $selectedMainMenu = '';
    const string MESSAGE_UNAUTHORIZED = 'This action is unauthorized.';

    public function __construct()
    {
        View::share('selectedMainMenu', $this->selectedMainMenu);
        $current_locale = App::getLocale() == config('app.fallback_locale') ? '' : App::getLocale();
        View::share('current_locale', $current_locale);

        if ($this->shouldShareFrontendData()) {
            $this->shareFrontendData();
        }
    }

    protected function shouldShareFrontendData(): bool
    {
        $request = request();
        if (!$request) {
            return false;
        }

        $adminPrefix = trim((string) config('cms.prefix_admin', 'backend'), '/');

        return !$request->is($adminPrefix, $adminPrefix . '/*');
    }

    protected function shareFrontendData(): void
    {
        $locale = App::getLocale();
        $cacheKey = 'frontend_share_v1_' . $locale;

        $share = Cache::remember($cacheKey, 300, function () {
            $clsPost = new Post();

            return [
                'top_menu' => Menu::getAllMenuLink('top'),
                'main_menu' => Menu::getAllMenuLink(),
                'footer_menu' => Menu::getAllMenuLink('footer'),
                'list_most_view_post' => $clsPost->getListMostViewPost(6),
                'list_hot_post_gallery' => $clsPost->getListPostHotWithImage(6),
                'banner_posts' => $clsPost->getListLatestPost(3),
            ];
        });

        View::share('share', $share);
        View::share('setting', Setting::getAllSetting());
    }

    protected function selectedSubMenu($menuId): void
    {
        View::share('selectedSubMenu', $menuId);
    }

    public function responseJsonBadRequest($data = [], $message = 'BadRequest')
    {
        return $this->responseCommonJson(400, $message, $data);
    }

    public function responseJsonOk($data = [], $message = 'ok')
    {
        return $this->responseCommonJson(200, $message, $data);
    }

    public function responseJsonNotAllowed($data = [], $message = 'NotAllowed')
    {
        return $this->responseCommonJson(403, $message, $data);
    }

    protected function responseCommonJson($code, $message, $data)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code, [], JSON_PRETTY_PRINT);
    }
}
