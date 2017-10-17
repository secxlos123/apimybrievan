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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_available' => 'integer'
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
        $select = $request->has('dropdown') ? ['id', 'address', 'price'] : array_merge(['id'], $this->fillable);
        
        if ( ! $request->has('dropdown') ) $query->with('photos');

        return $query
            ->with('propertyType')
            ->where(function ($item) use (&$request) {
                if ($request->has('property_type_id')) 
                    $item->where('property_type_id', $request->input('property_type_id'));

                if ($request->has('is_available')) 
                    $item->where('is_available', $request->input('is_available'));

                if ($request->has('status')) 
                    $item->where('status', $request->input('status'));

                if ($request->has('price')) 
                    $item->whereBetween('price', explode('|', $request->input('price')));
            })
            ->where(function ($item) use (&$request, &$query) {
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
            ->where('address', 'ilike', "%{$request->input('search')}%")
            ->orWhere(\DB::raw('CAST(price as varchar)'), 'ilike', "%{$request->input('search')}%")
            ->orWhereHas('propertyType', function ($type) use (&$request) {
                $type->where('property_types.name', 'ilike', "%{$request->input('search')}%");
            });
    }
}
