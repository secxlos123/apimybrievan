<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'price'   => 'required|numeric',
        'images.*'=> 'required|image|max:1024',
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
}
