<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'property_id', 'name', 'surface_area', 'building_area', 'price', 'electrical_power',
		'bathroom', 'bedroom', 'floors', 'carport', 'certificate', 
    ];

    /**
     * Get parent property of developer.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo( Property::class, 'property_id' );
    }

    /**
     * The relation to property types.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function propertyItems()
    {
        return $this->hasMany( PropertyItem::class );
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

        return $query->from('developer_properties_view_table')
            ->where(function ($property) use (&$request) {

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
                if ($request->has('items')) $developer->whereBetween('prop_items', explode('|', $request->input('items')));
            })
            ->where(function ($property) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where('prop_dev_id', $developerId)
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
            ->orWhere('prop_type_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('prop_city_name', 'ilike', "%{$request->input('search')}%");
    }
}
