<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyType extends Model
{
    use Sluggable, SluggableScopeHelpers;

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
     * The attributes that are rules for validations.
     *
     * @var array
     */
    public static $rules = [
        'name'  => 'required',
        'price' => 'required|numeric',
        'bathroom'  => 'required|numeric|between:0,4',
        'bedroom'   => 'required|numeric|between:0,4',
        'floors'    => 'required|numeric|between:0,4',
        'carport'   => 'required|numeric|between:0,4',
        'photos'    => 'required|array',
        'photos.*'  => 'required|image|max:1024',
        'property_id'   => 'required|exists:properties,id',
        'surface_area'  => 'required|numeric',
        'certificate'   => 'required',
        'building_area' => 'required|numeric',
        'electrical_power'  => 'required',
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
                'source' => ['property.name', 'name']
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
     * Get the properties photos.
     * @return     \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function photos()
    {
        return $this->morphMany( Photo::class, 'photoable' );
    }

    /**
     * Get lists of property type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  Request $request [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $select = $request->has('dropdown')
            ? ['id', 'name', 'building_area']
            : ['id', 'property_id', 'name', 'surface_area', 'building_area', 'certificate', 'slug', 'price'];

        $query
            ->where(function ($propertyType) use (&$request) {
                if ($request->has('property_id'))
                    $propertyType->where('property_id', $request->input('property_id'));
            
                if ($request->has('certificate'))
                    $propertyType->where('certificate', $request->input('certificate'));
            
                if ($request->has('surface_area'))
                    $propertyType->whereBetween('surface_area', explode('|', $request->input('surface_area')));
            
                if ($request->has('building_area'))
                    $propertyType->whereBetween('building_area', explode('|', $request->input('building_area')));
            
                if ($request->has('proyek_type'))
                    $propertyType->where('name', 'ilike', "%{$request->input('proyek_type')}%");
            })
            ->where(function ($propertyType) use (&$request, &$query) {
                if ($request->has('search')) $query->search($request);
            
                if ($user = $request->user()) {
                    if ($user->inRole('developer'))
                        $query->developerOwned($request->user()->developer->id);
                }
            })
            ->select($select)
            ->orderBy($sort[0], $sort[1]);

        if ( ! $request->has('dropdown') ) {
            $query->with('photos')
                ->addSelect(\DB::raw('(select count(property_items.id) from property_items where property_types.id = property_items.property_type_id) as items'));
        }

        return $query;
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
            ->where('certificate', 'ilike', "%".$request->input('search')."%")
            ->orWhere('name', 'ilike', "%".$request->input('search')."%");
    }

    /**
     * Get property types by developer
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  integer $developerId
     * @param  integer|null $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDeveloperOwned($query, $developerId, $params = [])
    {
        if ( ! empty($params) && in_array('property_type_id', array_keys($params)) ) {
            $query->where('id', $params['property_type_id']);
        }

        return $query->whereHas('property', function ($property) use ($developerId) {
            return $property->where('developer_id', $developerId);
        });
    }
}
