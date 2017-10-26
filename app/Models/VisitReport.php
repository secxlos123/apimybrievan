<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\BankStatement;
use App\Models\Mutation;

class VisitReport extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'visit_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'eform_id', 'purpose_of_visit', 'result', 'photo_with_customer', 'pros', 'cons',
        'seller_name', 'seller_address', 'seller_phone', 'selling_price', 'reason_for_sale', 'relation_with_seller'
    ];

    // ['npwp', 'legal_document', 'salary_slip', 'family_card', 'marrital_certificate', 'divorce_certificate', 'photo_with_customer', 'offering_letter', 'proprietary', 'building_permit', 'down_payment', 'building_tax'];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        $visit_report = ( new static )->newQuery()->create( $data );
        foreach ( $data[ 'mutations' ] as $key => $mutation_data ) {
            $mutation = Mutation::create( [
                'visit_report_id' => $visit_report->id
            ] + $mutation_data );
            foreach ( $mutation_data[ 'tables' ] as $key => $bank_statement_data ) {
                BankStatement::create( [
                    'mutation_id' => $mutation->id
                ] + $bank_statement_data );
            }
        }
    }

    /**
     * Set Report Legal Document image.
     *
     * @return void
     */
    public function setLegalDocumentAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'legal_document' );
    }

    /**
     * Set Report Salary Slip image.
     *
     * @return void
     */
    public function setSalarySlipAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'salary_slip' );
    }
    
    /**
     * Set Report Family Card image.
     *
     * @return void
     */
    public function setFamilyCardAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'family_card' );
    }
    
    /**
     * Set Report Marrital Certificate image.
     *
     * @return void
     */
    public function setMarritalCertificateAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'marrital_certificate' );
    }
    
    /**
     * Set Report Divorce Certificate image.
     *
     * @return void
     */
    public function setDivorceCertificateAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'divorce_certificate' );
    }
    
    /**
     * Set Report Photo With Customer image.
     *
     * @return void
     */
    public function setPhotoWithCustomerAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'photo_with_customer' );
    }
    
    /**
     * Set Report Offering Letter image.
     *
     * @return void
     */
    public function setOfferingLetterAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'offering_letter' );
    }
    
    /**
     * Set Report Proprietary image.
     *
     * @return void
     */
    public function setProprietaryAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'proprietary' );
    }
    
    /**
     * Set Report Building Permit image.
     *
     * @return void
     */
    public function setBuildingPermitAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'building_permit' );
    }
    
    /**
     * Set Report Down Payment image.
     *
     * @return void
     */
    public function setDownPaymentAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'down_payment' );
    }
    
    /**
     * Set Report Building Tax image.
     *
     * @return void
     */
    public function setBuildingTaxAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'building_tax' );
    }

    /**
     * Set Report NPWP image.
     *
     * @return void
     */
    public function setNpwpAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'npwp' );
    }

    /**
     * Get Report Legal Document image url.
     *
     * @return string
     */
    public function getLegalDocumentAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Salary Slip image url.
     *
     * @return string
     */
    public function getSalarySlipAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Family Card image url.
     *
     * @return string
     */
    public function getFamilyCardAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Marrital Certificate image url.
     *
     * @return string
     */
    public function getMarritalCertificateAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Divorce Certificate image url.
     *
     * @return string
     */
    public function getDivorceCertificateAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Photo With Customer image url.
     *
     * @return string
     */
    public function getPhotoWithCustomerAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Offering Letter image url.
     *
     * @return string
     */
    public function getOfferingLetterAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report roprietary image url.
     *
     * @return string
     */
    public function getProprietaryAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Building Permit image url.
     *
     * @return string
     */
    public function getBuildingPermitAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Down Payment image url.
     *
     * @return string
     */
    public function getDownPaymentAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }
    
    /**
     * Get Report Building Tax image url.
     *
     * @return string
     */
    public function getBuildingTaxAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user NPWP image url.
     *
     * @return string
     */
    public function getNpwpAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Global function for check file.
     *
     * @return string
     */
    public function globalImageCheck( $filename )
    {
        $path =  'img/noimage.jpg';
        if( ! empty( $filename ) ) {
            $image = 'uploads/eforms/' . $this->eform_id . '/visit_report/' . $filename;
            if( File::exists( public_path( $image ) ) ) {
                $path = $image;
            }
        }

        if (strpos(ENV('APP_URL'), 'localhost') !== false) {
            return public_path( $path );
        }
        
        return url( $path );
    }

    /**
     * Global function for set image attribute.
     *
     * @return void
     */
    public function globalSetImageAttribute( $image, $attribute )
    {
        if (gettype($image) == "string") {
            $this->attributes[ $attribute ] = $image;

        } else {
            $return = $this->globalSetImage( $image, $attribute );
            if ( $return ) {
                $this->attributes[ $attribute ] = $return;
            }
        }
    }

    /**
     * Global function for set image.
     *
     * @return void
     */
    public function globalSetImage( $image, $attribute )
    {
        if ( isset($this->attributes[ $attribute ]) && gettype($image) == 'object' ) {
            $path = public_path( 'uploads/eforms/' . $this->eform_id . '/visit_report/' );
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

            $filename = $this->user_id . '-' . $attribute . '.' . $extension;
            $image->move( $path, $filename );
            return $filename;
        } else {
            return null;
        }
    }
}