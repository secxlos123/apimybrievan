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
        'surface_area'  => 'required',
        'certificate'   => 'required',
        'building_area' => 'required',
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

        return $query
            ->with('photos')
            ->where(function ($propertyType) use (&$request) {
                if ($request->has('property_id')) $propertyType->where('property_id', $request->input('property_id'));
                if ($request->has('certificate')) $propertyType->where('certificate', $request->input('certificate'));
            })
            ->where(function ($propertyType) use (&$request, &$query) {
                if ($request->has('search')) $query->search($request);
            })
            ->select(['id', 'property_id', 'name', 'surface_area', 'building_area', 'certificate', 'slug'])
            ->selectRaw('(select count(property_items.id) from property_items) as items')
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
            ->where('certificate', 'ilike', "%{$request->input('search')}%")
            ->orWhere('surface_area', 'ilike', "%{$request->input('search')}%")
            ->orWhere('building_area', 'ilike', "%{$request->input('search')}%")
            ->orWhere('name', 'ilike', "%{$request->input('search')}%");
    }
}
