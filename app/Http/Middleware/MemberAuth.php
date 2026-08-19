<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MemberAuth
{
    public function handle($request, Closure $next)
    {

        if (!Auth::guard('member')->check()) {
            return redirect()->to(route('member_login'));
        }

        return $next($request);
    }
}
