<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsAccordingLetterLand extends Model
{
    /**
     * The fillable columns
     * @var [type]
     */
    protected $fillable = [
      'collateral_id',
      'type',
      'authorization_land',
      'match_bpn',
      'match_area',
      'match_limit_in_area',
      'surface_area_by_letter',
      'number',
      'date',
      'on_behalf_of',
      'duration_land_authorization',
      'bpn_name'
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
