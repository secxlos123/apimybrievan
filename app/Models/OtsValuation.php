<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsValuation extends Model
{
  /**
   * The fillable columns
   * @var [type]
   */
  protected $fillable = [
    'collateral_id',
    'scoring_land_date',
    'npw_land',
    'nl_land',
    'pnpw_land',
    'pnl_land',
    'scoring_building_date',
    'npw_building',
    'nl_building',
    'pnpw_building',
    'pnl_building',
    'scoring_all_date',
    'npw_all',
    'nl_all',
    'pnpw_all',
    'pnl_all'
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
