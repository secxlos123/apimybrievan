<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\BankStatement;
use App\Models\Mutation;
use App\Models\EForm;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class VisitReport extends Model implements AuditableContract
{
    use Auditable;
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
        'eform_id', 'purpose_of_visit', 'visit_result', 'photo_with_customer', 'pros', 'cons', 'seller_name', 'seller_address', 'seller_phone', 'selling_price', 'reason_for_sale', 'relation_with_seller', 'npwp_number', 'income', 'income_salary', 'income_allowance', 'kpp_type', 'type_financed', 'economy_sector', 'project_list', 'program_list', 'kpp_type_name', 'type_financed_name', 'economy_sector_name', 'project_list_name', 'program_list_name', 'id_prescreening', 'npwp', 'use_reason_name', 'use_reason', 'source', 'recommended', 'recommendation', 'marrital_certificate', 'divorce_certificate', 'offering_letter', 'down_payment', 'building_tax', 'salary_slip', 'proprietary', 'building_permit', 'legal_bussiness_document', 'license_of_practice', 'work_letter', 'family_card', 'couple_salary', 'couple_other_salary', 'title', 'employment_status', 'age_of_mpp', 'loan_history_accounts', 'religion', 'office_phone', 'source_income','other_document'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'npwp_number_masking', 'title_name', 'employment_status_name', 'loan_history_accounts_name', 'religion_name'
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
	if ( isset($data['mutations']) ){
        $visit_report = ( new static )->newQuery()->updateOrCreate( [ 'eform_id' => $data['eform_id']],$data );
        $mutation_ids = array();
        $bank_ids = array();
        foreach ( $data[ 'mutations' ] as $key => $mutation_data ) {
            $mutation = Mutation::create([ 'visit_report_id' => $visit_report->id ] + $mutation_data );
            $mutation_ids[]= $mutation->id;
            if (isset($mutation_data[ 'tables' ])) {
                foreach ( $mutation_data[ 'tables' ] as $key => $bank_statement_data ) {
                    $bank = BankStatement::create([ 'mutation_id' => $mutation->id ] + $bank_statement_data );
                    $bank_ids[] = $bank->id;
                }
                //Delete Bank Yang Lama
                BankStatement::where('mutation_id', $mutation->id)->whereNotIn('id', $bank_ids)->delete();
            }
        }
        //Delete mutasi yang Lama
        Mutation::where('visit_report_id', $visit_report->id)->whereNotIn('id', $mutation_ids)->delete();
        }
    }

    /**
     * Set Report legal_bussiness_document image.
     *
     * @return void
     */
    public function setLegalBussinessDocumentAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'legal_bussiness_document' );
    }
    /**
     * Set Report other_document image.
     *
     * @return void
     */
    public function setOtherDocumentAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'other_document' );
    }
    /**
     * Set Report license_of_practice image.
     *
     * @return void
     */
    public function setLicenseOfPracticeAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'license_of_practice' );
    }
    /**
     * Set Report work_letter image.
     *
     * @return void
     */
    public function setWorkLetterAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'work_letter' );
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
     * Set Report NPWP Number.
     *
     * @return void
     */
    public function setNpwpNumberAttribute( $value )
    {
        $this->attributes[ 'npwp_number' ] = str_replace('-', '', str_replace('.', '', $value));
    }

    /**
     * Get Report Legal Document image url.
     *
     * @return string
     */
    public function getLegalDocumentAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report other_document image url.
     *
     * @return string
     */
    public function getOtherDocumentAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Salary Slip image url.
     *
     * @return string
     */
    public function getSalarySlipAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Family Card image url.
     *
     * @return string
     */
    public function getFamilyCardAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Marrital Certificate image url.
     *
     * @return string
     */
    public function getMarritalCertificateAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Divorce Certificate image url.
     *
     * @return string
     */
    public function getDivorceCertificateAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Photo With Customer image url.
     *
     * @return string
     */
    public function getPhotoWithCustomerAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Offering Letter image url.
     *
     * @return string
     */
    public function getOfferingLetterAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report roprietary image url.
     *
     * @return string
     */
    public function getProprietaryAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Building Permit image url.
     *
     * @return string
     */
    public function getBuildingPermitAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Down Payment image url.
     *
     * @return string
     */
    public function getDownPaymentAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report Building Tax image url.
     *
     * @return string
     */
    public function getBuildingTaxAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report NPWP image url.
     *
     * @return string
     */
    public function getNpwpAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

     /**
     * Get Report LegalBussinessDocument image url.
     *
     * @return string
     */
    public function getLegalBussinessDocumentAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }
    /**
     * Get Report LicenseOfPractice image.
     *
     * @return string
     */
    public function getLicenseOfPracticeAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }
    /**
     * Get Report WorkLetter image.
     *
     * @return string
     */
    public function getWorkLetterAttribute( $image )
    {
        return $this->globalImageCheck( $image );
    }

    /**
     * Get Report NPWP Number Masking.
     *
     * @return string
     */
    public function getNpwpNumberMaskingAttribute(  )
    {
        return preg_replace(
            '/([0-9]{2})?([0-9]{3})?([0-9]{3})?([0-9]{1})?([0-9]{3})?([0-9]{3})/'
            , '$1.$2.$3.$4-$5.$6'
            , str_pad(substr($this->npwp_number, 0, 15), 15, "0")
        );
    }

    /**
     * Global function for check file.
     *
     * @return string
     */
    public function globalImageCheck( $filename )
    {
        $eformData = EForm::findOrFail($this->eform_id);
        $path =  'img/noimage.jpg';
        if( ! empty( $filename ) ) {
            $image = 'uploads/' . $eformData->nik . '/' . $filename;
            if( \File::exists( public_path( $image ) ) ) {
                // $path = $image;
                $path = 'files/' . $eformData->nik . '/' . $filename;
            }
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
        $eformData = EForm::findOrFail($this->eform_id);
        if ( gettype($image) == 'object' ) {
            $path = public_path( 'uploads/' . $eformData->nik . '/' );
            if ( ! empty( $this->attributes[ $attribute ] ) ) {
                \File::delete( $path . $this->attributes[ $attribute ] );
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

            $filename = $this->eform_id . '-' . $attribute . '.' . $extension;
            $image->move( $path, $filename );
            return $filename;
        } else {
            return null;
        }
    }

    /**
     * Get mutator age of title name.
     *
     * @return string
     */
    public function getTitleNameAttribute(  )
    {
        if (null !== $this->title) {
            return get_title($this->title);
        }

        return '-';
    }

    /**
     * Get mutator age of employment status name.
     *
     * @return string
     */
    public function getEmploymentStatusNameAttribute(  )
    {
        if (null !== $this->employment_status) {
            return get_employment($this->employment_status);
        }

        return '-';
    }

    /**
     * Get mutator age of history account name.
     *
     * @return string
     */
    public function getLoanHistoryAccountsNameAttribute(  )
    {
        if (null !== $this->loan_history_accounts) {
            return get_loan_history($this->loan_history_accounts);
        }

        return '-';
    }

    /**
     * Get mutator age of religion name.
     *
     * @return string
     */
    public function getReligionNameAttribute(  )
    {
        if (null !== $this->religion) {
            return get_religion($this->religion);
        }

        return '-';
    }

    /**
     * The relation to visit report.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mutation()
    {
        return $this->hasMany( Mutation::class );
    }
}
