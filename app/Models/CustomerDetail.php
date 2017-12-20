<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Auth;
use File;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CustomerDetail extends Model implements AuditableContract
{
    use Auditable;

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
        'user_id', 'city_id', 'nik', 'birth_place_id', 'birth_date', 'address', 'citizenship_id', 'status', 'address_status', 'mother_name', 'emergency_contact', 'emergency_relation', 'identity', 'npwp', 'salary_slip', 'bank_statement', 'family_card', 'marrital_certificate', 'diforce_certificate', 'job_type_id', 'job_id', 'company_name', 'job_field_id', 'position', 'work_duration', 'office_address', 'salary', 'other_salary', 'loan_installment', 'dependent_amount', 'couple_nik', 'couple_name', 'couple_birth_place_id', 'couple_birth_date', 'couple_identity', 'couple_salary', 'couple_other_salary', 'couple_loan_installment', 'emergency_name', 'is_verified','work_duration_month','citizenship_name' , 'job_type_name' , 'job_field_name' , 'job_name' , 'position_name','cif_number','current_address', 'kewarganegaraan','pendidikan_terakhir_name','address_domisili','mobile_phone_couple', 'source_income'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'status_id','address_status_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    public static $folder = '';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        self::$folder = \Request::input('nik');

        parent::boot();
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
            $image = 'uploads/' . $this->nik . '/' . $filename;
            if( File::exists( public_path( $image ) ) ) {
                $path = $image;
            }
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
            return 'Belum menikah';
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
     * Get Status Integer.
     *
     * @return string
     */
    public function getStatusIdAttribute()
    {
        if( $this->status == 'Belum menikah' ) {
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
        if (!empty($this->user)) {
            $user = $this->user->id;
        }else{
            $user = user_info('id');
        }

        if ($callbackPosition) {
            $doFunction = isset($this->attributes[ $attribute ]);
        }
        \Log::info("========================handling upload=============================");
        $base = $this->nik ? $this->nik : self::$folder;
        if ( isset($this->attributes[ $attribute ]) && gettype($image) == 'object' ) {
            $path = public_path( 'uploads/' . $base . '/' );
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

            $filename = $user . '-' . $attribute . '.' . $extension;
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
    public function updateAllImageAttribute( $keys, $data, $callbackPosition = null )
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
     * Query scope .
     *
     * @param  \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDbws($query, Request $request)
    {
        return $query->leftJoin('eforms','customer_details.id','=','eforms.user_id')
                    ->leftJoin('visit_reports','eforms.id','=','visit_reports.eform_id')
                    ->where(function ($data) use ($request) {
                        $data->where('customer_details.nik','=',$request->input('nik'));
                    })->selectRaw("
                                    customer_details.user_id,
                                    visit_reports.eform_id,
                                    customer_details.identity AS KTP,
                                    customer_details.couple_identity AS KTPPASANGAN,
                                    visit_reports.divorce_certificate AS Akta_Pisah_Harta,
                                    visit_reports.marrital_certificate AS Akta_Nikah_Atau_Akta_Cerai,
                                    visit_reports.down_payment AS Bukti_Uang_Muka,
                                    visit_reports.photo_with_customer AS Foto_Debitur,
                                    visit_reports.npwp AS Kartu_Npwp,
                                    visit_reports.offering_letter AS Surat_Penawaran,
                                    visit_reports.building_tax AS PBB,
                                    visit_reports.salary_slip AS Slip_Gaji,
                                    visit_reports.proprietary AS SHM_Atau_SHGB,
                                    visit_reports.building_permit AS IMB,
                                    visit_reports.legal_bussiness_document AS Dokumen_legal_Usaha,
                                    visit_reports.license_of_practice AS Surat_Izin_Praktek_Usaha,
                                    visit_reports.work_letter AS Surat_Keterangan_Kerja,
                                    visit_reports.family_card AS Kartu_Keluarga "
                                );
    }

    /**
     * Get list debitur.
     *
     * @return array
     */
    public function getListDebitur($params)
    {
        $data = CustomerDetail::with('user', 'city')
                ->whereHas('user', function($query) use ($params){
                    return $query->where('first_name', 'like', '%'.$params['name'].'%')
                                 ->orWhere('last_name', 'like', '%'.$params['name'].'%');
                })
                ->where('nik', 'like', '%'.$params['nik'].'%')
                ->where('city_id', $params['city_id'])
                ->get()
                ->pluck('listDebitur');
        return $data;
    }

    /**
     * Mutator for list debitur.
     *
     * @return void
     */
    public function getListDebiturAttribute()
    {
        return [
            "nik"    => $this->nik,
            "nama"   => $this->user->first_name." ".$this->user->last_name,
            "email"  => $this->user->email,
            "kota"   => $this->city ? $this->city->name : '',
            "phone"  => $this->user->mobile_phone,
            "gender" => $this->user->gender,
        ];
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

    /**
     * The user_id belongs to user
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
