<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PropertyType extends Model implements AuditableContract
{
    use Sluggable, SluggableScopeHelpers , Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'property_id', 'name', 'surface_area', 'building_area', 'price', 'electrical_power',
		'bathroom', 'bedroom', 'floors', 'carport', 'certificate',
    ];

    protected $appends = [
      'photos'
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
        'photos.*'  => 'required|image|max:5024',
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
     * Get property item
     * @return \App\Models\PropertyItem
     */
    public function getPhotosAttribute()
    {
      return $this->photos()->get();
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
                if ($request->has('without_independent')){
                    if ($request->without_independent) {
                    $data = \DB::table('properties')->select('id')->whereNotIn('developer_id',[1])->get();
                    $dataid = array();
                    if (count($data)>0) {
                        foreach ($data as $key => $value) {
                            foreach ($value as $key2 => $value2) {
                                $dataid[]= $value2;
                            }
                        }
                    }
                    $propertyType->whereIn('property_id',$dataid);
                    }
                }
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
            ->select($select)->selectRaw('(select properties.is_approved from properties where property_types.property_id = properties.id)')
            ->orderBy($sort[0], $sort[1]);

        if ( ! $request->has('dropdown') ) {
            $query->with('photos')
                ->addSelect(\DB::raw('(select count(property_items.id) from property_items where property_types.id = property_items.property_type_id) as items'));
        }

        return $query;
    }

    /**
     * Get list proptype without non-kerjasama
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param  Request $request [description]
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsPropType($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $select = ['id', 'name', 'building_area'];
        $listPropType = $query
                    ->where(function($listPropType) use ($request, $query){
                        if($request->has('search')){
                        $listPropType->where(\DB::raw('lower(name)'), 'like', '%'.$request->input('search').'%');
                        }
                    })
                    ->where(function($listPropType) use ($request, $query){
                        $listPropType->where('name', 'not like', 'Non Kerja Sama');
                    })
                    ->select($select)
                    ->orderBy($sort[0], $sort[1]);

        return $listPropType;
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
