<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Libs\Util;
use App\Models\TrackingFootPrint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TrackingController extends Controller
{
    public function trackingFootprint(Request $request)
    {
        $validated = $request->validate([
            'track_ukey' => 'required|string',
            'request' => 'required|url',
            'referer' => 'nullable|url',
            'user_agent' => 'nullable|string',
        ]);

        $ip = $request->ip();
        $country_info = Util::getCountryFromIp($ip);
        $key = 'tracking:' . $ip;

        // Cho phép tối đa 100 request mỗi phút
        $executed = RateLimiter::attempt(
            $key,
            100,
            function () use ($country_info, $ip, $validated, $request) {
                TrackingFootPrint::create([
                    'ukey' => $validated['track_ukey'],
                    'request' => $validated['request'],
                    'referer' => $validated['referer'] ?? null,
                    'ip' => $ip,
                    'country' => $country_info['country'] ?? null,
                    'country_code' => $country_info['country_code'] ?? null,
                    'user_agent' => $validated['user_agent'] ?? $request->userAgent(),
                ]);
            }
        );

        if (!$executed) {
            return response()->json(['status' => 'rate_limited'], 200);
        }

        return response()->json(['status' => 'ok']);
    }


}
