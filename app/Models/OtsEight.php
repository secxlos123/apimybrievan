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
            'collateral_id',
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
		  	'binding_value',
		  	'independent_appraiser_name'
		  ];

    protected $appends = ['type_binding_name'];

	/**
	 * Relation with collateral
	 * @return \Illuminate\Database\Eloquent\BelongsTo
	 */
	public function collateral()
	{
        return $this->belongsTo(Collateral::class, 'collateral_id');
	}

    /**
     * Get type binding name
     *
     * @return string
     */
    public function getTypeBindingNameAttribute( )
    {
        if ( $this->type_binding == "01" ) {
            return "Hak Tanggungan";

        } else if ( $this->type_binding == "02" ) {
            return "Gadai";

        } else if ( $this->type_binding == "03" ) {
            return "Feduciare Elgendom Overdracht";

        } else if ( $this->type_binding == "04" ) {
            return "SKMHT (Surat Kuasa Memberikan Hak Tanggungan)";

        } else if ( $this->type_binding == "05" ) {
            return "Cessie";

        } else if ( $this->type_binding == "06" ) {
            return "Belum Diikat";

        } else if ( $this->type_binding == "09" ) {
            return "Lain-lain";

        } else if ( $this->type_binding == "10" ) {
            return "Fidusia Dengan UU";

        } else if ( $this->type_binding == "11" ) {
            return "Fidusia Dengan PJ.08";

        }

        return "-";
    }

    /**
     * Get type binding name
     *
     * @return string
     */
    public function getTypeBindingAttribute( $value )
    {
        if ( $value == "Hak Tanggungan" ) {
            return "01";

        } else if ( $value == "Gadai" ) {
            return "02";

        } else if ( $value == "Feduciare Elgendom Overdracht (FEO)" ) {
            return "03";

        } else if ( $value == "SKMHT (Surat Kuasa Memberikan Hak Tanggungan)" ) {
            return "04";

        } else if ( $value == "Cessie" ) {
            return "05";

        } else if ( $value == "Belum Diikat" ) {
            return "06";

        } else if ( $value == "Lain - Lain" ) {
            return "09";

        } else if ( $value == "Fidusia dengan UU" ) {
            return "10";

        } else if ( $value == "Fidusia dengan PJ08" ) {
            return "11";

        }

       return $value;
    }
}
