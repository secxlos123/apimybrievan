<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Property extends Model implements AuditableContract
{
    use Sluggable, SluggableScopeHelpers , Auditable ;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'developer_id', 'city_id', 'name', 'address', 'category', 'latitude', 'longitude',
        'facilities', 'approved_by', 'pic_name', 'pic_phone', 'is_approved', 'description', 'pks_number', 'region_id', 'region_name','prop_id_bri'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $appends = [
      'category_name',
      'city',
      'propertyTypes',
      'propertyItems',
      'photos'
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
     * Get the Name Category for Property.
     *
     * @return string
     */
    public function getCategoryNameAttribute()
    {
        switch ($this->category) {
            case '1':
                $name = "Rumah Tapak";
                break;
            case '2':
                $name = "Rumah Susun/Apartment";
                break;
            case '3':
                $name = "Rumah Toko" ;
                break;
            case '4':
                $name = "Non Kerja Sama" ;
                break;

            default:
                $name = "Tidak Terdaftar";
                break;
        }
        return $name;
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

    public function getCityAttribute()
    {
      return $this->city()->first();
    }

    /**
     * Get property types
     * @return \App\Models\PropertyType
     */
    public function getPropertyTypesAttribute()
    {
      return $this->propertyTypes()->get();
    }

    /**
     * Get property item
     * @return \App\Models\PropertyItem
     */
    public function getPropertyItemsAttribute()
    {
      return $this->propertyItems()->get();
    }

    /**
     * Get property item
     * @return \App\Models\PropertyItem
     */
    public function getPhotosAttribute()
    {
      return $this->photo()->get();
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
                 * Query for filter by prop_types.
                 */
                if ($request->has('prop_types'))
                    $property->where('prop_types', $request->input('prop_types'));


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
                 * Filter bedroom
                 * @author erwan.akse@wgs.co.id
                 */
                if ($request->has('bedroom')) {
                    $id = $request->input('bedroom');
                    if ($id > 3) {
                        $property->whereRaw("prop_id in (select property_id from property_types where bedroom >= ?) ",array($id));
                    }else{
                        $property->whereRaw("prop_id in (select property_id from property_types where bedroom = ?) ",array($id));
                    }
                }

                /**
                 * Filter bathroom
                 * @author erwan.akse@wgs.co.id
                 */
                if ($request->has('bathroom')) {
                    $id = $request->input('bathroom');
                    if ($id > 3) {
                        $property->whereRaw("prop_id in (select property_id from property_types where bathroom > ?) ",array($id));
                    }else{
                        $property->whereRaw("prop_id in (select property_id from property_types where bathroom = ?) ",array($id));
                    }
                }

                /**
                 * Filter carport
                 * @author erwan.akse@wgs.co.id
                 */
                if ($request->has('carport')) {
                    $id = $request->input('carport');
                    if ($id > 0) {
                        $property->whereRaw("prop_id in (select property_id from property_types where carport >= ? ) ",array($id));
                    }else{
                        $property->whereRaw("prop_id in (select property_id from property_types where carport = ? ) ",array($id));
                    }
                }

                /**
                 * Filter surface_area
                 * @author erwan.akse@wgs.co.id
                 */
                if ($request->has('surface_area')) {
                    $data = explode('|', $request->input('surface_area'));
                    $property->whereRaw("prop_id in (select property_id from property_types where surface_area between ? and ?) ",$data);
                }

                /**
                 * Filter building area
                 * @author erwan.akse@wgs.co.id
                 */
                if ($request->has('building_area')) {
                    $data = explode('|', $request->input('building_area'));
                    $property->whereRaw("prop_id in (select property_id from property_types where building_area between ? and ?) ",$data);
                }

                /**
                 * Query for filter by range items.
                 */
                if ($request->has('without_independent')){
                    if ($request->without_independent) {
                        $property->where('bri', '!=', '1');
                    }
                }

                /**
                 * Query for filter by developer or user login.
                 */
                if ($developerId)
                    {
                        $property->where('prop_dev_id', $developerId);
                        if ($request->has('is_approved')) {
                            $property->where('is_approved',false);
                        }
                    }
                else{
                         $property->where('is_approved',true);
                     }
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
        $radius = ((double) $radius) + 30;

        $distance = "round( CAST( ( {$type}
            * acos( cos( radians( cast( {$lat} as double precision ) ) )
            * cos( radians( cast( latitude as double precision ) ) )
            * cos( radians( cast( longitude as double precision ) )
                 - radians( cast( {$lng} as double precision ) ) )
                 + sin( radians( cast( {$lat}  as double precision ) ) )
            * sin( radians( cast( latitude as double precision ) ) ) ) ) as numeric), 2)";

        // $distance = "round( CAST( ( {$type}
        //     * acos( sin( radians( cast( {$lat} as double precision ) ) )
        //     * sin( radians( cast( latitude as double precision ) ) )
        //     * cos( radians( cast( longitude as double precision ) )
        //          - radians( cast( {$lng} as double precision ) ) )
        //          + cos( radians( cast( {$lat}  as double precision ) ) )
        //     * cos( radians( cast( latitude as double precision ) ) ) ) ) as numeric), 2)";

        $query = $query
            ->selectRaw("{$distance} as distance")
            ->groupBy( "properties.id" )
            ->orderBy( 'distance', 'asc' );

        if ($radius > -1) {
            $query = $query->havingRaw("{$distance} <= {$radius}");
        }

        return $query;
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
        $radius = $request->get('radius', 30);
        $type   = $request->get('type', 'km');
        $limit  = $request->get('limit', 6);
        $rawPrice = \DB::raw('(SELECT max(property_types.price) from property_types where property_types.property_id = properties.id) as price');

        $properties = $this->distance($lat, $long, $radius, $type)
                ->with(['photo', 'developer', 'city'])
                ->withCount(['propertyTypes as types', 'propertyItems as items'])
                ->addSelect([
                    'properties.id', 'name', 'slug', 'latitude', 'longitude', 'category', 'pic_name', 'properties.address',
                    'developer_id', 'pic_phone', 'properties.city_id', $rawPrice
                ])
                ->leftJoin('developers','developers.id','=','properties.developer_id')
                ->where( function($query) {
                    return $query->whereNotIn('developers.dev_id_bri',['1'])
                        ->orWhereNull('developers.dev_id_bri');
                })
                ->where('properties.is_approved', '=', true)
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
            unset( $data['prop_developer'], $data['prop_city']);
            return $data;
        });

        return $properties;
    }

    /**
     * Get distance
     *
     * @param  integer $id
     * @param  integer $long
     * @param  integer $lat
     * @return array
     */
    public static function getDistance( $id, $long, $lat )
    {
        $data = static::select(['longitude', 'latitude'])
            ->find($id);

        $distance = ( 6378.10
            * acos( cos( deg2rad( $lat ) )
            * cos( deg2rad( $data->latitude ) )
            * cos( deg2rad( $data->longitude )
                 - deg2rad( $long ) )
                 + sin( deg2rad( $lat ) )
            * sin( deg2rad( $data->latitude ) ) ) );

        return round($distance, 2);
    }

    /**
     * Get property attribute
     * @return array
     */
    public function getNewestPropertyAttribute()
    {
        return [
            'property_name' => $this->property_name,
            'city'          => $this->cities,
            'pic_name'      => $this->pic_name,
            'pic_phone'     => $this->pic_phone,
            'property_type' => "Tipe ".$this->building_area,
            'property_unit' => $this->unit_property
        ];
    }

    /**
     * Get list new property
     *
     * @param  integer $cityId
     * @return collection
     */
    public function getNewestProperty($cityId)
    {
        $condition = empty($cityId) ? false : true;

        $data = Property::select(
                DB::raw("count(property_items.id) as unit_property"),
                "properties.name as property_name",
                "cities.name as cities",
                "properties.pic_name",
                "properties.pic_phone",
                "property_types.building_area"
            )
            ->join('property_types', 'property_types.property_id', '=', 'properties.id')
            ->join('property_items', 'property_items.property_type_id', '=', 'property_types.id')
            ->join('cities', 'cities.id', '=', 'properties.city_id')
            ->when($condition, function($query) use ($cityId){
                return $query->where('city_id', $cityId);
            })
            ->groupBy(
                'properties.id',
                'properties.name',
                'cities.name',
                'properties.pic_name',
                'properties.pic_phone',
                "property_types.building_area"
            )
            ->orderBy('properties.created_at', 'desc')
            ->get()->pluck('newestProperty');

        return $data;
    }

    public function getChartAttribute()
    {
        return [
            'month'  => $this->month,
            'month2' => $this->month2,
            'value'  => $this->value,
        ];
    }

    public function chartNewestProperty($startChart, $endChart)
    {
        if(!empty($startChart) && !empty($endChart)){
            $startChart = date("01-m-Y",strtotime($startChart));
            $endChart   = date("t-m-Y", strtotime($endChart));

            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($startChart) && !empty($endChart)){
            $now        = new \DateTime();
            $startChart = $now->format('Y-m-d h:i:s');

            $endChart = date("t-m-Y", strtotime($endChart));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($endChart) && !empty($startChart)){
            $now      = new \DateTime();
            $endChart = $now->format('Y-m-d h:i:s');

            $startChart = date("01-m-Y",strtotime($startChart));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $filter = true;
        }else{
            $filter = false;
        }

        $data = Property::select(
                    DB::raw("count(properties.id) as value"),
                    DB::raw("to_char(properties.created_at, 'TMMonth YYYY') as month"),
                    DB::raw("to_char(properties.created_at, 'MM YYYY') as month2"),
                    DB::raw("to_char(properties.created_at, 'YYYY MM') as order")
                )
                ->when($filter, function ($query) use ($startChart, $endChart){
                    return $query->whereBetween('properties.created_at', [$startChart, $endChart]);
                })
                ->groupBy('month', 'month2', 'order')
                ->orderBy("order", "asc")
                ->get()
                ->pluck("chart");
        return $data;
    }
}