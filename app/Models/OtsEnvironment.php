<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsEnvironment extends Model
{
  /**
   * The fillable columns
   * @var [type]
   */
  protected $fillable = [
    'collateral_id',
    'designated_land',
    'designated',
    'other_designated',
    'nearest_location',
    'other_guide',
    'transportation',
    'distance_from_transportation'
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
