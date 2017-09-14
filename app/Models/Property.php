<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Http\Request;

class Property extends Model
{
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'developer_id', 'city_id', 'name', 'address', 'category', 'latitude', 'longitude',
        'facilities', 'approved_by', 'pic_name', 'pic_phone', 'is_approved'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => ['developer.company_name', 'name']
            ]
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get parent property of developer.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function developer()
    {
        return $this->belongsTo( Developer::class, 'developer_id' );
    }

    /**
     * Get parent property of developer.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo( User::class, 'approved_by' );
    }

    /**
     * Get parent property of city.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }

    /**
     * The relation to property types.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propertyTypes()
    {
        return $this->hasMany( PropertyType::class );
    }

    /**
     * Get all of the property items for the property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function propertyItems()
    {
        return $this->hasManyThrough( PropertyItem::class, PropertyType::class );
    }

    /**
     * Get the properties photo.
     */
    public function photo()
    {
        return $this->morphOne( Photo::class, 'photoable' );
    }

    /**
     * Get the properties photo.
     */
    public function propPhoto()
    {
        return $this->morphOne( Photo::class, 'photoable', null, null, 'prop_id' );
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request, $developerId)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['prop_id', 'asc'];

        return $query
            ->with('propPhoto')
            ->from('developer_properties_view_table')
            ->where(function ($property) use (&$request, $developerId) {

                /**
                 * Query for filter by prop_type.
                 */
                if ($request->has('prop_type_id')) $property->where('prop_type_id', $request->input('prop_type_id'));

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('prop_city_id')) $property->where('prop_city_id', $request->input('prop_city_id'));

                /**
                 * Query for filter by range items.
                 */
                if ($request->has('items')) $property->whereBetween('prop_items', explode('|', $request->input('items')));

                /**
                 * Query for filter by developer or user login.
                 */
                if ($developerId) $property->where('prop_dev_id', $developerId);
                if ($request->has('dev_id')) $property->where('prop_dev_id', $request->input('dev_id'));
            })
            ->where(function ($property) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
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
            ->where('prop_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('prop_pic_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('prop_pic_phone', 'ilike', "%{$request->input('search')}%")
            ->orWhere('prop_city_name', 'ilike', "%{$request->input('search')}%");
    }
}