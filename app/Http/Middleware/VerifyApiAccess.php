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
        if (!is_null($request->route('type'))) $this->params = $request->route('type');
        if (!array_key_exists($this->params, $this->types)) {
            return response()->error(['message' => 'Halaman tidak di temukan.'], 404);
        }

        if ($request->hasHeader('Authorization')) {
            try {
                if (! $token = \JWTAuth::setRequest($request)->getToken()) {
                    return response()->error(['message' => 'Token tidak tersedia.', 'data' => (object) null], 401);
                }

                if (! $this->verify($request, $token)) {
                    return response()->error(['message' => 'Halaman tidak di temukan.'], 404);
                }
            } catch ( \Tymon\JWTAuth\Exceptions\TokenExpiredException $e ) {
                return response()->error( [
                    'message' => 'Token expired.'
                ], 404 );
            } catch ( \Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e ) {
                return response()->error( [
                    'message' => 'Your session has expired.'
                ], 400 );
            }
        }

        return $next($request);
    }
}
