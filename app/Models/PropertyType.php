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
        'images.*'  => 'required|image|max:1024',
        'surface_area'  => 'required',
        'certificate'   => 'required',
        'building_area' => 'required',
        'electrical_power'  => 'required',
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
     * Get the properties photos.
     */
    public function photos()
    {
        return $this->morphMany( Photo::class, 'photoable' );
    }
}
