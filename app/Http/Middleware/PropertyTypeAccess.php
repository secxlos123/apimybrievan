<?php

namespace App\Http\Middleware;

use App\Models\Property;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Closure;

class PropertyTypeAccess
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
        $property = $request->route('property');

        if ($prop = Property::findBySlug($property)) {
            $request->merge(['property_id' => $prop->id ]);
        } else {
            throw new ModelNotFoundException;
        }

        return $next($request);
    }
}
