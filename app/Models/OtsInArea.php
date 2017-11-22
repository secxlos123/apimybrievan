<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsInArea extends Model
{
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

    /**
     * Relation with collateral
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function collateral()
    {
      return $this->belongsTo(Collateral::class, 'collateral_id');
    }
}
