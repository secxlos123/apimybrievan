<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsEnvironment extends Model implements AuditableContract
{
  use Auditable;
  
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
    'distance_from_transportation',
    'designated_pln',
    'designated_phone',
    'designated_pam',
    'designated_telex'
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
