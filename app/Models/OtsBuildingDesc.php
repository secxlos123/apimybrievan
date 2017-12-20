<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsBuildingDesc extends Model implements AuditableContract
{
    use Auditable;
    /**
     * The fillable columns
     * @var [type]
     */
    protected $fillable = [
      'collateral_id',
      'permit_number',
      'permit_date',
      'on_behalf_of',
      'type',
      'count',
      'spacious',
      'year',
      'description',
      'north_limit',
      'north_limit_from',
      'east_limit',
      'east_limit_from',
      'south_limit',
      'south_limit_from',
      'west_limit',
      'west_limit_from'
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
