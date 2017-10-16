<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'photoable_id',
        'photoable_type',
        'id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image'
    ];

    /**
     * Get all of the owning photoable models.
     */
    public function photoable()
    {
        return $this->morphTo();
    }

    /**
     * Get path to url for the user.
     *
     * @return string
     */
    public function getImageAttribute()
    {
        switch ($this->attributes['photoable_type']) {
            case 'App\Models\Property': $disk = 'properties'; break;
            case 'App\Models\PropertyType': $disk = 'types'; break;
            case 'App\Models\PropertyItem': $disk = 'units'; break;
            default: $disk = 'uploads'; break;
        }
        
        return \Storage::disk($disk)->url($this->attributes['path']);
    }
}
