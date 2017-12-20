<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsEight extends Model implements AuditableContract
{
		use Auditable;
   
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'liquidation_realization',
		  	'fair_market',
		  	'liquidation',
		  	'fair_market_projection',
		  	'liquidation_projection',
		  	'njop',
		  	'appraisal_by',
		  	'independent_appraiser',
		  	'date_assessment',
		  	'type_binding',
		  	'binding_number',
		  	'binding_value'
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
