<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsNine extends Model implements AuditableContract
{
   		use Auditable;
			/**
		   * The fillable columns
		   * @var [type]
		   */
		  protected $fillable = [
		  	'certificate_status',
		  	'receipt_date',
		  	'information',
		  	'notary_status',
		  	'takeover_status',
		  	'credit_status',
		  	'skmht_status',
		  	'imb_status',
		  	'shgb_status',
		  	'receipt_date_notary',
		  	'information_notary',
		  	'receipt_date_takeover',
		  	'information_takeover',
		  	'receipt_date_credit',
		  	'information_credit',
		  	'receipt_date_skmht',
		  	'information_skmht',
		  	'receipt_date_imb',
		  	'information_imb',
		  	'receipt_date_shgb',
		  	'information_shgb'
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
