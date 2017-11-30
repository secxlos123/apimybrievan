<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use Sentinel;
use Asmx;
use RestwsHc;

class EForm extends Model
{
    /**
     * The table name.
     *`
     * @var string
     */
    protected $table = 'eforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address', 'token', 'status', 'response_status', 'recommended', 'recommendation', 'is_screening', 'pefindo_score', 'uploadscore', 'ket_risk', 'dhn_detail', 'sicd_detail', 'status_eform', 'branch'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'ao_name', 'status', 'aging', 'is_visited', 'pefindo_color' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'updated_at', 'ao' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [ 'additional_parameters' => 'array' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function saveImages( $images )
    {
        foreach ( $images as $key => $image ) {
            $path = public_path( 'uploads/eforms/' . $this->id . '/' );
            $filename = $key . '.' . $image->getClientOriginalExtension();
            $image->move( $path, $filename );
        }
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getCustomerNameAttribute()
    {
        return str_replace('"', '', str_replace("'", '', $this->customer->fullname));
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getMobilePhoneAttribute()
    {
        return $this->customer->mobile_phone;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getNominalAttribute()
    {
        if( $this->kpr ) {
            return $this->kpr->request_amount;
        }
        return 0;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getAoNameAttribute()
    {
        if ( $this->ao_id ) {
            $AO = \RestwsHc::getUser( $this->ao_id );
            return $AO[ 'name' ];
        }

        return null;
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        // if ( !$this->is_approved && $this->recommended) {
        //     return 'Kredit Ditolak';
        // }
         if ($this->status_eform == 'Rejected' ) {
            return 'Kredit Ditolak';
        }
        if( $this->is_approved && $this->customer->detail->is_verified ) {
            return 'Proses CLF';
        }
        if( $this->visit_report ) {
            return 'Prakarsa';
        }
        if( $this->ao_id ) {
            return 'Disposisi Pengajuan';
        }

        return 'Pengajuan Kredit';
    }

    /**
     * Get Prescreening color information.
     *
     * @return string
     */
    public function getPrescreeningStatusAttribute( $value )
    {
        if ( $value == 1 ) {
            return 'Hijau';

        } elseif ( $value == 2 ) {
            return 'Kuning';

        } elseif ( $value == 3 ) {
            return 'Merah';

        }

        return '-';
    }

    /**
     * Get Pefindo color information.
     *
     * @return string
     */
    public function getPefindoColorAttribute( $value )
    {
        if ( $value >= 250 && $value <= 573 ) {
            return 'Merah';

        } elseif ( $value >= 677 && $value <= 900 ) {
            return 'Hijau';

        } else {
            return 'Kuning';
        }
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        $days = $this->created_at->diffInDays();
        return $days . ' hari ';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getIsVisitedAttribute()
    {
        if( $this->visit_report ) {
            return true;
        }
        return false;
    }

    /**
     * Set user id information.
     *
     * @return string
     */
    public function setUserIdAttribute( $value )
    {
        $this->attributes[ 'user_id' ] = $value;
        $customer = $this->customer;
        $ref_number = strtoupper( substr( $customer->first_name, 0, 3 ) );
        $ref_number .= date( 'y' );
        $ref_number .= date( 'm' );
        $ref_number_check = static::whereRaw( 'ref_number ILIKE ?', [ $ref_number . '%' ] );
        \Log::info($ref_number_check->get());
        $ref_number_check = $ref_number_check->max( 'ref_number' );
        \Log::info($ref_number_check);

        if( count($ref_number_check) > 0 ) {

            $ref_number .= ((integer) substr($ref_number_check, 7)) + 1;
        } else {
            $ref_number .= '1';
        }

        $this->attributes[ 'ref_number' ] = $ref_number;
    }

    /**
     * Approve E-Form function.
     *
     * @return string
     */
    public static function approve( $eform_id, $request )
    {
        $eform = static::findOrFail( $eform_id );
        $result['status'] = false;
        if ( $request->is_approved ) {
             $result = $eform->insertCoreBRI();

             if ($result['status']) {
            $eform->update( [
                'pros' => $request->pros,
                'cons' => $request->cons,
                'recommendation' => $request->recommendation,
                'recommended' => $request->recommended == "yes" ? true : false,
                'is_approved' => $request->is_approved,
                'status_eform' => 'approved'
                ] );
            }

        }
        else
        {
            $eform->update( [
                'pros' => $request->pros,
                'cons' => $request->cons,
                'recommendation' => $request->recommendation,
                'recommended' => $request->recommended == "yes" ? true : false,
                'is_approved' => $request->is_approved,
                'status_eform' => 'Rejected'
                ] );
            $result['status'] = true;

        }

        return $result;
    }

    /**
     * Function to insert data to core BRI.
     *
     * @return array
     */
    public function insertCoreBRI()
    {
        \Log::info("console 1");
        $kpr = $this->kpr;
        \Log::info("console 2");
        $customer = $this->customer;
        \Log::info("console 3");
        $customer_detail = $customer->personal;
        \Log::info("console 4");
        $customer_work =  $customer->work;
        \Log::info("console 5");
        $customer_finance =  $customer->Financial;
        \Log::info("console 6");
        $customer_contact =  $customer->contact;
        \Log::info("console 7");
        $customer_other =  $customer->other;
        \Log::info("console 8");
        $lkn = $this->visit_report;
        \Log::info("console 9");
        \Log::info($customer);
        \Log::info("==============================================================================================");
        /*
        $request = [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->customer_name,
            // "tempat_lahir_pemohon" => "Jambi",
            // "tanggal_lahir_pemohon" => "1989-07-25",
            // "alamat_pemohon" => "ini alamat pemohon",
            // "jenis_kelamin_pemohon" => "l",
            // "kewarganegaraan_pemohon" => "ID",
            // "pekerjaan_pemohon_value" => "001",
            // "status_pernikahan_pemohon_value" => "2",
            // "status_pisah_harta_pemohon" => "Pisah Harta",
            // "nik_pasangan" => "3174062507891237",
            // "nama_pasangan" => "Nama Bojo",
            // "status_tempat_tinggal_value" => "0",
            // "telepon_pemohon" => "123456789",
            // "hp_pemohon" => "082177777669",
            // "email_pemohon" => "prayantaalfian@gmail.com",
            // "jenis_pekerjaan_value" => "17",
            // "pekerjaan_value" => "18",
            // "nama_perusahaan" => "Nama Perusahaan 19",
            // "bidang_usaha_value" => "20",
            // "jabatan_value" => "21",
            // "lama_usaha" => "12",
            // "alamat_usaha" => "ini alamat usaha",
            // "jenis_penghasilan" => "Singe Income",
            // "gaji_bulanan_pemohon" => "8100000",
            // "pendapatan_lain_pemohon" => "7100000",
            // "gaji_bulanan_pasangan" => "2100000",
            // "pendapatan_lain_pasangan" => "1100000",
            // "angsuran" => "500000",
            // "jenis_kpp_value" => "KPR Perorangan PNS / BUMN",
            // "permohonan_pinjaman" => "151000000",
            // "uang_muka" => "51000000",
            // "jangka_waktu" => "240",
            // "jenis_dibiayai_value" => "123456789",
            // "sektor_ekonomi_value" => "123456789",
            // "project_value" => "1086",
            // "program_value" => "27",
            // "pihak_ketiga_value" => "1016",
            // "sub_pihak_ketiga_value" => "1",
            // "nama_keluarga" => "siSepupu",
            // "hubungan_keluarga" => "Sepupu",
            // "telepon_keluarga" => "123456789",
            // "jenis_kredit" => "KPR",
            // "tujuan_penggunaan_value" => "3",
            // "tujuan_penggunaan" => "Pembelian Rumah Baru",
            // "kode_cabang" => "0206",
            // "id_prescreening" => "12",
            // "nama_ibu" => "Ibu Terbaik",
            // "npwp_pemohon" => "36.930.247.6-409.000",
            // "nama_pengelola" => "Oblag",
            // "pn_pengelola" => "00139644",
            // "tempat_lahir_pemohon" => "Jambi",
            "tempat_lahir_pemohon" => $customer_detail['birth_place'] ? $customer_detail['birth_place'] : '',
            "tanggal_lahir_pemohon" => $customer_detail['birth_date'] ? $customer_detail['birth_date'] : '',
            "alamat_pemohon" => $customer_detail['address'] ? $customer_detail['address'] : '',
            // "jenis_kelamin_pemohon" => "l",
            "jenis_kelamin_pemohon" => $customer->gender ? $customer->gender : '', // L harusnya 0 atau 1 atau 2 atau 3
            "kewarganegaraan_pemohon" => $customer_detail['citizenship_id'] ? $customer_detail['citizenship_id'] : '',
            "pekerjaan_pemohon_value" => $customer_work['work_id'] ? $customer_work['work_id'] : '',
            // "status_pernikahan_pemohon_value" => "2",
            "status_pernikahan_pemohon_value" => $customer_detail['status_id'] ? $customer_detail['status_id'] : '', // Belum sama dengan value dari BRI
            "status_pisah_harta_pemohon" => $customer_finance['status_income']?$customer_finance['status_income'] :'',
            "nik_pasangan" => $customer_detail['couple_nik'] ? $customer_detail['couple_nik'] : '',
            "nama_pasangan" => $customer_detail['couple_name']  ? $customer_detail['couple_name'] : '',
            "status_tempat_tinggal_value" => $customer_detail['address_status_id'] ? $customer_detail['address_status_id'] : '',
            //"status_tempat_tinggal_value" => empty( $customer_detail->address_status ) ? '0' : $customer_detail->address_status,
            "telepon_pemohon" => $customer->phone  ? $customer->phone : '',
            "hp_pemohon" => $customer->mobile_phone  ? $customer->mobile_phone : '',
            "email_pemohon" => $customer->email ? $customer->email : '',
            "jenis_pekerjaan_value" => $customer_work['type_id'] ? $customer_work['type_id'] : '',
            "pekerjaan_value" => $customer_work['work_id'] ? $customer_work['work_id'] : '',
            "nama_perusahaan" => $customer_work['company_name'] ? $customer_work['company_name']:'',
            "bidang_usaha_value" => $customer_work['work_field_id'] ? $customer_work['work_field_id'] : '',
            "jabatan_value" => $customer_work['position_id']  ? $customer_work['position_id'] : '',
            "lama_usaha" => $customer_work['work_duration']  ? $customer_work['work_duration'] : '',
            "alamat_usaha" => $customer_work['office_address']  ? $customer_work['office_address'] : '',
            "jenis_penghasilan" =>  $customer_finance['status_finance'] ? $customer_finance['status_finance'] : '',
             // Tidak ada di design dan database
            "gaji_bulanan_pemohon" => $customer_finance['salary']  ? $customer_finance['salary'] : '',
            "pendapatan_lain_pemohon" => $customer_finance['other_salary']  ? $customer_finance['other_salary'] : '',
            "gaji_bulanan_pasangan" => $customer_finance['salary_couple']  ? $customer_finance['salary_couple'] : '',
            "pendapatan_lain_pasangan" => $customer_finance['other_salary_couple'] ? $customer_finance['other_salary_couple'] : '',
            "angsuran" => $customer_finance['loan_installment']  ? $customer_finance['loan_installment'] : '',
            "jenis_kpp_value" => $lkn->kpp_type  ? $lkn->kpp_type : '',
            "permohonan_pinjaman" => $kpr->request_amount  ? $kpr->request_amount : '',
            // "uang_muka" => "51000000",
            "uang_muka" => $kpr->dp ? ( ( $kpr->request_amount * $kpr->dp ) / 100 ) : '',
            "jangka_waktu" => $kpr->year ? ( $kpr->year * 12 ) : '',
            "jenis_dibiayai_value" => $lkn->type_financed  ? $lkn->type_financed : '', // Tidak ada di design dan database
            "sektor_ekonomi_value" => $lkn->economy_sector  ? $lkn->economy_sector : '',//"123456789", // Tidak ada di design dan database
            "project_value" => $lkn->project_list ? $lkn->project_list : '',//"1086", // Tidak ada di design dan database
            "program_value" => $lkn->program_list  ? $lkn->program_list : '',//"27", // Tidak ada di design dan database
            "pihak_ketiga_value" => $kpr->developer_id  ? $kpr->developer_id : '',
            "sub_pihak_ketiga_value" => "1", // Tidak ada di design dan database
            "nama_keluarga" => $customer_detail['emergency_name'] ? $customer_detail['emergency_name'] : '',
            "hubungan_keluarga" => $customer_detail['emergency_relation'] ? $customer_detail['emergency_relation'] : '',
            "telepon_keluarga" => $customer_detail['emergency_contact']  ? $customer_detail['emergency_contact'] : '',
            "jenis_kredit" => strtoupper( $this->product_type ),
            "tujuan_penggunaan_value" => $lkn->use_reason_id ? $lkn->use_reason_id : '', // Tidak ada di design dan database
            "tujuan_penggunaan" => $lkn->use_reason  ? $lkn->use_reason : '', // Tidak ada di design dan database
            "kode_cabang" => $this->branch_id ? $this->branch_id : '',
            "id_prescreening" => $lkn->id_prescreening  ? $lkn->id_prescreening : '', // Tidak ada di design dan database dan perlu sync dengan BRI
            "nama_ibu" => $customer_detail['mother_name'] ? $customer_detail['mother_name'] : '',
            "npwp_pemohon" => $lkn->id_prescreening  ? $lkn->id_prescreening : '', // Tidak ada di design dan database
            "nama_pengelola" => $this->ao_name ? $this->ao_name : '' , // Nama AO
            "pn_pengelola" => $this->ao_id ? $this->ao_id : '', //"00139644",
            "cif" => $this->cif_number ? $this->cif_number : ''
             //Informasi nomor CIF
        ];
        $request += $this->additional_parameters;
        */

        $endpoint = [
            ['InsertDataCif', 'fid_cif_las']
            , ['InsertDataCifSdn', null]
            , ['InsertDataAplikasi', 'fid_aplikasi']
            , ['InsertDataPrescreening', null]
            , ['InsertDataScoringKpr', null]
            , ['InsertDataTujuanKredit', null]
            , ['InsertDataMaster', null]
        ];

        $step = 1;
        $allRequest = array();
        $return = array(
            'status' => true
            , 'message' => ''
        );

        foreach ($endpoint as $value => $key) {
            \Log::info("Start Step " . $step);

            $request = $this->{"step".$step}($this->additional_parameters);
            $allRequest += $request;

            $sendRequest = ($step == 7 ? $allRequest : $request);

            \Log::info(json_encode($sendRequest));

            $set = $this->SentToBri( $sendRequest, $key[0], $key[1] );

            if (!$set['status']) {
                \Log::info('Error Step Ke -'.$step);
                $return = array(
                    'status' => false
                    , 'message' => $set[ 'message' ]
                );
                \Log::info($return);
                break;
            }

            \Log::info('Berhasil Step Ke -'.$step);
            $step++;
        }

        if ($step == 7) {
            $this->update( [ 'is_approved' => true ] );
        }
        return $return;
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating( function( $eform ) {
            $customer_detail = CustomerDetail::whereNik( $eform->nik )->first();
            $eform->user_id = $customer_detail->user_id;

            if( $user_input = Sentinel::getUser() ) {
                if( $user_input->roles->first()->slug == 'ao' ) {
                    $eform->ao_id = $user_input->id;
                }
            }
        } );
    }

    /**
     * Scope a query to filter eform.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter( $query, Request $request )
    {
        \Log::info("===================================================");

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['created_at', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "ref_number" ) {
            $sort = ['created_at', 'asc'];
        }

        $eform = $query->where( function( $eform ) use( $request, &$user ) {
            if( $request->has( 'status' ) ) {
                if( $request->status == 'Submit' ) {
                    $eform->whereIsApproved( true );
                } else if( $request->status == 'Initiate' ) {
                    $eform->has( 'visit_report' )->whereIsApproved( false );
                } else if( $request->status == 'Dispose' ) {
                    $eform->whereNotNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                } else if( $request->status == 'Rekomend' ) {
                    $eform->whereNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                } elseif ($request->status == 'Rejected') {
                    $eform->where('status_eform', 'Rejected');
                }
            }
        } );

        if ($request->has('customer_name')){
            $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id');
        }

        $eform = $query->where( function( $eform ) use( $request, &$user ) {
            if ($request->has('ref_number')) {
                $eform->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('ref_number').'%');
            }

            if ($request->has('customer_name')){
                $eform->orWhere('users.last_name', 'ilike', '%'.$request->input('search').'%')
                    ->orWhere('users.first_name', 'ilike', '%'.$request->input('search').'%');
            }
        } );

        if ($request->has('prescreening')) {
            $eform = $query->where( function( $eform ) use( $request, &$user ) {
                $prescreening = $request->input('prescreening');
                if (strtolower($prescreening) != 'all') {
                    $eform->Where('eforms.prescreening_status', $prescreening);
                }
            } );
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $eform = $query->where( function( $eform ) use( $request, &$user ) {
                $start_date = date('Y-m-d',strtotime($request->input('start_date')));
                $end_date = $request->has('end_date') ? date('Y-m-d',strtotime($request->input('end_date'))) : date('Y-m-d');

                $eform->where('eforms.created_at', '>=', $start_date . ' 00:00:00')
                ->where('eforms.created_at', '<=', $end_date . ' 23:59:59');
            } );
        }

        if ( !$request->has('is_screening') ) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                if ( $user['role'] == 'ao' ) {
                    $eform = $eform->where('eforms.ao_id', $user['pn']);

                }

                if ($request->has('branch_id')) {
                    $eform = $eform->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($request->input('branch_id')) );
                }
            } );

            if ( $user['role'] != 'ao' || $request->has('customer_name')) {
                if ( $request->has('customer_name') ) {
                    $eform = $eform->select( ['eforms.*', 'users.first_name', 'users.last_name'] );

                } else {
                    $eform = $eform->select([
                            'eforms.*'
                            , \DB::Raw(" case when ao_id is not null then 2 else 1 end as new_order ")
                        ])
                        ->orderBy('new_order', 'asc');

                }

            }
        }

        if ( $request->has('is_screening') ) {
            if ( $request->input('is_screening') != 'All' ) {
                $eform = $eform->where('eforms.is_screening', $request->input('is_screening'));

            }
        }

        if ( $request->has('product') ) {
            if ( $request->input('product') != 'All' ) {
                $eform = $eform->where('eforms.product_type', $request->input('product'));

            }
        }

        $eform = $eform->orderBy('eforms.'.$sort[0], $sort[1]);

        \Log::info($eform->toSql());

        return $eform;
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo( Customer::class, 'user_id' );
    }

    /**
     * The relation to visit report.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function visit_report()
    {
        return $this->hasOne( VisitReport::class, 'eform_id' );
    }

    /**
     * The relation to visit report.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kpr()
    {
        return $this->hasOne( KPR::class, 'eform_id' );
    }

    /**
     * Verify E-Form customer data.
     *
     * @return array
     */
    public static function verify( $token, $status )
    {
        $returnStatus = false;
        $target = static::where('token', $token)->first();

        if ($target) {
            $lastData = static::where('user_id', $target->user_id)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($lastData->token == $target->token) {
                $returnStatus = true;
                $target->update(['response_status' => $status]);
                $verifiedStatus = ($status == "approve" ? true : false);
                $target->customer->detail()->update(['is_verified' => $verifiedStatus]);
            }
        }

        return array(
            'message' => $returnStatus
            , 'contents' => $target
        );
    }

    /**
     * Generate token for verification.
     *
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function generateToken( $user_id )
    {
        $lastData = static::where( 'user_id', $user_id )
            ->orderBy( 'created_at', 'desc' )
            ->first();

        $lastData->update( [
            'token' => strtr(base64_encode(openssl_encrypt(date('y-m-d h:i:s'), 'AES-128-ECB', 'APImyBRI')), '+/=', '-_,')
            , 'response_status' => 'unverified'
        ]);

        return $lastData;
    }

    /**
     * [Sent To 7 End Point Bri]
     * @author erwan.akse@wgs.co.id
     * @param $request  Data User
     * @param $endpoint End Point BRI
     * @param $value    Return Data From Bri
     * @return true|false Is Sent Success|Failed
     */
    public function SentToBri($request, $endpoint, $value = null)
    {
        $post_to_bri = Asmx::setEndpoint( $endpoint )
            ->setBody( [
                'Request' => json_encode( $request )
            ] )
            ->post( 'form_params' );

        $return = array(
            'status' => false
            , 'message' => isset($post_to_bri[ 'contents' ]) ? $post_to_bri[ 'contents' ] : ''
        );

        \Log::info('============================================================================================');
        \Log::info($endpoint);
        \Log::info($post_to_bri);
        \Log::info('============================================================================================');

        if ( $post_to_bri[ 'code' ] == 200 ) {
            if ($value != null) {
                $this->additional_parameters += [ $value => $post_to_bri[ 'contents' ] ] ;
                $this->save();
            }
            $return = array(
                'status' => true
                , 'message' => ''
            );
        }

        return $return;
    }

    /**
     * Generate Parameters for step 1.
     *
     * @param array $data
     * @return array $request
     */
    public function step1($data)
    {
        \Log::info("step1");
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $customer_work = (object) $customer->work;
        $customer_contact = (object) $customer->contact;
        $lkn = $this->visit_report;
        $year = !( $customer_work->work_duration ) ? 0 : $customer_work->work_duration;
        $mount = !( $customer_work->work_duration_month ) ? 0 : $customer_work->work_duration_month;
        $lama_usaha = $year *12 + $mount;

        $request = $data + [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->customer_name,
            "tempat_lahir_pemohon" => $customer_detail->birth_place ? $customer_detail->birth_place : '',
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            // "alamat_pemohon" => !( $customer_detail->address ) ? '' : $customer_detail->address,
            "alamat_pemohon" => !( $customer_detail->current_address ) ? '' : $customer_detail->current_address,
            "jenis_kelamin_pemohon" => !( $customer->gender_sim ) ? '' : $customer->gender_sim,
            "kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "pekerjaan_pemohon_value" => !( $customer_work->work_id ) ? '' : $customer_work->work_id,
            "status_pernikahan_pemohon_value" => !( $customer_detail->status_id ) ? '' : $customer_detail->status_id,
            "nik_pasangan" => !( $customer_detail->couple_nik ) ? '' : $customer_detail->couple_nik,
            "nama_pasangan" => !( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "status_tempat_tinggal_value" => !( $customer_detail->address_status_id ) ? '0' : $customer_detail->address_status_id,
            "telepon_pemohon" => !( $customer->phone ) ? '' : $customer->phone,
            "hp_pemohon" => !( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => !( $customer->email ) ? '' : $customer->email,
            "nama_perusahaan" => !( $customer_work->company_name ) ? '' : $customer_work->company_name,
            "lama_usaha" => $lama_usaha,
            "nama_keluarga" => !( $customer_contact->emergency_name ) ? '' : $customer_contact->emergency_name,
            "telepon_keluarga" => !( $customer_contact->emergency_contact ) ? '' : $customer_contact->emergency_contact,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "npwp_pemohon" => !( $lkn->npwp_number ) ? '' : $lkn->npwp_number,
            "cif" => !( $customer_detail->cif_number ) ? '' : $customer_detail->cif_number
        ];

        return $request;
    }

    /**
     * Generate Parameters for step 2.
     *
     * @param array $data
     * @return array $request
     */
    public function step2($data)
    {
        \Log::info("step2");
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $customer_work = (object) $customer->work;
        $lkn = $this->visit_report;

        $request = $data + [
            "kode_cabang" => !( $this->branch_id ) ? '' : $this->branch_id,
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->customer_name,
            "jenis_kelamin_pemohon" => !( $customer->gender ) ? '' : $customer->gender,
            "kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "tempat_lahir_pemohon" => $customer_detail->birth_place ? $customer_detail->birth_place : '',
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "status_pernikahan_pemohon_value" => !( $customer_detail->status_id ) ? '' : $customer_detail->status_id,
            // "alamat_pemohon" => !( $customer_detail->address ) ? '' : $customer_detail->address,
            "alamat_pemohon" => !( $customer_detail->current_address ) ? '' : $customer_detail->current_address,
            "telepon_pemohon" => !( $customer->phone ) ? '' : $customer->phone,
            "hp_pemohon" => !( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => !( $customer->email ) ? '' : $customer->email,
            "jenis_pekerjaan_value" => !( $customer_work->type_id ) ? '' : $customer_work->type_id,
            "nama_perusahaan" => !( $customer_work->company_name ) ? '' : $customer_work->company_name,
            "bidang_usaha_value" => !( $customer_work->work_field_id ) ? '' : $customer_work->work_field_id,
            "jabatan_value" => !( $customer_work->position_id ) ? '' : $customer_work->position_id,
            "npwp_pemohon" => !( $lkn->npwp_number ) ? '' : $lkn->npwp_number,
            "alamat_usaha" => !( $customer_work->office_address ) ? '' : $customer_work->office_address
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 3.
     *
     * @param array $data
     * @return array $request
     */
    public function step3($data)
    {
        \Log::info("step3");
        $kpr = $this->kpr;
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;
        $lkn = $this->visit_report;

        $request = $data + [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "jenis_kredit" => strtoupper( $this->product_type ),
            "kode_cabang" => !( $this->branch_id ) ? '' : $this->branch_id,
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->customer_name,
            "nama_pasangan" => !( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "jenis_kpp_value" => !( $lkn->kpp_type_name ) ? '' : $lkn->kpp_type_name,
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "program_value" => !( $lkn->program_list ) ? '' : $lkn->program_list,
            "project_value" => !( $lkn->project_list ) ? '' : $lkn->project_list,
            "pihak_ketiga_value" => !( $kpr->developer_id ) ? '' : $kpr->developer_id,
            "sub_pihak_ketiga_value" => "1"
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 4.
     *
     * @param array $data
     * @return array $request
     */
    public function step4($data)
    {
        \Log::info("step4");
        return $data;
    }

    /**
     * Generate Parameters for step 5.
     *
     * @param array $data
     * @return array $request
     */
    public function step5($data)
    {
        \Log::info("step5");
        $kpr = $this->kpr;
        $customer = clone $this->customer;
        $customer_finance = (object) $customer->Financial;

        $request = $data + [
            "jenis_kredit" => strtoupper( $this->product_type ),
            "angsuran" => !( $customer_finance->loan_installment ) ? '' : str_replace(',', '.', str_replace('.', '', $customer_finance->loan_installment)),
            "pendapatan_lain_pemohon" => !( $kpr->income_salary ) ? '' : str_replace(',', '.', str_replace('.', '', $kpr->income_salary)),
            "jangka_waktu" => $kpr->year,
            "permohonan_pinjaman" => !( $kpr->request_amount ) ? '' : $kpr->request_amount,
            "uang_muka" => ( ( $kpr->request_amount * $kpr->dp ) / 100 ),
            "gaji_bulanan_pemohon" => !( $kpr->income ) ? '' : str_replace(',', '.', str_replace('.', '', $kpr->income))
        ];

        return $request;
    }

    /**
     * Generate Parameters for step 6.
     *
     * @param array $data
     * @return array $request
     */
    public function step6($data)
    {
        \Log::info("step6");
        $lkn = $this->visit_report;

        $request = $data + [
            "tujuan_penggunaan_value" => !( $lkn->use_reason ) ? '' : $lkn->use_reason,
            "tujuan_penggunaan" => !( $lkn->use_reason_name ) ? '' : $lkn->use_reason_name
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 7.
     *
     * @param array $data
     * @return array $request
     */
    public function step7($data)
    {
        \Log::info("step7");
        $request = $data + [
            "nama_pengelola" => !($this->ao_name) ? '': $this->ao_name ,
            "pn_pengelola" => !($this->ao_id) ? '': $this->ao_id
        ];
        return $request;
    }
}
