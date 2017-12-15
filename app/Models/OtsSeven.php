<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsSeven extends Model
{
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'collateral_status',
		  	'on_behalf_of',
		  	'ownership_number',
		  	'location',
		  	'address_collateral',
		  	'description',
		  	'ownership_status',
		  	'date_evidence',
		  	'village',
		  	'districts'
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
