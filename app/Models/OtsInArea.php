<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsInArea extends Model implements AuditableContract
{
     use Auditable;
    /**
     * The fillable columns
     * @var [type]
     */
    protected $fillable = [
      'collateral_id',
      'collateral_type',
      'city_id',
      'location',
      'latitude',
      'longtitude',
      'district',
      'sub_district',
      'rt',
      'rw',
      'zip_code',
      'distance',
      'unit_type',
      'distance_from',
      'position_from_road',
      'ground_type',
      'ground_level',
      'distance_of_position',
      'north_limit',
      'east_limit',
      'south_limit',
      'west_limit',
      'another_information',
      'surface_area'
    ];

    protected $appends = [
      'city'
    ];


    /**
     * Get related city
     * @return
     */
    public function getCityAttribute()
    {
      return $this->city()->first();
    }

    /**
     * Relation with city
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function city()
    {
      return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Relation with collateral
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function collateral()
    {
      return $this->belongsTo(Collateral::class, 'collateral_id');
    }
}
