<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'developer_id', 'city_id', 'name', 'address', 'category',
        'latitude', 'longitude', 'facilities',
    ];

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
}