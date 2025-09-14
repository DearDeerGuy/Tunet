<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
{
    public function handle($request, Closure $next)
    {
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                Auth::setUser($user);
            }
        }
        return $next($request);
    }
}
