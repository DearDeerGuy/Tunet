<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next, ...$lvl)
    {
        $user = Auth::user();
        $lvl = array_pop($lvl);
        if (!$user || $user->admin_lvl < (int)$lvl)
            return response()->json([
                'message' => "Нет доступа."
            ], 403);

        return $next($request);
    }
}
