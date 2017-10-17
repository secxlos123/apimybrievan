<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Property extends Model
{
    use Sluggable, SluggableScopeHelpers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'developer_id', 'city_id', 'name', 'address', 'category', 'latitude', 'longitude',
        'facilities', 'approved_by', 'pic_name', 'pic_phone', 'is_approved', 'description'
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getFacilitiesAttribute($facilities)
    {
        return htmlspecialchars_decode($facilities);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getDescriptionAttribute($description)
    {
        return htmlspecialchars_decode($description);
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
        $select = $request->has('dropdown') ? ['prop_id', 'prop_name', 'prop_category', 'prop_dev_id'] : ['*'];

        if ( ! $request->has('dropdown') )
            $query->with('propPhoto');

        return $query
            ->from('developer_properties_view_table')
            ->where(function ($property) use (&$request, $developerId) {

                /**
                 * Query for filter by prop_type.
                 */
                if ($request->has('types'))
                    $property->whereBetween('prop_types', explode('|', $request->input('types')));

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('prop_city_id'))
                    $property->where('prop_city_id', $request->input('prop_city_id'));

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('name'))
                    $property->where('prop_name', 'ilike', "%{$request->input('name')}%");

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('pic'))
                    $property->where('prop_pic_name', 'ilike', "%{$request->input('pic')}%");

                /**
                 * Query for filter by category.
                 */
                if ($request->has('category'))
                    $property->where('prop_category', $request->input('category'));

                /**
                 * Query for filter by range items.
                 */
                if ($request->has('price'))
                    $property->whereBetween('prop_price', explode('|', $request->input('price')));

                /**
                 * Query for filter by range items.
                 */
                if ($request->has('items'))
                    $property->whereBetween('prop_items', explode('|', $request->input('items')));

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
            ->select($select)
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

    /**
     * [scopeDistance description]
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  string  $lat    Latitude
     * @param  string  $lng    Longitude
     * @param  integer $radius Radius
     * @param  string  $type   Type for seach
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDistance($query, $lat, $lng, $radius = 100, $type = "km")
    {
        $type = ($type === "km") ? 6378.10 : 3963.17;
        $lat  = (float) $lat;
        $lng  = (float) $lng;
        $radius = (double) $radius;

        $distance = "( {$type}
            * acos( cos( radians( cast( {$lat} as double precision ) ) )
            * cos( radians( cast( latitude as double precision ) ) )
            * cos( radians( cast( longitude as double precision ) )
                 - radians( cast( {$lng} as double precision ) ) )
                 + sin( radians( cast( {$lat}  as double precision ) ) )
            * sin( radians( cast( latitude as double precision ) ) ) ) )";

        return $query
            ->selectRaw("{$distance} as distance")
            ->havingRaw("{$distance} <= {$radius}")
            ->groupBy( "id" )
            ->orderBy( 'distance', 'asc' );
    }

    /**
     * Get nearby property
     *
     * @param  Request $request
     * @return array
     */
    protected function nearby(Request $request)
    {
        $lat    = $request->get('lat', '');
        $long   = $request->get('long', '');
        $radius = $request->get('radius', 10);
        $type   = $request->get('type', 'km');
        $limit  = $request->get('limit', 6);
        $rawPrice = \DB::raw('(SELECT max(property_types.price) from property_types where property_types.property_id = properties.id) as price');

        $properties = $this->distance($lat, $long, $radius, $type)
               ->with(['photo', 'developer', 'city'])
               ->withCount(['propertyTypes as types', 'propertyItems as items'])
               ->addSelect([
                    'id', 'name', 'slug', 'latitude', 'longitude', 'category', 'pic_name',
                    'developer_id', 'pic_phone', 'city_id', $rawPrice
                ])
               ->limit($limit)
               ->get();

        $properties->transform(function ($property) {
            $data = [];
            foreach ($property->toArray() as $key => $value) {
                $key = str_replace('_count', '', $key);
                $data["prop_{$key}"] = $value;
            }
            $data['prop_developer_name'] = $data['prop_developer']['company_name'];
            $data['prop_photo'] = $data['prop_photo']['image'] ?: asset('img/noimage.jpg');
            $data['prop_city_name'] = ! is_null( $data['prop_city'] ) ? $data['prop_city']['name'] : '';
            unset( $data['prop_developer'], $data['prop_city'], $data['prop_distance'] );
            return $data;
        });

        return $properties;
    }
}