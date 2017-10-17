<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use Illuminate\Http\Request;
use App\Models\Customer;
use Sentinel;
use Asmx;

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
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'branch', 'ao_name', 'status', 'aging', 'is_visited' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'ao_id', 'updated_at', 'branch', 'ao' ];

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
        return $this->customer->fullname;
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
    public function getBranchAttribute()
    {
        // if( $branch = $this->branch ) {
        //     return $this->branch->name;
        // }
        return 'Branch Name';
    }

    /**
     * Get AO detail information.
     *      
     * @return string
     */
    public function getAoNameAttribute()
    {
        $AO = \RestwsHc::getUser( $this->ao_id );
        if( $AO ) {
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
        if( $this->is_approved ) {
            return 'Submit';
        }
        if( $this->visit_report ) {
            return 'Initiate';
        }
        if( $this->ao_id ) {
            return 'Dispose';
        }
        return 'Rekomend';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getPrescreeningStatusAttribute()
    {
        return 'Hijau';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        $days = $this->created_at->diffInDays();
        // $weeks = (integer) ( $days / 7 );
        // $days = $days % 7;
        // $months = (integer) ( $weeks / 4 );
        // $weeks = $weeks % 4;
        // $result = '';
        // if( $months != 0 ) {
        //     $result .= $months . ' bulan ';
        // }
        // if( $weeks != 0 ) {
        //     $result .= $weeks . ' minggu ';
        // }
        // if( $days != 0 ) {
        //     $result .= $days . ' hari ';
        // } else {
        //     $result = 'Baru';
        // }
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
        $ref_number_check = static::whereRaw( 'ref_number ILIKE ?', [ $ref_number . '%' ] )->max( 'ref_number' );
        if( $ref_number_check ) {
            $ref_number .= substr( ( '00' . ( integer ) substr( $ref_number_check, -2 ) + 1 ), -2 );
        } else {
            $ref_number .= '01';
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
        $eform = static::find( $eform_id );
        for ( $i=1; $i <= 7; $i++ ) {
            $result = $eform->insertCoreBRI( $i );
            if( $result === false ) {
                \Log::info( 'Error step ' . $i );
                return $result;
                // $i--;
            }
            \Log::info( 'Step ' . $i . ' Berhasil.' );
        }
        $eform->update( [
            'pros' => $request->pros,
            'cons' => $request->cons,
            'is_approved' => true
        ] );
        return $eform;
    }

    /**
     * Function to insert data to core BRI.
     *
     * @return string
     */
    public function insertCoreBRI( $step_id )
    {
        $kpr = $this->kpr;
        $customer = $this->customer;
        $customer_detail = $customer->detail;
        $request = [
            "nik_pemohon" => empty( $this->nik ) ? '' : $this->nik,
            "nama_pemohon" => empty( $this->customer_name ) ? '' : $this->customer_name,
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
            "tempat_lahir_pemohon" => empty( $customer_detail->birth_place_id ) ? '' : $customer_detail->birth_place_id,
            "tanggal_lahir_pemohon" => empty( $customer_detail->birth_date ) ? '' : $customer_detail->birth_date,
            "alamat_pemohon" => empty( $customer_detail->address ) ? '' : $customer_detail->address,
            // "jenis_kelamin_pemohon" => "l",
            "jenis_kelamin_pemohon" => empty( $customer->gender ) ? '' : $customer->gender, // L harusnya 0 atau 1 atau 2 atau 3
            "kewarganegaraan_pemohon" => empty( $customer_detail->citizenship_id ) ? '' : $customer_detail->citizenship_id,
            "pekerjaan_pemohon_value" => empty( $customer_detail->job_id ) ? '' : $customer_detail->job_id,
            // "status_pernikahan_pemohon_value" => "2",
            "status_pernikahan_pemohon_value" => empty( $customer_detail->status ) ? '' : $customer_detail->status, // Belum sama dengan value dari BRI
            "status_pisah_harta_pemohon" => "Pisah Harta",
            "nik_pasangan" => empty( $customer_detail->couple_nik ) ? '' : $customer_detail->couple_nik,
            "nama_pasangan" => empty( $customer_detail->couple_name ) ? '' : $customer_detail->couple_name,
            "status_tempat_tinggal_value" => "0",
            "status_tempat_tinggal_value" => empty( $customer_detail->address_status ) ? '0' : $customer_detail->address_status,
            "telepon_pemohon" => empty( $customer->phone ) ? '' : $customer->phone,
            "hp_pemohon" => empty( $customer->mobile_phone ) ? '' : $customer->mobile_phone,
            "email_pemohon" => empty( $customer->email ) ? '' : $customer->email,
            "jenis_pekerjaan_value" => empty( $customer_detail->job_type_id ) ? '' : $customer_detail->job_type_id,
            "pekerjaan_value" => empty( $customer_detail->job_id ) ? '' : $customer_detail->job_id,
            "nama_perusahaan" => empty( $customer_detail->company_name ) ? '' : $customer_detail->company_name,
            "bidang_usaha_value" => empty( $customer_detail->job_field_id ) ? '' : $customer_detail->job_field_id,
            "jabatan_value" => "21",
            "jabatan_value" => empty( $customer_detail->position ) ? '' : $customer_detail->position,
            "lama_usaha" => empty( $customer_detail->work_duration ) ? '0' : $customer_detail->work_duration,
            "alamat_usaha" => empty( $customer_detail->office_address ) ? '' : $customer_detail->office_address,
            "jenis_penghasilan" => "Single Income", // Tidak ada di design dan database
            "gaji_bulanan_pemohon" => empty( $customer_detail->salary ) ? '' : $customer_detail->salary,
            "pendapatan_lain_pemohon" => empty( $customer_detail->other_salary ) ? '' : $customer_detail->other_salary,
            "gaji_bulanan_pasangan" => "2100000", // Belum ada
            "pendapatan_lain_pasangan" => "1100000", // Belum ada
            "angsuran" => empty( $customer_detail->loan_installment ) ? '' : $customer_detail->loan_installment,
            "jenis_kpp_value" => "KPR Perorangan PNS / BUMN", // Tidak ada di design dan database, ada dropdownnya GetJenisKPP
            "permohonan_pinjaman" => empty( $kpr->request_amount ) ? '' : $kpr->request_amount,
            // "uang_muka" => "51000000",
            "uang_muka" => ( ( $kpr->request_amount * $kpr->dp ) / 100 ),
            "jangka_waktu" => ( $kpr->year * 12 ),
            "jenis_dibiayai_value" => "123456789", // Tidak ada di design dan database
            "sektor_ekonomi_value" => "123456789", // Tidak ada di design dan database
            "project_value" => "1086", // Tidak ada di design dan database
            "program_value" => "27", // Tidak ada di design dan database
            "pihak_ketiga_value" => empty( $kpr->developer_id ) ? '' : $kpr->developer_id,
            "sub_pihak_ketiga_value" => "1", // Tidak ada di design dan database
            "nama_keluarga" => "siSepupu", // Tidak ada di design dan database
            "hubungan_keluarga" => empty( $customer_detail->emergency_relation ) ? '' : $customer_detail->emergency_relation,
            "telepon_keluarga" => empty( $customer_detail->emergency_contact ) ? '' : $customer_detail->emergency_contact,
            "jenis_kredit" => strtoupper( $this->product_type ),
            "tujuan_penggunaan_value" => "3", // Tidak ada di design dan database
            "tujuan_penggunaan" => "Pembelian Rumah Baru", // Tidak ada di design dan database
            "kode_cabang" => empty( $this->branch_id ) ? '' : $this->branch_id,
            "id_prescreening" => "12", // Tidak ada di design dan database dan perlu sync dengan BRI
            "nama_ibu" => empty( $customer_detail->mother_name ) ? '' : $customer_detail->mother_name,
            "npwp_pemohon" => "36.930.247.6-409.000", // Tidak ada di design dan database
            "nama_pengelola" => "Oblag", // Nama AO
            "pn_pengelola" => "00139644",
            "cif" => '' //Informasi nomor CIF
        ];
        $request += $this->additional_parameters;
        if( $step_id == 1 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataCif' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                $this->additional_parameters += $this->additional_parameters + [ 'fid_cif_las' => $post_to_bri[ 'contents' ] ];
                $this->save();
                return true;
            }
        } else if( $step_id == 2 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataCifSdn' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                return true;
            }
        } else if( $step_id == 3 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataAplikasi' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                $this->additional_parameters += $this->additional_parameters + [ 'fid_aplikasi' => $post_to_bri[ 'contents' ] ];
                $this->save();
                return true;
            }
        } else if( $step_id == 4 ) {
            // Perlu tambah data prescreening (id_prescreening)
            $post_to_bri = Asmx::setEndpoint( 'InsertDataPrescreening' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                return true;
            }
        } else if( $step_id == 5 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataScoringKpr' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                return true;
            }
        } else if( $step_id == 6 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataTujuanKredit' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                return true;
            }
        } else if( $step_id == 7 ) {
            $post_to_bri = Asmx::setEndpoint( 'InsertDataMaster' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                $this->update( [ 'is_approved' => true ] );
                return true;
            }
        }
        return $post_to_bri;
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

        // static::addGlobalScope( 'role', function( Builder $builder ) {
        //     $login_usr = Sentinel::getUser();
        //     if( $login_usr ) {
        //         $role_slug = $login_usr->roles()->first()->slug;
        //         if( $role_slug == 'ao' ) {
        //             // $builder->whereAoId( $login_usr->id )->has( 'visit_report', '<', 1 );
        //             $builder->whereAoId( $login_usr->id );
        //         } else if( $role_slug == 'mp' || $role_slug == 'pinca' ) {
        //             if( $login_usr->detail ) {
        //                 // $builder->where( [
        //                 //     'office_id' => $login_usr->detail->office_id,
        //                 //     'prescreening_status' => 0
        //                 // ] )->has( 'visit_report' );
        //                 $builder->where( [
        //                     'office_id' => $login_usr->detail->office_id
        //                 ] );
        //             }
        //         }
        //     }
        // } );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['appointment_date', 'asc'];
        return $query->leftJoin('users','eforms.user_id','=','users.id')
        ->where( function( $eform ) use( $request ) {
            if( $request->has( 'status' ) ) {
                if( $request->status == 'Submit' ) {
                    $eform->whereIsApproved( true );
                } else if( $request->status == 'Initiate' ) {
                    $eform->has( 'visit_report' )->whereIsApproved( false );
                } else if( $request->status == 'Dispose' ) {
                    $eform->whereNotNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                } else if( $request->status == 'Rekomend' ) {
                    $eform->whereNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );
                }
            }
            if ($request->has('search')) {
                 $eform->where('eforms.ref_number', '=', $request->input('search'))
                  ->orWhere('users.first_name', 'ilike', '%' . $request->input('search') . '%')
                  ->orWhere('users.last_name', 'ilike', '%' . $request->input('search') . '%');
            }
            if ($request->has('start_date') || $request->has('end_date')) {
                $start_date= $request->input('start_date');
                $end_date = $request->has('end_date') ? $request->input('end_date') : date('Y-m-d');
                $eform->whereBetween('eforms.created_at',array($start_date,$end_date));
            }
        } )->orderBy('eforms.'.$sort[0], $sort[1]);
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
}