<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        //            $guards = empty($guards) ? [null] : $guards;
        //
        //            foreach ($guards as $guard) {
        //                if (Auth::guard($guard)->check()) {
        //                    return redirect(RouteServiceProvider::HOME);
        //                }
        //            }
        //
        //            return $next($request);
        return $next($request);
    }
}
