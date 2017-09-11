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
    public function getPathAttribute()
    {
        return \Storage::disk('properties')->url($this->attributes['path']);
    }
}
