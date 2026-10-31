<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        if (BlockedIp::isBlocked($request->ip())) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }

    private function shouldBypass(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if ($path === 'up') {
            return true;
        }

        return (bool) preg_match('#(^|/)(backend|login|logout)(/|$)#i', $path);
    }
}
