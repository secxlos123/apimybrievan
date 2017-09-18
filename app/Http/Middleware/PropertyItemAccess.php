<?php

namespace App\Http\Middleware;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Closure;

class PropertyItemAccess
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
        $propertyType = $request->route('property_type');

        if ($propType = PropertyType::findBySlug($propertyType)) {
            $request->merge(['property_type_id' => $propType->id ]);
        } else {
            throw new ModelNotFoundException;
        }

        return $next($request);
    }
}
