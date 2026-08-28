<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
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
        $clsPost = new Post();
        View::share('selectedMainMenu', $this->selectedMainMenu);
        $current_locale = App::getLocale() == config('app.fallback_locale') ? '' : App::getLocale();

        $list_most_view_post = $clsPost->getListMostViewPost(6);
        $banner_posts = $clsPost->getListLatestPost(3);

        //Code dự án
        $setting = Setting::getAllSetting();
        $top_menu = Menu::getAllMenuLink('top');
        $main_menu = Menu::getAllMenuLink();
        $footer_menu = Menu::getAllMenuLink('footer');

        $share = [
            'top_menu' => $top_menu,
            'main_menu' => $main_menu,
            'footer_menu' => $footer_menu,
            'list_most_view_post' => $list_most_view_post,
            'banner_posts' => $banner_posts,
        ];

        View::share('share', $share);
        View::share('setting', $setting);
        View::share('current_locale', $current_locale);
        //End code dự án

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
