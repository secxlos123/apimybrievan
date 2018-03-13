<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsTen extends Model implements AuditableContract
{
   		use Auditable;
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'collateral_id',
		  	'paripasu',
		  	'paripasu_bank',
		  	'insurance',
		  	'insurance_company',
		  	'insurance_value',
		  	'eligibility',
		  	'insurance_company_name'
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
