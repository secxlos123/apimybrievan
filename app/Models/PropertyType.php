<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
