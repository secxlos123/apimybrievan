<?php

namespace App\Http\Middleware;

use Closure;

class IpWhiteList {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
        $ip = env('ACCESS_CLAS_IP','10.35.65.167');
        \Log::info("=================ENV IP=================");
        \Log::info($ip);
        \Log::info("=================CLIENT IP=================");
        \Log::info($request->ip());
        \Log::info("=================SERVER===================");
        \Log::info($request->server());
		if ($request->ip() != $ip) {
			return response()->json(['error' => 401, 'message' => 'Unauthorized action.'], 401);
		}
		return $next($request);
	}
}
