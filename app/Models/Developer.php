<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Developer extends Model
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'city_id', 'company_name', 'address', 'summary',
        'created_by', 'approved_by', 'is_approved', 'pks_number', 'plafond'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'image' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'user' ];

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'dev_id' );
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo( User::class, 'approved_by' );
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo( User::class, 'created_by' );
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }

    /**
     * The relation to properties.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany( Property::class );
    }

    /**
     * Get all of the property items for the property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function propertyTypes()
    {
        return $this->hasManyThrough( PropertyType::class, Property::class );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['dev_id', 'asc'];

        return $query->from('developers_view_table')
            ->where(function ($developer) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($developer) use ($request) {

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('city_id')) $developer->where('city_id', $request->input('city_id'));

                /**
                 * Query for filter by range project.
                 */
                if ($request->has('project')) $developer->whereBetween('project', explode('|', $request->input('project')));
            })
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query for search user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        return $query
            ->where('company_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('email', 'ilike', "%{$request->input('search')}%")
            ->orWhere('phone_number', 'ilike', "%{$request->input('search')}%")
            ->orWhere('city_name', 'ilike', "%{$request->input('search')}%");
    }

    /**
     * Get user developer avatar url.
     *
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->user->image;
    }
}