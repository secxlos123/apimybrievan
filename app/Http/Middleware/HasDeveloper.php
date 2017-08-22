<?php

namespace App\Http\Middleware;

use Closure;

class HasDeveloper
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $developer = $request->developer ?: $request->model;

        return $developer->inRole('developer')
            ? $next($request)
            : response()->error(['message' => 'Halaman tidak ditemukan.'], 404);
    }
}
