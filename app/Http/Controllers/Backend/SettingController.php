<?php

namespace App\Modules\Setting\Controllers;

namespace App\Http\Controllers\Backend;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;


class SettingController extends Controller
{
    private Setting $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
        $this->selectedMainMenu = 'setting';

        parent::__construct();

        if (!Gate::allows('setting')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function general()
    {
        if (!Gate::allows('setting/general')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }


        $this->selectedSubMenu('general');
        $settings = Setting::getAllSetting();

        $option_pages = Page::makeListPage($settings['page_rule'] ?? 0);

        return view('backend.setting.general', compact('settings', 'option_pages'));
    }

    public function social()
    {
        if (!Gate::allows('setting/social')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('social');
        $settings = Setting::getAllSetting();
        return view('backend.setting.social', compact('settings'));
    }

    public function seo()
    {
        if (!Gate::allows('setting/seo')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('seo');
        $settings = Setting::getAllSetting();
        return view('backend.setting.seo', compact('settings'));
    }

    public function ads()
    {
        if (!Gate::allows('setting/ads')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('ads');
        $settings = Setting::getAllSetting();
        return view('backend.setting.ads', compact('settings'));
    }

    public function content()
    {
        if (!Gate::allows('setting/content')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('content');
        $settings = Setting::getAllSetting();
        return view('backend.setting.content', compact('settings'));
    }


    public function author()
    {
        if (!Gate::allows('setting/author')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('setting_author');
        $settings = Setting::getAllSetting();
        return view('backend.setting.author', compact('settings'));
    }

    public function ai()
    {
        if (!Gate::allows('setting/ai')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('setting_ai');
        $settings = Setting::getAllSetting();
        return view('backend.setting.ai', compact('settings'));
    }

    public function save(Request $request)
    {
        $language = App::getLocale();
        $arrListKey = $request->settings;
        if (!isset($arrListKey['noindex'])) {
            $arrListKey['noindex'] = 0;
        }
        if (!isset($arrListKey['allow_search_store'])) {
            $arrListKey['allow_search_store'] = 0;
        }
        foreach ($arrListKey as $skey => $svalue) {
            if (Setting::check_exists_skey($skey)) {
                //Nếu skey đã tồn tại thì cập nhật svalue
                Setting::where('skey', $skey)->where('language', $language)->update(['svalue' => $svalue]);
            } else {
                Setting::insert(["skey" => "$skey", "svalue" => "$svalue", "language" => "$language"]);
            }
        }
        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');

    }
}
