<?php

namespace App\Models;

use Cartalyst\Sentinel\Roles\EloquentRole as Model;
use Illuminate\Http\Request;

class Role extends Model
{
	/**
     * Get mutator for the "permissions" attribute.
     *
     * @param  mixed  $permissions
     * @return array
     */
    public function getPermissionsAttribute($permissions)
    {
        return $permissions ? json_decode($permissions, true) : (object) null;
    }

	/**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
    	return $query->where(function ($role) use ($request) {
    		$role->where("name", 'ilike', "%{$request->input('name')}%")
    			->orWhere("slug", 'ilike', "%{$request->input('slug')}%");
    	})->select(array_merge(['id'], $this->fillable));
    }
}