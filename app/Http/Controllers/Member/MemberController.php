<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function index()
    {
        //SEO MOZ Cấu hình SEO
        $setting = Setting::getAllSetting();
        $setting['hide_menu_pc'] = true;
        $setting['no_footer'] = true;
        $setting['heading'] = 'Chương trình "Lên xe sang - đón ngàn lộc"';
        $setting['meta_title'] = $setting['heading'];
        $setting['menu_active'] = 'Trang chủ';

        return view('frontend.member.index', compact('setting'));
    }

    public function profile()
    {
        $member = Auth::guard('member')->user();
        $language = App::getLocale();

        //SEO MOZ Cấu hình SEO
        $setting = Setting::getAllSetting();
        $setting['hide_menu_pc'] = true;
        $setting['no_footer'] = true;
        $setting['heading'] = 'THÔNG TIN NGƯỜI DÙNG';
        $setting['meta_title'] = 'Thông tin người dùng';
        $setting['menu_active'] = 'Thông tin người dùng';

        return view('frontend.member.profile', compact('setting', 'member'));
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $member = Auth::guard('member')->user();

            if (!$member || !Hash::check($request->current_password, $member->password)) {
                $validator->errors()->add('password', 'Mật khẩu hiện tại không đúng');
            }

            if (!preg_match(User::PATTERN_PASSWORD['PATTERN'], $request->new_password)) {
                $validator->errors()->add('password', User::PATTERN_PASSWORD['MESSAGE']);
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $member = Auth::guard('member')->user();
            $member->password = Hash::make($request->new_password);
            $member->save();

        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
                'errors' => ['error' => ['Đã xảy ra lỗi, Vui lòng thử lại sau']],
            ], 422);
        }

        return response()->json(['success' => true, 'message' => 'Đổi mật khẩu thành công!'], 201);
    }
}
