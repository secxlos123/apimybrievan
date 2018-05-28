<?php

namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsDoc extends Model implements AuditableContract
{
    use Auditable;

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
    	'collateral_id',
    	'collateral_binding_doc',
    	'collateral_insurance_doc',
    	'life_insurance_doc',
    	'ownership_doc',
    	'building_permit_doc',
    	'sales_law_doc',
    	'property_tax_doc',
    	'sale_value_doc',
    	'progress_one_doc',
    	'progress_two_doc',
    	'progress_three_doc',
    	'progress_four_doc',
    	'progress_five_doc'

    ];

     public static $folder = '';



     /**
     * Global function for check file.
     *
     * @return string
     */
    public function globalImageCheck( $filename )
    {
        $path =  'img/noimage.jpg';
        if( ! empty( $filename ) ) {
            $image = 'uploads/collateral/' . $this->collateral_id . '/' . $filename;
            if( File::exists( public_path( $image ) ) ) {
                // $path = $image;
                $path = 'files/collateral/' . $this->collateral_id . '/' . $filename;
            }
        }

        return url( $path );
    }

     /**
     * Global function for set image attribute.
     *
     * @return void
     */
    public function globalSetImageAttribute( $image, $attribute, $callbackPosition = null )
    {
        if ($image != "") {
            $this->attributes[ $attribute ] = $image;

            if (gettype($image) != "string") {
                $return = $this->globalSetImage( $image, $attribute, $callbackPosition );
                if ( $return ) {
                    $this->attributes[ $attribute ] = $return;
                }
            }

        }
    }

    /**
     * Global function for set image.
     *
     * @return void
     */
    public function globalSetImage( $image, $attribute, $callbackPosition = null )
    {
        $doFunction = true;

        if ($callbackPosition) {
            $doFunction = isset($this->attributes[ $attribute ]);
        }
        $base = $this->collateral_id ? $this->collateral_id : self::$folder;
        if ( isset($this->attributes[ $attribute ]) && gettype($image) == 'object' ) {
            $path = public_path( 'uploads/collateral/'.$this->collateral_id.'/' );
            if ( ! empty( $this->attributes[ $attribute ] ) ) {
                File::delete( $path . $this->attributes[ $attribute ] );
            }

            $extension = 'png';

            if ( !$image->getClientOriginalExtension() ) {
                if ( $image->getMimeType() == 'image/jpg' ) {
                    $extension = 'jpg';
                } elseif ( $image->getMimeType() == 'image/jpeg' ) {
                    $extension = 'jpeg';
                }
            } else {
                $extension = $image->getClientOriginalExtension();
            }

            $filename = $this->collateral_id . '-' . $attribute . '.' . $extension;
            $image->move( $path, $filename );
            return $filename;
        } else {
            return null;
        }
    }

    /**
     * Get user collateral_binding_doc.
     *
     * @return string
     */
    public function getCollateralBindingDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer collateral_binding_doc.
     *
     * @return void
     */
    public function setCollateralBindingDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'collateral_binding_doc' );
    }

    /**
     * Get user collateral_insurance_doc.
     *
     * @return string
     */
    public function getCollateralInsuranceDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer collateral_insurance_doc.
     *
     * @return void
     */
    public function setCollateralInsuranceDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'collateral_insurance_doc' );
    }

    /**
     * Get user life_insurance_doc.
     *
     * @return string
     */
    public function getLifeInsuranceDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer life_insurance_doc.
     *
     * @return void
     */
    public function setLifeInsuranceDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'life_insurance_doc' );
    }

    /**
     * Get user ownership_doc.
     *
     * @return string
     */
    public function getOwnershipDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer ownership_doc.
     *
     * @return void
     */
    public function setOwnershipDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'ownership_doc' );
    }

     /**
     * Get user building_permit_doc.
     *
     * @return string
     */
    public function getBuildingPermitDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer building_permit_doc.
     *
     * @return void
     */
    public function setBuildingPermitDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'building_permit_doc' );
    }

     /**
     * Get user sales_law_doc.
     *
     * @return string
     */
    public function getSalesLawDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer sales_law_doc.
     *
     * @return void
     */
    public function setSalesLawDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'sales_law_doc' );
    }

     /**
     * Get user property_tax_doc.
     *
     * @return string
     */
    public function getPropertyTaxDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer property_tax_doc.
     *
     * @return void
     */
    public function setPropertyTaxDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'property_tax_doc' );
    }

     /**
     * Get user sale_value_doc.
     *
     * @return string
     */
    public function getSaleValueDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer sale_value_doc.
     *
     * @return void
     */
    public function setSaleValueDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'sale_value_doc' );
    }

     /**
     * Get user progress_1_doc.
     *
     * @return string
     */
    public function getProgressOneDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer progress_1_doc.
     *
     * @return void
     */
    public function setProgressOneDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'progress_one_doc' );
    }

    /**
     * Get user progress_1_doc.
     *
     * @return string
     */
    public function getProgressTwoDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer progress_1_doc.
     *
     * @return void
     */
    public function setProgressTwoDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'progress_two_doc' );
    }

    /**
     * Get user progress_1_doc.
     *
     * @return string
     */
    public function getProgressThreeDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer progress_1_doc.
     *
     * @return void
     */
    public function setProgressThreeDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'progress_three_doc' );
    }

    /**
     * Get user progress_1_doc.
     *
     * @return string
     */
    public function getProgressFourDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer progress_1_doc.
     *
     * @return void
     */
    public function setProgressFourDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'progress_four_doc' );
    }

    /**
     * Get user progress_1_doc.
     *
     * @return string
     */
    public function getProgressFiveDocAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer progress_1_doc.
     *
     * @return void
     */
    public function setProgressFiveDocAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'progress_five_doc' );
    }

     /**
	 * Relation with collateral
	 * @return \Illuminate\Database\Eloquent\BelongsTo
	 */
	public function collateral()
	{
		return $this->belongsTo(Collateral::class, 'collateral_id');
	}


}
