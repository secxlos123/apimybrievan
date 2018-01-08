<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsAnotherData extends Model implements AuditableContract
{
      use Auditable;
      /**
       * The fillable columns
       * @var [type]
       */
      protected $fillable = [
        'collateral_id',
        'bond_type',
        'use_of_building_function',
        'optimal_building_use',
        'building_exchange',
        'things_bank_must_know',
        'image_condition_area',
        'building_exchange'
      ];

      protected $appends = [
      'photos'
      ];

      /**
       * Relation with collateral
       * @return \Illuminate\Database\Eloquent\BelongsTo
       */
    public function collateral()
    {
        return $this->belongsTo(Collateral::class, 'collateral_id');
    }

    public function getPhotosAttribute()
    {
      return $this->photos()->get();
    }

      /**
     * Get the properties photos.
     */
    public function photos()
    {
        return $this->morphMany( Photo::class, 'photoable' );
    }
}
