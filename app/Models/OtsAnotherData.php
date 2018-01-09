<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use App\Models\OtsPhoto;
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

      /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create($data) {
      if (array_key_exists('image_area',$data)){
      $dataother = $data;
      unset($dataother['image_area']);
      $otsother = ( new static )->newQuery()->create($dataother);
            foreach ( $data['image_area'] as $key => $imagedata ) {
                $image_data = OtsPhoto::create( [
                    'ots_other_id' => $otsother->id
                ] + $imagedata );
            }
      }
    }

      /**
       * Relation with collateral
       * @return \Illuminate\Database\Eloquent\BelongsTo
       */
    public function collateral()
    {
        return $this->belongsTo(Collateral::class, 'collateral_id');
    }

    /**
       * Relation with collateral
       * @return \Illuminate\Database\Eloquent\BelongsTo
       */
    public function images()
    {
        return $this->hasMany(OtsPhoto::class, 'ots_other_id');
    }

}
