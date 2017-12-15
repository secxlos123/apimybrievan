<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtsTen extends Model
{
   
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'paripasu',
		  	'paripasu_bank',
		  	'insurance',
		  	'insurance_company',
		  	'insurance_value',
		  	'eligibility'
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
