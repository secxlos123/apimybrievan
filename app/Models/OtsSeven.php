<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsSeven extends Model implements AuditableContract
{
		use Auditable;
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'collateral_status',
		  	'on_behalf_of',
		  	'ownership_number',
		  	'location',
		  	'city_id',
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

		 /**
	     * Relation with city
	     * @return \Illuminate\Database\Eloquent\BelongsTo
	     */
	    public function city()
	    {
	        return $this->belongsTo(City::class, 'city_id');
	    }
}
