<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PropertyItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_type_id', 'address', 'price', 'is_available', 'status',
    ];

    /**
     * The attributes that are rules for validations.
     *
     * @var array
     */
    public static $rules = [
        'address' => 'required',
        'status'  => 'required|in:new,second',
        'price'   => 'required|numeric',
        'photos.*'=> 'image|max:1024',
    ];

    /**
     * Get parent property of developer.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function propertyType()
    {
        return $this->belongsTo( PropertyType::class, 'property_type_id' );
    }

    /**
     * Get the properties photos.
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
                if ($request->has('property_type_id')) 
                    $propertyType->where('property_type_id', $request->input('property_type_id'));

                if ($request->has('is_available')) 
                    $propertyType->where('is_available', $request->input('is_available'));

                if ($request->has('status')) 
                    $propertyType->where('status', $request->input('status'));

                if ($request->has('price')) 
                    $propertyType->whereBetween('price', explode('|', $request->input('price')));
            })
            ->where(function ($propertyType) use (&$request, &$query) {
                if ($request->has('search')) $query->search($request);
            })
            ->whereHas('propertyType', function ($type) use (&$request) {
                if ($user = $request->user()) {
                    if ($user->inRole('developer')) {
                        $developerId = $user->developer->id;
                        $type->developerOwned($developerId);
                    }
                }
            })
            ->select(array_merge(['id'], $this->fillable))
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
