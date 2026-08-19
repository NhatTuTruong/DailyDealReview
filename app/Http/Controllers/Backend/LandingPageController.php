<?php

namespace App\Modules\Setting\Controllers;

namespace App\Http\Controllers\Backend;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;


class LandingPageController extends Controller
{
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
        $this->selectedMainMenu = 'landing_page';

        parent::__construct();

        if (!Gate::allows('lading_page')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    public function home()
    {
        if (!Gate::allows('lading_page/home')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('home');


        $home_data = Setting::getSettingByKey('home_data');
        $home_data = unserialize($home_data);
        return view('backend.landing_page.home', compact('home_data'));
    }
    public function job()
    {
        if (!Gate::allows('lading_page/job')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }

        $this->selectedSubMenu('job');


        $job_data = Setting::getSettingByKey('job_data');
        $job_data = unserialize($job_data);
        return view('backend.landing_page.job', compact('job_data'));
    }

    public function save(Request $request, $key)
    {
        $language = App::getLocale();
        $keys = $request->get($key);
        $keys = serialize($keys);
        if (Setting::check_exists_skey($key)) {
            //Nếu skey đã tồn tại thì cập nhật svalue
            Setting::where('skey', $key)->where('language', $language)->update(['svalue' => $keys]);
        } else {
            Setting::insert(["skey" => $key, "svalue" => "$keys", "language" => "$language"]);
        }
        return redirect()->back();
    }
}
