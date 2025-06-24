<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$lvl)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Нет доступа.');
        }



        if ((int)$lvl <= $user->admin_lvl) {
            abort(403, 'Нет доступа.');
        }

        return $next($request);
    }
}
