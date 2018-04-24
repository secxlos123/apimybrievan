<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Photo extends Model implements AuditableContract
{
    use Auditable;
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
            case 'App\Models\Developer': $disk = 'avatars'; break;
            default: $disk = 'uploads'; break;
        }
        
        return url('files'.'/'.$disk.'/'.$this->attributes['path']);
    }
}
