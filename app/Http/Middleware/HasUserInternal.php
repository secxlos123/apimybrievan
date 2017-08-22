<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Traits\AvailableType;

class HasUserInternal
{
    use AvailableType;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!is_null($request->user)) $role = $request->user->roles->first()->slug;

        if (!is_null($request->model)) $role = $request->model->roles->first()->slug;
        
        return in_array($role, $this->types['int'])
            ? $next($request)
            : response()->error(['message' => 'Halaman tidak ditemukan.'], 404);
    }
}
