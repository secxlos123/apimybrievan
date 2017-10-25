<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Auth;
use File;

class CustomerDetail extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'customer_details';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'city_id', 'nik', 'birth_place_id', 'birth_date', 'address', 'citizenship_id', 'status', 'address_status', 'mother_name', 'emergency_contact', 'emergency_relation', 'identity', 'npwp', 'legal_document', 'salary_slip', 'bank_statement', 'family_card', 'marrital_certificate', 'diforce_certificate', 'job_type_id', 'job_id', 'company_name', 'job_field_id', 'position', 'work_duration', 'office_address', 'salary', 'other_salary', 'loan_installment', 'dependent_amount', 'couple_nik', 'couple_name', 'couple_birth_place_id', 'couple_birth_date', 'couple_identity', 'couple_salary', 'couple_other_salary', 'couple_loan_installment', 'emergency_name', 'is_verified','work_duration_month'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * Global function for check file.
     *
     * @return string
     */
    public function globalImageCheck( $filename )
    {
        $path =  'img/noimage.jpg';
        if( ! empty( $filename ) ) {
            if( File::exists( public_path('uploads/users/' . $this->user_id . '/' . $filename) ) ) {
                $path = 'uploads/users/' . $this->user_id . '/' . $filename;
            }
        }

        if (strpos(ENV('APP_URL'), 'localhost') !== false) {
            return public_path( $path );
        }
        
        return url( $path );
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
     * Get user Identity image url.
     *
     * @return string
     */
    public function getIdentityAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Couple Identity image url.
     *
     * @return string
     */
    public function getCoupleIdentityAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Legal Document image url.
     *
     * @return string
     */
    public function getLegalDocumentAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Salary Slip image url.
     *
     * @return string
     */
    public function getSalarySlipAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Bank Statement image url.
     *
     * @return string
     */
    public function getBankStatementAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Family Card image url.
     *
     * @return string
     */
    public function getFamilyCardAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Marrital Certificate image url.
     *
     * @return string
     */
    public function getMarritalCertificateAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Get user Diforce Certificate image url.
     *
     * @return string
     */
    public function getDiforceCertificateAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Parse Couple relation name.
     *
     * @return string
     */
    public function getStatusAttribute( $value )
    {
        if( $value == 1 ) {
            return 'Tidak menikah';
        } else if( $value == 2 ) {
            return 'Menikah';
        } else if( $value == 3 ) {
            return 'Duda/Janda';
        }

        return null;
    }

    /**
     * Get user String Address_status.
     * @author Akse
     * @return string
     */
    public function getAddressStatusAttribute( $value )
    {
        if( $value == 0 ) {
            return 'Milik Sendiri';
        } else if( $value == 1 ) {
            return 'Milik Orang Tua/Mertua atau Rumah Dinas';
        } else if( $value == 3 ) {
            return 'Tinggal di Rumah Kontrakan';
        }

        return null;
    }

    /**
     * Get user citizenship information.
     *
     * @return string
     */
    public function getCitizenshipAttribute( $value )
    {
        $citizenship_search = \Asmx::setEndpoint( 'GetNegara' )->setQuery( [
            'limit' => 1,
            'search' => $this->citizenship_id,
        ] )->post();
        if( $citizenship_search[ 'code' ] == 200 ) {
            return $citizenship_search[ 'contents' ][ 'data' ][ 0 ][ 'desc2' ];
        }
        return null;
    }

    /**
     * Global function for Get ASMX data
     *
     * @return string
     */
    public function globalGetAttribute( $endpoint, $value ) {
        $search = \Asmx::setEndpoint( $endpoint )->setQuery( [
            'limit' => 1,
            'search' => $value,
        ] )->post();
        
        if( $search[ 'code' ] == 200 ) {
            foreach ($search[ 'contents' ][ 'data' ] as $data) {
                if ( $data['desc1'] == $value ) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * Get user citizenship information.
     *
     * @return array
     */
    public function getCitizenshipIdAttribute( $value )
    {
        return $this->globalGetAttribute( 'GetNegara', $value );
    }

    /**
     * Get Job information.
     *
     * @return array
     */
    public function getJobIdAttribute( $value )
    {
        return $this->globalGetAttribute( 'GetPekerjaan', $value );
    }

    /**
     * Get Job Type information.
     *
     * @return array
     */
    public function getJobTypeIdAttribute( $value )
    {
        return $this->globalGetAttribute( 'GetJenisPekerjaan', $value );
    }

    /**
     * Get Job Field information.
     *
     * @return array
     */
    public function getJobFieldIdAttribute( $value )
    {
        return $this->globalGetAttribute( 'GetBidangPekerjaan', $value );
    }

    /**
     * Get Jabatan information.
     *
     * @return array
     */
    public function getPositionAttribute( $value )
    {
        return $this->globalGetAttribute( 'GetJabatan', $value );
    }

    /**
     * Get Status Integer.
     *
     * @return string
     */
    public function getStatusIdAttribute()
    {
        if( $this->status == 'Tidak menikah' ) {
            return 1;
        } else if( $this->status == 'Menikah' ) {
            return 2;
        } else if( $this->status == 'Duda/Janda' ) {
            return 3;
        }

        return null;
    }

     /**
     * Get Status Integer.
     *
     * @return string
     */
    public function getAddressStatusIdAttribute()
    {
        if( $this->address_status == 'Milik Sendiri' ) {
            return 0;
        } else if( $this->address_status == 'Milik Orang Tua/Mertua atau Rumah Dinas' ) {
            return 1;
        } else if( $this->address_status == 'Tinggal di Rumah Kontrakan' ) {
            return 3;
        }

        return null;
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
            $path = public_path( 'uploads/users/' . $this->user_id . '/' );
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

    /**
     * Update all image needed.
     *
     * @return void
     */
    public function updateAllImageAttribute( $keys, $data )
    {
        $newData = array();
        foreach ($keys as $key) {
            if ( isset($data[ $key ]) && !empty($data[ $key ]) ) {
                $image = $this->globalSetImage( $data[ $key ], $key );
                if ($image) {
                    $this->update([ $key => $image ]);                    
                }
            }
        }
    }

    /**
     * Set customer npwp image.
     *
     * @return void
     */
    public function setNpwpAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'npwp' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setIdentityAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'identity' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setCoupleIdentityAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'couple_identity' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setLegalDocumentAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'legal_document' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setSalarySlipAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'salary_slip' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setBankStatementAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'bank_statement' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setFamilyCardAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'family_card' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setMarritalCertificateAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'marrital_certificate' );
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setDiforceCertificateAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'diforce_certificate' );
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function birth_place_city()
    {
        return $this->belongsTo( City::class, 'birth_place_id' );
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function couple_birth_place_city()
    {
        return $this->belongsTo( City::class, 'couple_birth_place_id' );
    }
}
