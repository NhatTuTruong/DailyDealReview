<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected string $redirectTo = '/home';

    public function showLoginForm()
    {
        if (Auth::guard('member')->check()) {
            // Nếu đã đăng nhập, chuyển hướng đến trang dashboard của member
            return redirect()->route('member_home');
        }

        $setting = Setting::getAllSetting();
        $setting['hide_menu_pc'] = true;
        $setting['no_footer'] = true;

        return view('frontend.member.login', compact('setting'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required',
        ]);

        $remember = (boolean)$request->get('remember');
        $user_name = $request->get('user_name');
        $password = $request->get('password');

        $member = Member::where('user_name', $user_name)->first();

        if ($member) {
            if ($member->status == 0) {
                return back()->withInput()->withErrors(['user_name' => 'Tài khoản chưa được kích hoạt']);
            }
            if (!Hash::check($password, $member->password)) {
                return back()->withInput()->withErrors(['user_name' => 'Thông tin đăng nhập không đúng']);
            }
            Auth::guard('member')->loginUsingId($member->id, $remember);
            return redirect()->route('member_home');
        } else {
            return back()->withInput()->withErrors(['user_name' => 'Tài khoản không tồn tại']);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    public function logout()
    {
        Auth::guard('member')->logout();
        return redirect()->route('member_login');
    }
}
