<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\UserNotification;
use App\Models\Developer;
use App\Models\PropertyItem;
use App\Models\Collateral;
use App\Models\Appointment;
use Carbon\Carbon;
use Sentinel;
use Asmx;
use RestwsHc;
use DB;
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
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address', 'token', 'status', 'response_status', 'recommended', 'recommendation', 'is_screening', 'pefindo_score', 'uploadscore', 'ket_risk', 'dhn_detail', 'sicd_detail', 'status_eform', 'branch', 'ao_name', 'ao_position', 'pinca_name', 'pinca_position', 'prescreening_name', 'prescreening_position', 'selected_sicd','ref_number', 'sales_dev_id'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'status', 'aging', 'is_visited', 'pefindo_color' ];

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
            $path = public_path( 'uploads/' . $this->nik . '/' );
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
        if ($this->customer) {
            return str_replace('"', '', str_replace("'", '', $this->customer->fullname));
        }

        return '';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getMobilePhoneAttribute()
    {
        if ($this->customer) {
            return $this->customer->mobile_phone;
        }

        return '';
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
    // public function getAoNameAttribute()
    // {
    //     if ( $this->ao_id ) {
    //         $AO = \RestwsHc::getUser( $this->ao_id );
    //         return $AO[ 'name' ];
    //     }

    //     return null;
    // }

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
        if( $this->is_approved && $this->customer["detail"]["is_verified"] ) {
        // if( $this->is_approved && $this->customer->detail->is_verified ) {
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
        $value = $this->pefindo_score;
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
        $days = 0;

        if ($this->created_at) {
            $days = $this->created_at->diffInDays();

        }
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
        $developer_id = env('DEVELOPER_KEY',1);
        $developer_name = env('DEVELOPER_NAME','Non Kerja Sama');
        if ( $request->is_approved ) {
            //di update kalo collateral udah jalan
            // if ($eform->kpr->developer_id != $developer_id && $eform->kpr->developer_name != $developer_name)
            // {
                $result = $eform->insertCoreBRI();
                if ($result['status']) {
                    $eform->kpr()->update(['is_sent'=> true]);
                }
            // }
            // else
            // {
            //     $eform->kpr()->update(['is_sent'=> false]);
            //     $result['status'] = true;
            // }

            if ($result['status']) {
                $eform->update( [
                    'pros' => $request->pros,
                    'cons' => $request->cons,
                    'pinca_position' => $request->pinca_position,
                    'pinca_name' => $request->pinca_name,
                    'recommendation' => $request->recommendation,
                    'recommended' => $request->recommended == "yes" ? true : false,
                    'is_approved' => $request->is_approved,
                    'status_eform' => 'approved'
                    ] );
            if ($eform->kpr->developer_id != $developer_id && $eform->kpr->developer_name != $developer_name)
                PropertyItem::setAvailibility( $eform->kpr->property_item, "sold" );
            }

        } else {
            if ($eform->kpr->developer_id != $developer_id && $eform->kpr->developer_name != $developer_name)
                PropertyItem::setAvailibility( $eform->kpr->property_item, "available" );

            $eform->update( [
                'pros' => $request->pros,
                'cons' => $request->cons,
                'pinca_position' => $request->pinca_position,
                'pinca_name' => $request->pinca_name,
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
        $customer_finance =  $customer->financial;
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
            "jenis_kelamin_pemohon" => $customer->gender_sim ? $customer->gender_sim : '', // L harusnya 0 atau 1 atau 2 atau 3
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
            //, ['InsertDataAgunanModel71',null]
            //, ['InsertIntoReviewer',null]
            //, ['InsertDataAgunanTanahRumahTinggal',null]

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

        static::created( function( $eform ) {
            $scheduleData = array(
                'title' => $eform->ref_number
                , 'appointment_date' => $eform->appointment_date
                , 'user_id' => $eform->user_id
                , 'ao_id' => $eform->ao_id
                , 'eform_id' => $eform->id
                , 'ref_number' => $eform->ref_number
                , 'address' => $eform->address
                , 'latitude' => $eform->longitude
                , 'longitude' => $eform->latitude
                , 'desc' => '-'
                , 'status' => 'waiting'
            );

            $schedule = Appointment::create($scheduleData);
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

                } elseif ($request->status == 'Rejected' || $request->status == 'Approval1' || $request->status == 'Approval2') {
                    $eform->where('status_eform', $request->status);

                }
            }
        } );

        if ($request->has('search')) {
            $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
                ->where( function( $eform ) use( $request, &$user ) {
                    $eform->orWhere('users.last_name', 'ilike', '%'.strtolower($request->input('search')).'%')
                        ->orWhere('users.first_name', 'ilike', '%'.strtolower($request->input('search')).'%')
                        ->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('search').'%');
                } );

        } else {
            if ($request->has('customer_name')){
                $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
                    ->where( function( $eform ) use( $request, &$user ) {
                        $eform->orWhere('users.last_name', 'ilike', '%'.strtolower($request->input('customer_name')).'%')
                            ->orWhere('users.first_name', 'ilike', '%'.strtolower($request->input('customer_name')).'%');
                    } );
            }

            if ($request->has('ref_number')) {
                $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                    $eform->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('ref_number').'%');
                } );
            }
        }


        if ($request->has('prescreening')) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
                $prescreening = $request->input('prescreening');
                if (strtolower($prescreening) != 'all') {
                    $eform->Where('eforms.prescreening_status', $prescreening);
                }
            } );
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $eform = $eform->where( function( $eform ) use( $request, &$user ) {
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
            \Log::info("===========================role===================================");
            \Log::info($user['role']);
            if ( $user['role'] != 'ao' || $request->has('search')) {
                if ( $request->has('search') ) {
                    $eform = $eform->select( ['eforms.*', 'users.first_name', 'users.last_name'] );

                }
            }
        }

        if ( $request->has('product') ) {
            if ( $request->input('product') != 'All' ) {
                $eform = $eform->where('eforms.product_type', $request->input('product'));

            }
        }

        $eform = $eform->orderBy('eforms.'.$sort[0], $sort[1]);

        \Log::info($eform->toSql());
        \Log::info($eform->getBindings());

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
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users()
    {
        return $this->belongsTo( User::class, 'user_id' );
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
     * Update EForm from CLAS.
     *
     * @return array
     */
    public static function updateCLAS( $ref_number, $status )
    {
        $returnStatus = false;
        $target = static::where('ref_number', $ref_number)->first();

        if ($target) {
            $target->update([
                'is_approved' => ( $status == 'Approval1' ? true : false )
                , 'status_eform' => $status
            ]);

            $returnStatus = "EForm berhasil di update.";

            if ($target->kpr) {
                PropertyItem::setAvailibility( $target->kpr->property_item, $status == 'Approval1' ? "sold" : "available" );
            }
        }

        return array(
            'message' => $returnStatus
            , 'contents' => $target
        );
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
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "tempat_lahir_pemohon" => $this->reformatString( $customer_detail->birth_place ),
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "alamat_pemohon" => !( $customer_detail->address ) ? '' : substr($customer_detail->address, 40),
            "alamat_domisili" => !( $customer_detail->current_address ) ? '' : substr($customer_detail->current_address, 40),
            "jenis_kelamin_pemohon" => !( $customer->gender_sim ) ? '' : strtolower($customer->gender_sim),
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
            "hubungan_keluarga" => !( $customer_contact->emergency_relation ) ? '' : $customer_contact->emergency_relation,
            "telepon_keluarga" => !( $customer_contact->emergency_contact ) ? '' : $customer_contact->emergency_contact,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "npwp_pemohon" => !( $lkn->npwp_number ) ? '' : $lkn->npwp_number,
            "cif" => !( $customer_detail->cif_number ) ? '' : $customer_detail->cif_number,
            "status_pisah_harta_pemohon" => !( $lkn->source_income ) ? '' : ($lkn->source_income == "Single Income" ? 'Tidak' : 'Pisah Harta'),
            "sektor_ekonomi_value" => !( $lkn->economy_sector ) ? '' : $lkn->economy_sector,
            "Status_gelar_cif" => !( $lkn->title ) ? '' : $lkn->title,
            "Kecamatan_cif" => 'kecamatan',
            "Kelurahan_cif" => 'kelurahan',
            "Kode_pos_cif" => '40000',
            "Lokasi_dati_cif" => $this->reformatCity( $customer_detail->city ),
            "Usia_mpp" => !( $lkn->age_of_mpp ) ? '' : $lkn->age_of_mpp,
            "Bidang_usaha_value" => !( $lkn->economy_sector ) ? '' : $lkn->economy_sector,
            "Status_kepegawaian_value" => !( $lkn->employment_status ) ? '' : $lkn->employment_status,
            "Pernah_pinjam_bank_lain_value" => !( $lkn->loan_history_accounts ) ? '' : $lkn->loan_history_accounts,
            'agama_value_pemohon' => !( $lkn->religion ) ? '' : $lkn->religion,
            'telepon_tempat_kerja' => !( $lkn->office_phone ) ? '' : $lkn->office_phone,
            "jenis_kpp_value" => !( $lkn->kpp_type_name ) ? '' : $lkn->kpp_type_name
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
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4),
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "jenis_kelamin_pemohon" => !( $customer->gender_sim ) ? '' : strtolower($customer->gender_sim),
            "kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "tempat_lahir_pemohon" => $this->reformatString( $customer_detail->birth_place ),
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "nama_ibu" => !( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "status_pernikahan_pemohon_value" => !( $customer_detail->status_id ) ? '' : $customer_detail->status_id,
            "alamat_pemohon" => !( $customer_detail->address ) ? '' : substr($customer_detail->address, 40),
            "status_tempat_tinggal_value" => !( $customer_detail->address_status_id ) ? '' : $customer_detail->address_status_id,
            "alamat_domisili" => !( $customer_detail->current_address ) ? '' : substr($customer_detail->current_address, 40),
            "telepon_pemohon" => !( $customer->phone ) ? '' : $customer->phone,
            "hp_pemohon" => !( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => !( $customer->email ) ? '' : $customer->email,
            "jenis_pekerjaan_value" => !( $customer_work->type_id ) ? '' : $customer_work->type_id,
            "nama_perusahaan" => !( $customer_work->company_name ) ? '' : $customer_work->company_name,
            "bidang_usaha_value" => !( $customer_work->work_field_id ) ? '' : $customer_work->work_field_id,
            "jabatan_value" => !( $customer_work->position_id ) ? '' : $customer_work->position_id,
            "npwp_pemohon" => !( $lkn->npwp_number ) ? '' : $lkn->npwp_number,
            'agama_value_pemohon' => !( $lkn->religion ) ? '' : $lkn->religion,
            "alamat_usaha" => !( $customer_work->office_address ) ? '' : $customer_work->office_address,
            'telepon_tempat_kerja' => !( $lkn->office_phone ) ? '' : $lkn->office_phone,
            'tujuan_membuka_rekening_value' => 'T2',
            'sumber_utama_value' => !( $lkn->source ) ? '00099' : ($lkn->source == "fixed" ? '00011' : '00012')
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

        $developer = Developer::find($kpr->developer_id);

        $request = $data + [
            "nik_pemohon" => !( $this->nik ) ? '' : $this->nik,
            "jenis_kredit" => strtoupper( $this->product_type ),
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4),
            "nama_pemohon" => !( $this->customer_name ) ? '' : $this->reformatString( $this->customer_name ),
            "nama_pasangan" => !( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "jenis_kpp_value" => !( $lkn->kpp_type_name ) ? '' : $lkn->kpp_type_name,
            "tanggal_lahir_pemohon" => !( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "program_value" => !( $lkn->program_list ) ? '' : $lkn->program_list,
            "project_value" => !( $lkn->project_list ) ? '' : $lkn->project_list,
            "pihak_ketiga_value" => !( $developer ) ? '' : ( $developer->dev_id_bri ? $developer->dev_id_bri : '1' ),
            "sub_pihak_ketiga_value" => '1'
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
        $lkn = $this->visit_report;
        $customer = clone $this->customer;
        $customer_finance = (object) $customer->financial;

        $request = $data + [
            "jenis_kredit" => strtoupper( $this->product_type ),
            "angsuran" => !( $customer_finance->loan_installment ) ? '0' : round( str_replace(',', '.', str_replace('.', '', $customer_finance->loan_installment)) ),
            "pendapatan_lain_pemohon" => !( $lkn->income_salary ) ? '0' : round( str_replace(',', '.', str_replace('.', '', $lkn->income_salary)) ),
            "jangka_waktu" => $kpr->year,
            "Jenis_dibiayai_value" => !( $lkn->type_financed ) ? '0' : $lkn->type_financed,
            "permohonan_pinjaman" => !( $kpr->request_amount ) ? '0' : $kpr->request_amount,
            "uang_muka" => round( ( $kpr->request_amount * $kpr->dp ) / 100 ),
            "gaji_bulanan_pemohon" => !( $lkn->income ) ? '0' : round( str_replace(',', '.', str_replace('.', '', $lkn->income)) ),
            "jenis_penghasilan" =>  !( $lkn->source_income ) ? 'Single Income' : $lkn->source_income,
            "gaji_bulanan_pasangan" => !( $customer_finance->salary_couple ) ? '0' : round( str_replace(',', '.', str_replace('.', '', $customer_finance->salary_couple)) ),
            "pendapatan_lain_pasangan" => !( $customer_finance->other_salary_couple ) ? '0' : round( str_replace(',', '.', str_replace('.', '', $customer_finance->other_salary_couple)) ),
            "harga_agunan" => !($kpr->price) ? '0' : round( str_replace(',', '.', str_replace('.', '', $kpr->price)) )
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
        $lkn = $this->visit_report;

        $request = $data + [
            "nama_pengelola" => !($this->ao_name) ? '': $this->ao_name ,
            "pn_pengelola" => !($this->ao_id) ? '': $this->ao_id
        ];
        return $request;
    }

    /**
     * Generate Parameters for step 8.
     *
     * @param array $data
     * @return array $request
     */
    public function step8($data)
    {
        \Log::info("step8");
        $kpr = $this->kpr;
        $collateral = Collateral::WithAll()->where('property_id',$kpr->property_id)->firstOrFail();
        $otsInArea = $collateral->otsInArea;
        $otsLetter = $collateral->otsLetter;
        $otsBuilding = $collateral->otsBuilding;
        $otsEnvironment = $collateral->otsEnvironment;
        $otsValuation = $collateral->otsValuation;
        $otsOther = $collateral->otsOther;
        $customer = clone $this->customer;
        $customer_detail = (object) $customer->personal;

        $request = $data + [
            //ots Area
            "Lokasi_tanah_agunan" => !($otsInArea->location) ? '0' : $otsInArea->location,
            "Rt_agunan" => !($otsInArea->rt) ? '0' : $otsInArea->rt,
            "Rw_agunan" => !($otsInArea->rw) ? '0' : $otsInArea->rw,
            "Kelurahan_agunan"=> !($otsInArea->sub_district) ? '0' : $otsInArea->sub_district,
            "Kecamatan_agunan"=> !($otsInArea->district) ? '0' : $otsInArea->district,
            "Kabupaten_kotamadya_agunan" => !($otsInArea->city_id) ? '0' : $otsInArea->city_id,
            "Jarak_agunan" => !($otsInArea->distance) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->distance))),
            "Jarak_satuan_agunan" => 'Kilometer',
            "Jarak_dari_agunan" => 'Pusat Kota',
            "Kewarganegaraan_pemohon" => !( $customer_detail->citizenship_id ) ? '0' : $customer_detail->citizenship_id,
            "Posisi_terhadap_jalan_agunan_value"=> !($otsInArea->position_from_road) ? '0' : $otsInArea->position_from_road,
            "Posisi_terhadap_jalan_agunan" => !($otsInArea->position_from_road) ? '0' : $otsInArea->position_from_road,
            "Batas_utara_tanah_agunan" => !($otsInArea->north_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->north_limit))),
            "Batas_timur_tanah_agunan" => !($otsInArea->east_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->east_limit))),
            "Batas_selatan_tanah_agunan" => !($otsInArea->south_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->south_limit))),
            "Batas_barat_tanah_agunan" => !($otsInArea->west_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->west_limit))),
            "Keterangan_lain_agunan" => !($otsInArea->another_information) ? '0' : $otsInArea->another_information,
            "Bentuk_tanah_value" => !($otsInArea->ground_type) ? '0' : $otsInArea->ground_type,
            "Permukaan_tanah_agunan_value" => !($otsInArea->ground_level) ? '0' : $otsInArea->ground_level,
            "Luas_tanah_sesuai_identifikasi_lapangan_agunan" => !($otsInArea->surface_area) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->surface_area))),
            //ots Letter
            "Jenis_surat_tanah_agunan_value" => !($otsLetter->type) ? '0' : $otsLetter->type,
            "No_surat_tanah" => !($otsLetter->number) ? '0' : $otsLetter->number,
            "Tanggal_surat_tanah_agunan" => !($otsLetter->date) ? '0' : $otsLetter->date,
            "Atas_nama_agunan" => !($otsLetter->on_behalf_of) ? '0' : $otsLetter->on_behalf_of,
            "Hak_atas_tanah_agunan_value" => !($otsLetter->authorization_land) ? '0' : $otsLetter->authorization_land,
            "Masa_hak_atas_tanah_agunan" => !($otsLetter->duration_land_authorization) ? '0' : $otsLetter->duration_land_authorization,
            "Kemampuan_perpanjangan_hak_atas_tanah_agunan_value" => '0',//tidak ada di table
            "Kecocokan_data_kantor_agraniabpn_agunan" => !($otsLetter->match_bpn) ? '0' : $otsLetter->match_bpn,
            "Kecocokan_data_kantor_agraniabpn_agunan_value" => !($otsLetter->match_bpn) ? '0' : $otsLetter->match_bpn,
            "Nama_kantor_agrariabpn_agunan"=> !($otsLetter->bpn_name) ? '0' : $otsLetter->bpn_name,
            "Kecocokan_pemeriksaan_lokasi_tanah_lapangan_agunan_value" => !($otsLetter->match_area) ? '0' : $otsLetter->match_area,
            "Kecocokan_batas_tanah_lapangan_agunan_value" => !($otsLetter->match_limit_in_area) ? '0' : $otsLetter->match_limit_in_area,
            "Luas_tanah_berdasarkan_surat_tanah_agunan" => !($otsLetter->surface_area_by_letter) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsLetter->surface_area_by_letter))),
            //otsBuilding
            "No_ijin_mendirikan_bangunan_agunan" => !($otsBuilding->permit_number) ? '0' : $otsBuilding->permit_number,
            "Tanggal_ijin_mendirikan_bangunan_agunan" => !($otsBuilding->permit_date) ? '0' : $otsBuilding->permit_date,
            "Atas_nama_ijin_mendirikan_bangunan_agunan" => !($otsBuilding->on_behalf_of) ? '0' : $otsBuilding->on_behalf_of,
            "Jenis_bangunan_agunan_value" => !($otsBuilding->type) ? '0' : $otsBuilding->type,
            "Jumlah_bangunan_agunan" => !($otsBuilding->count) ? '0' : $otsBuilding->count,
            "Luas_bangunan_agunan" => !($otsBuilding->spacious) ? '0' : $otsBuilding->spacious,
            "Tahun_bangunan_agunan" => !($otsBuilding->year) ? '0' : $otsBuilding->year,
            "Uraian_bangunan_agunan" => !($otsBuilding->description) ? '0' : $otsBuilding->description,
            "Batas_utara_bangunan_agunan" => !($otsBuilding->north_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->north_limit))),
            "Batas_utara_bangunan_agunan1" => !($otsBuilding->north_limit_from) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->north_limit_from))),
            "Batas_timur_bangunan_agunan" => !($otsBuilding->east_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->east_limit))),
            "Batas_timur_bangunan_agunan1" => !($otsBuilding->east_limit_from) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->east_limit_from))),
            "Batas_selatan_bangunan_agunan" => !($otsBuilding->south_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->south_limit))),
            "Batas_selatan_bangunan_agunan1" => !($otsBuilding->south_limit_form) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->south_limit_form))),
            "Batas_barat_bangunan_agunan" => !($otsBuilding->west_limit) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->west_limit))),
            "Batas_barat_bangunan_agunan1" => !($otsBuilding->west_limit_from) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsBuilding->west_limit_from))),
            //otsEnvironment
            "Peruntukan_bangunan_agunan_value" => !($otsEnvironment->designated_land) ? '0' : $otsEnvironment->designated_land,
            "Fasilitas_umum_yang_ada_agunan_pln" => !($otsEnvironment->designated_pln) ? '0' : $otsEnvironment->designated_pln,
            "Fasilitas_umum_yang_ada_agunan_pam" => !($otsEnvironment->designated_pam) ? '0' : $otsEnvironment->designated_pam,
            "Fasilitas_umum_yang_ada_agunan_telepon" => !($otsEnvironment->designated_phone) ? '0' : $otsEnvironment->designated_phone,
            "Fasilitas_umum_yang_ada_agunan_telex" => !($otsEnvironment->designated_telex) ? '0' : $otsEnvironment->designated_telex,
            "Fasilitas_umum_lain_agunan" => !($otsEnvironment->other_designated) ? '0' : $otsEnvironment->other_designated,
            "Saran_transportasi_agunan" => !($otsEnvironment->transportation) ? '0' : $otsEnvironment->transportation,
            "Jarak_dari_agunan" => !($otsEnvironment->distance_from_transportation) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsEnvironment->distance_from_transportation))),
            "Lain_lain_agunan_value" => '0',
            "Petunjuk_lain_agunan" => !($otsEnvironment->other_guide) ? '0' : $otsEnvironment->other_guide,
            //valuation
            "Tanggal_penilaian_npw_tanah_agunan" => !($otsValuation->scoring_land_date) ? '0' : $otsValuation->scoring_land_date,
            "Npw_tanah_agunan" => !($otsValuation->npw_land) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->npw_land))),
            "Nl_tanah_agunan" => !($otsValuation->nl_land) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->nl_land))),
            "Pnpw_tanah_agunan" => !($otsValuation->pnpw_land) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnpw_land))),
            "Pnl_tanah_agunan" => !($otsValuation->pnl_land) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnl_land))),
            "Tanggal_penilaian_npw_bangunan_agunan" => !($otsValuation->scoring_building_date) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->scoring_building_date))),
            "Npw_bangunan_agunan" => !($otsValuation->npw_building) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->npw_building))),
            "Nl_bangunan_agunan" => !($otsValuation->nl_building) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->nl_building))),
            "Pnpw_bangunan_agunan" => !($otsValuation->pnpw_building) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnpw_building))),
            "Pnl_bangunan_agunan" => !($otsValuation->pnl_building) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnl_building))),
            "Tanggal_penilaian_npw_tanah_bangunan_agunan"=> !($otsValuation->scoring_all_date) ? '0' : $otsValuation->scoring_all_date,
            "Npw_tanah_bangunan_agunan" => !($otsValuation->npw_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->npw_all))),
            "Nl_tanah_bangunan_agunan" => !($otsValuation->nl_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->nl_all))),
            "Pnpw_tanah_bangunan_agunan" => !($otsValuation->pnpw_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnpw_all))),
            "Pnl_tanah_bangunan_agunan" => !($otsValuation->pnl_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnl_all))),
            //otsOther
            "Jenis_ikatan_agunan_value" => !($otsOther->bond_type) ? '0' : $otsOther->bond_type,
            "Penggunaan_bangunan_sesuai_fungsinya_agunan_value" => !($otsOther->use_of_building_function) ? '0' : $otsOther->use_of_building_function,
            "Penggunaan_bangunan_sesuai_optimal_agunan_value" => !($otsOther->optimal_building_use) ? '0' : $otsOther->optimal_building_use,
            //"Peruntukan_bangunan_agunan_value" => '0',//tidak ada di table
            "Peruntukan_tanah_agunan_value" => !($otsEnvironment->designated_land) ? '0' : $otsEnvironment->designated_land,
            "jarak_posisi_terhadap_jalan"=>!($otsInArea->distance_of_position) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsInArea->distance_of_position))),
            "Nama_debitur_agunan" => !( $this->customer_name ) ? '' : $this->customer_name,
            "Biaya_sewa_agunan" => '0',//tidak ada di table
            "Hal_perludiketahui_bank_agunan" => !($otsOther->things_bank_must_know) ? '0' : $otsOther->things_bank_must_know,
            //"fid_aplikasi" => '0',
            "uid_ao"=> '0'
            //"uid_ao"=>!($collateral->staff_id) ? '0' : $collateral->staff_id

        ];
        return $request;
    }

     /**
     * Generate Parameters for step 9.
     *
     * @param array $data
     * @return array $request
     */
    public function step9($data)
    {
        \Log::info("step9");
        return $data + [
            "kode_cabang" => !( $this->branch_id ) ? '' : substr('0000'.$this->branch_id, -4)
        ];

    }
     /**
     * Generate Parameters for step 9.
     *
     * @param array $data
     * @return array $request
     */
    public function step10($data)
    {
        \Log::info("step10");
        $kpr = $this->kpr;
        $collateral = Collateral::WithAll()->where('property_id',$kpr->property_id)->firstOrFail();
        $otsInArea = $collateral->otsInArea;
        $otsLetter = $collateral->otsLetter;
        $otsBuilding = $collateral->otsBuilding;
        $otsEnvironment = $collateral->otsEnvironment;
        $otsValuation = $collateral->otsValuation;
        $otsOther = $collateral->otsOther;

        $request = $data + [
            "Fid_agunan" => '0',
            //"Fid_cif_las" => '',
            "Nama_debitur_agunan_rt" => !( $this->customer_name ) ? '' : $this->customer_name,
            "Jenis_agunan_value_rt" => 'Rumah Tinggal',
            "Status_agunan_value_agunan_rt" => 'Ditempati Sendiri',
            "Deskripsi_agunan_rt" => !($otsBuilding->description) ? '0' : $otsBuilding->description,
            "Jenis_mata_uang_agunan_rt" => 'IDR',
            "Nama_pemilik_agunan_rt" => !($otsLetter->on_behalf_of) ? '0' : $otsLetter->on_behalf_of,
            "Status_bukti_kepemilikan_value_agunan_rt" => !($otsLetter->authorization_land) ? '0' : $otsLetter->authorization_land,
            "Nomor_bukti_kepemilikan_agunan_rt" => !($otsLetter->number) ? '0' : $otsLetter->number,
            "Tanggal_bukti_kepemilikan_agunan_rt" => !($otsLetter->date) ? '0' : date('dmY', strtotime($otsLetter->date)),
            "Tanggal_jatuh_tempo_agunan_rt"=> !($otsLetter->duration_land_authorization) ? '0' : date('dmY', strtotime($otsLetter->duration_land_authorization)),
            "Alamat_agunan_rt" => !($kpr->home_location) ? '0': str_replace("'", "",$kpr->home_location),
            "Kelurahan_agunan_rt" => !($otsInArea->sub_district) ? '0' : $otsInArea->sub_district,
            "Kecamatan_agunan_rt" => !($otsInArea->district) ? '0' : $otsInArea->district,
            "Lokasi_agunan_rt" =>!($otsInArea->location) ? '0' : $otsInArea->location,
            "Nilai_pasar_wajar_agunan_rt"=>!($otsValuation->npw_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->npw_all))),
            "Nilai_likuidasi_agunan_rt"=>!($otsValuation->nl_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->nl_all))),
            "Proyeksi_nilai_pasar_wajar_agunan_rt"=>!($otsValuation->pnpw_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnpw_all))),
            "Proyeksi_nilai_likuidasi_agunan_rt" => !($otsValuation->pnl_all) ? '0' : round( str_replace(',', '.', str_replace('.', '',$otsValuation->pnl_all))),
            "Nilai_likuidasi_saat_realisasi_agunan_rt"=>'0',
            "Nilai_jual_obyek_pajak_agunan_rt" =>'0',// no pokok wajib pajak
            "Penilaian_appraisal_dilakukan_oleh_value_agunan_rt"=>'bank',// bank and independent
            "Penilai_independent_agunan_rt"=>'0',
            "Tanggal_penilaian_terakhir_agunan_rt"=>'0',//!($otsValuation->scoring_all_date) ? '0' : $otsValuation->scoring_all_date,
            "Jenis_pengikatan_value_agunan_rt" => '01',//!($otsOther->bond_type) ? '0' : $otsOther->bond_type,
            "No_bukti_pengikatan_agunan_rt" => '0',//taidak
            "Nilai_pengikatan_agunan_rt" => '0',//taidak
            "Paripasu_value_agunan_rt" => '0',//taidak
            "Nilai_paripasu_agunan_bank_rt" => '0',//taidak
            "Flag_asuransi_value_agunan_rt" => '0',//taidak
            "Nama_perusahaan_asuransi_agunan_rt" =>'IJK',//taidak
            "Nilai_asuransi_agunan_rt" => '0',//taidak
            "Eligibility_value_agunan_rt" => '0',//taidak
            "Proyeksi_nilai_likuidasi_agunan_rt" => '0'//taidak
        ];
        return $request;
    }

    public function user_notifications()
    {
        return $this->hasMany('App\Models\UserNotification', 'notifiable_id');
    }

    public function related()
    {
        return $this->morphTo();
    }

    /**
     * Get Data Notification.
     *
     * @param array $data
     * @return array $request
     */


     /**
     * Remove comma and dot.
     *
     * @param string $place
     * @return string $return
     */
    public function reformatString( $place )
    {
        return $place ? str_replace(',', '', str_replace('.', '', $place)) : '';
    }

     /**
     * Reformat City.
     *
     * @param string $place
     * @return string $return
     */
    public function reformatCity( $city )
    {
        $needle = strpos(strtolower($city), 'kota') == 0 ? 'kota' : 'kab';

        return strtoupper(str_replace($needle, '', strtolower($city)) . " " . $needle);
    }

    /**
     * Get chart attribute
     */

    public function getChartAttribute()
    {
        return [
            'month'  => $this->month,
            'month2' => $this->month2,
            'value'  => $this->value,
        ];
    }


    /**
        * Get list count for chart
        *
        * @param  string $startChart
        * @param  string $endChart
        * @return array
    */
    public function getChartEForm($startChart, $endChart)
    {
        if(!empty($startChart) && !empty($endChart)){
            $startChart = date("01-m-Y",strtotime($startChart));
            $endChart   = date("t-m-Y", strtotime($endChart));

            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($startChart) && !empty($endChart)){
            $now        = new \DateTime();
            $startChart = $now->format('Y-m-d h:i:s');

            $endChart   = date("t-m-Y", strtotime($endChart));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($endChart) && !empty($startChart)){
            $now      = new \DateTime();
            $endChart = $now->format('Y-m-d h:i:s');
            
            $startList = date("01-m-Y",strtotime($startList));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $filter = true;
        }else{
            $filter = false;
        }

        $data = Eform::select(
                    DB::raw("count(eforms.id) as value"),
                    DB::raw("to_char(eforms.created_at, 'TMMonth YYYY') as month"),
                    DB::raw("to_char(eforms.created_at, 'MM YYYY') as month2"),
                    DB::raw("to_char(eforms.created_at, 'YYYY MM') as order")
                )
                ->when($filter, function ($query) use ($startChart, $endChart){
                    return $query->whereBetween('eforms.created_at', [$startChart, $endChart]);
                })
                ->groupBy('month', 'month2', 'order')
                ->orderBy("order", "asc")
                ->get()
                ->pluck("chart");
        return $data;
    }

    public function getNewestEFormAttribute()
    {
        // Set language to Bahasa
        Carbon::setLocale('id');

        // return custom collection
        return [
            'no_ref'            => $this->ref_number,
            'nasabah'           => $this->customer['personal']['name'],
            'nominal'           => $this->nominal,
            'product_type'      => $this->product_type,
            'tanggal_pengajuan' => date('d-M-Y', strtotime($this->created_at)),
            'no_telepon'        => empty($this->mobile_phone) ? null : $this->mobile_phone,
            'prescreening'      => $this->prescreening_status,
            'status'            => $this->status,
            'aging'             => Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans()
        ];
    }

    public function getNewestEForm($startList, $endList)
    {
        if(!empty($startList) && !empty($endList)){
            $startList = date("01-m-Y",strtotime($startList));
            $endList   = date("t-m-Y", strtotime($endList));
     
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
            $startList = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
            $endList = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($startList) && !empty($endList)){
            $now        = new \DateTime();
            $startList = $now->format('Y-m-d h:i:s');

            $endList   = date("t-m-Y", strtotime($endList));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
            $endList = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($endList) && !empty($startList)){
            $now      = new \DateTime();
            $endList = $now->format('Y-m-d h:i:s');

            $startList = date("01-m-Y",strtotime($startList));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
            $startList = $dateStart->format('Y-m-d h:i:s');

            $filter = true;
        }else{
            $filter = false;
        }

        $data = EForm::when($filter, function($query) use ($startList, $endList){
                    return $query->whereBetween('eforms.created_at', [$startList, $endList]);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->pluck('newestEForm');

        return $data;
    }
}
