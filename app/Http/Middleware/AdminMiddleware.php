<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next, ...$lvl)
    {
        $user = Auth::user();
        if ($user->tariff_end_date <= now()) {
            $user->tariff_end_date = null;
            $user->tariff_id = null;
            $user->save();
        }

        $lvl = array_pop($lvl);
        if (!$user || $user->admin_lvl < (int) $lvl)
            return response()->json([
                'message' => "Відказано в доступі"
            ], 403);

        return $next($request);
    }
}
