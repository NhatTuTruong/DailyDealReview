<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Shared API-key auth for the Affiliate Store Publisher endpoints.
 * Accepts the key via header X-API-Key, Authorization: Bearer, or ?api_key=.
 */
class AffiliateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $expected = config('affiliate.api_key');
        if (empty($expected)) {
            return response()->json(['ok' => false, 'code' => 'no_key', 'message' => 'Server API key is not configured.'], 500);
        }

        $provided = $request->header('X-API-Key');
        if (!$provided) {
            $auth = (string) $request->header('Authorization');
            if (stripos($auth, 'bearer ') === 0) {
                $provided = trim(substr($auth, 7));
            }
        }
        if (!$provided) {
            $provided = $request->query('api_key');
        }

        if (!$provided || !hash_equals((string) $expected, (string) $provided)) {
            return response()->json(['ok' => false, 'code' => 'bad_key', 'message' => 'Invalid or missing API key.'], 401);
        }

        return $next($request);
    }
}
