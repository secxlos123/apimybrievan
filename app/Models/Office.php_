<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Office extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'address', 'city_id'
    ];

    /**
     * Scope a query to get lists of offices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
    	return $query->where(function ($office) use ($request) {
    		$office->where("name", 'ilike', "%{$request->input('name')}%");
            if ($request->input('city_id')) $office->whereCityId($request->input('city_id'));
    	})->orderBy('name', 'asc')->select($this->fillable);
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany( UserDetail::class );
    }
}
