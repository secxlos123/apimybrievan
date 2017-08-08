<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\VerifyUser;

class VerifyApiAccess
{
    use VerifyUser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($request->route()->parameters()['type'])) $this->params = $request->route()->parameters()['type'];
        if (!array_key_exists($this->params, $this->types)) {
            return response()->error(['message' => 'Halaman tidak di temukan.'], 404);
        }

        if ($request->hasHeader('Authorization')) {
            if (! $token = \JWTAuth::setRequest($request)->getToken()) {
                return response()->error(['message' => 'Token tidak tersedia.', 'data' => (object) null], 401);
            }

            if (! $this->verify($request, $token)) {
                return response()->error(['message' => 'Halaman tidak di temukan.'], 404);
            }
        }

        return $next($request);
    }
}
