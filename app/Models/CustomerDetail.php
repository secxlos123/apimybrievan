<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'user_id', 'nik', 'birth_place' , 'birth_date', 'address', 'gender', 'city', 'phone', 'citizenship', 'status', 'address_status', 'mother_name', 'mobile_phone', 'emergency_contact', 'emergency_relation', 'identity', 'npwp', 'work_type', 'work', 'company_name', 'work_field', 'position', 'work_duration', 'office_address', 'salary', 'other_salary', 'loan_installment', 'dependent_amount', 'couple_nik', 'couple_name', 'couple_birth_place', 'couple_birth_date', 'couple_identity', 'legal_document', 'salary_slip', 'bank_statement', 'family_card', 'marrital_certificate', 'diforce_certificate'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id'
    ];

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getNpwpAttribute( $value )
    {
        if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
            $image = url( 'uploads/users/' . $this->user_id . '/' . $value );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getIdentityAttribute( $value )
    {
        if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
            $image = url( 'uploads/users/' . $this->user_id . '/' . $value );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getCoupleIdentityAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getLegalDocumentAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getSalarySlipAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getBankStatementAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getFamilyCardAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getMarritalCertificateAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getDiforceCertificateAttribute( $value )
    {
        if( ! empty( $value ) ) {
            if( File::exists( 'uploads/users/' . $this->user_id . '/' . $value ) ) {
                return url( 'uploads/users/' . $this->user_id . '/' . $value );
            }
        }
        return url( 'img/noimage.jpg' );
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getStatusAttribute( $value )
    {
        if( $value == 0 ) {
            return 'Tidak menikah';
        } else if( $value == 1 ) {
            return 'Menikah';
        } else if( $value == 2 ) {
            return 'Janda';
        } else if( $value == 3 ) {
            return 'Duda';
        }

        return null;
    }

    /**
     * Set customer npwp image.
     *
     * @return void
     */
    public function setNpwpAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'npwp' ] ) ) {
            File::delete( $path . $this->attributes[ 'npwp' ] );
        }

        $filename = $this->user_id . '-npwp.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'npwp' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setIdentityAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'identity' ] ) ) {
            File::delete( $path . $this->attributes[ 'identity' ] );
        }

        $filename = $this->user_id . '-identity.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'identity' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setCoupleIdentityAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'couple_identity' ] ) ) {
            File::delete( $path . $this->attributes[ 'couple_identity' ] );
        }

        $filename = $this->user_id . '-couple_identity.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'couple_identity' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setLegalDocumentAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'legal_document' ] ) ) {
            File::delete( $path . $this->attributes[ 'legal_document' ] );
        }

        $filename = $this->user_id . '-legal_document.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'legal_document' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setSalarySlipAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'salary_slip' ] ) ) {
            File::delete( $path . $this->attributes[ 'salary_slip' ] );
        }

        $filename = $this->user_id . '-salary_slip.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'salary_slip' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setBankStatementAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'bank_statement' ] ) ) {
            File::delete( $path . $this->attributes[ 'bank_statement' ] );
        }

        $filename = $this->user_id . '-bank_statement.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'bank_statement' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setFamilyCardAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'family_card' ] ) ) {
            File::delete( $path . $this->attributes[ 'family_card' ] );
        }

        $filename = $this->user_id . '-family_card.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'family_card' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setMarritalCertificateAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'marrital_certificate' ] ) ) {
            File::delete( $path . $this->attributes[ 'marrital_certificate' ] );
        }

        $filename = $this->user_id . '-marrital_certificate.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'marrital_certificate' ] = $filename;
    }

    /**
     * Set customer identity image.
     *
     * @return void
     */
    public function setDiforceCertificateAttribute( $image )
    {
        $path = public_path( 'uploads/users/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'diforce_certificate' ] ) ) {
            File::delete( $path . $this->attributes[ 'diforce_certificate' ] );
        }

        $filename = $this->user_id . '-diforce_certificate.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'diforce_certificate' ] = $filename;
    }
}
