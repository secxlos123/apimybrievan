<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use App\Models\Customer;
use Sentinel;
use Asmx;

class EForm extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'eforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'customer_name', 'mobile_phone', 'nominal', 'office', 'ao_name', 'status', 'aging', 'is_visited' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'ao_id', 'created_at', 'updated_at', 'branch', 'ao', 'kpr', 'visit_report' ];

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
    public function getOfficeAttribute()
    {
        if( $office = $this->branch ) {
            return $this->branch->name;
        }
        return '-';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getAoNameAttribute()
    {
        if( $ao = $this->ao ) {
            return $ao->fullname;
        }

        return '-';
    }

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if( $this->is_approved ) {
            return 'Diterima';
        }
        return 'Pengajuan Baru';
    }

    /**
     * Get eform aging detail information.
     *
     * @return string
     */
    public function getAgingAttribute()
    {
        $days = $this->created_at->diffInDays();
        $weeks = (integer) ( $days / 7 );
        $days = $days % 7;
        $months = (integer) ( $weeks / 4 );
        $weeks = $weeks % 4;
        $result = '';
        if( $months != 0 ) {
            $result .= $months . ' bulan ';
        }
        if( $weeks != 0 ) {
            $result .= $weeks . ' minggu ';
        }
        if( $days != 0 ) {
            $result .= $days . ' hari ';
        } else {
            $result = 'Baru';
        }
        return $result;
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
    public static function approve( $eform_id )
    {
        $eform = static::find( $eform_id );
        $eform->update( [ 'is_approved' => true ] );
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
            // "nik_pemohon":"3174062507890007",
            "nik_pemohon" => $this->nik,
            // "nama_pemohon":"Gilang Bikin WS",
            "nama_pemohon" => $this->customer_name,
            // "tempat_lahir_pemohon":"Jambi",
            "tempat_lahir_pemohon" => $customer_detail->birth_place,
            // "tanggal_lahir_pemohon":"1989-07-25",
            "tanggal_lahir_pemohon" => $customer_detail->birth_date,
            // "alamat_pemohon":"ini alamat pemohon",
            "alamat_pemohon" => $customer_detail->address,
            // "jenis_kelamin_pemohon":"l",
            "jenis_kelamin_pemohon" => $customer->gender, // L harusnya 0 atau 1 atau 2 atau 3
            // "kewarganegaraan_pemohon":"ID",
            "kewarganegaraan_pemohon" => $customer_detail->citizenship, // Value belum sama dengan pihak BRI
            // "pekerjaan_pemohon_value":"001",
            "pekerjaan_pemohon_value" => $customer_detail->work,
            // "status_pernikahan_pemohon_value":"2",
            "status_pernikahan_pemohon_value" => $customer_detail->status, // Belum sama dengan value dari BRI
            // "status_pisah_harta_pemohon":"Pisah Harta",
            "status_pisah_harta_pemohon" => "Pisah Harta", // Tidak ada di design dan database
            // "nik_pasangan":"3174062507891237",
            "nik_pasangan" => "3174062507891237", // Tidak ada di design dan database
            // "nama_pasangan":"Nama Bojo",
            "nama_pasangan" => "Nama Bojo", // Tidak ada di design dan database
            // "status_tempat_tinggal_value":"0",
            "status_tempat_tinggal_value" => $customer_detail->address_status,
            // "telepon_pemohon":"123456789",
            "telepon_pemohon" => $customer->phone,
            // "hp_pemohon":"082177777669",
            "hp_pemohon" => $customer->mobile_phone,
            // "email_pemohon":"prayantaalfian@gmail.com",
            "email_pemohon" => $customer->email,
            // "jenis_pekerjaan_value":"17",
            "jenis_pekerjaan_value" => $customer_detail->work_type,
            // "pekerjaan_value":"18",
            "pekerjaan_value" => $customer_detail->work,
            // "nama_perusahaan":"Nama Perusahaan 19",
            "nama_perusahaan" => $customer_detail->company_name,
            // "bidang_usaha_value":"20",
            "bidang_usaha_value" => $customer_detail->work_field,
            // "jabatan_value":"21",
            "jabatan_value" => $customer_detail->position,
            // "lama_usaha":"12",
            "lama_usaha" => $customer_detail->work_duration,
            // "alamat_usaha":"ini alamat usaha",
            "alamat_usaha" => $customer_detail->office_address,
            // "jenis_penghasilan":"Singe Income",
            "jenis_penghasilan" => "Single Income", // Tidak ada di design dan database
            // "gaji_bulanan_pemohon":"8100000",
            "gaji_bulanan_pemohon" => $customer_detail->salary,
            // "pendapatan_lain_pemohon":"7100000",
            "pendapatan_lain_pemohon" => $customer_detail->other_salary,
            // "gaji_bulanan_pasangan":"2100000",
            "gaji_bulanan_pasangan" => "2100000", // Belum ada
            // "pendapatan_lain_pasangan":"1100000",
            "pendapatan_lain_pasangan" => "1100000", // Belum ada
            // "angsuran":"500000",
            "angsuran" => $customer_detail->loan_installment,
            // "jenis_kpp_value":"KPR Perorangan PNS / BUMN",
            "jenis_kpp_value" => "KPR Perorangan PNS / BUMN", // Tidak ada di design dan database, ada dropdownnya GetJenisKPP
            // "permohonan_pinjaman":"151000000",
            "permohonan_pinjaman" => $kpr->request_amount,
            // "uang_muka":"51000000",
            "uang_muka" => ( ( $kpr->request_amount * $kpr->dp ) / 100 ),
            // "jangka_waktu":"240",
            "jangka_waktu" => ( $kpr->year * 12 ),
            // "jenis_dibiayai_value":"123456789",
            "jenis_dibiayai_value" => "123456789", // Tidak ada di design dan database
            // "sektor_ekonomi_value":"123456789",
            "sektor_ekonomi_value" => "123456789", // Tidak ada di design dan database
            // "project_value":"1086",
            "project_value" => "1086", // Tidak ada di design dan database
            // "program_value":"27",
            "program_value" => "27", // Tidak ada di design dan database
            // "pihak_ketiga_value":"1016",
            "pihak_ketiga_value" => "1016", // Tidak ada di design dan database
            // "sub_pihak_ketiga_value":"1",
            "sub_pihak_ketiga_value" => "1", // Tidak ada di design dan database
            // "nama_keluarga":"siSepupu",
            "nama_keluarga" => "siSepupu", // Tidak ada di design dan database
            // "hubungan_keluarga":"Sepupu",
            "hubungan_keluarga" => $customer_detail->emergency_relation,
            // "telepon_keluarga":"123456789",
            "telepon_keluarga" => $customer_detail->emergency_contact,
            // "jenis_kredit":"KPR",
            "jenis_kredit" => $this->product_type,
            // "tujuan_penggunaan_value":"3",
            "tujuan_penggunaan_value" => "3", // Tidak ada di design dan database
            // "tujuan_penggunaan":"Pembelian Rumah Baru",
            "tujuan_penggunaan" => "Pembelian Rumah Baru", // Tidak ada di design dan database
            // "kode_cabang":"0206",
            "kode_cabang" => $this->office_id, // Value belum sama dengan pihak BRI
            // "id_prescreening":"12",
            "id_prescreening" => "12", // Tidak ada di design dan database dan perlu sync dengan BRI
            // "nama_ibu":"Ibu Terbaik",
            "nama_ibu" => $customer_detail->mother_name,
            // "npwp_pemohon":"36.930.247.6-409.000",
            "npwp_pemohon" => "36.930.247.6-409.000", // Tidak ada di design dan database
            // "nama_pengelola":"Oblag",
            "nama_pengelola" => "Oblag", // Nama AO
            // "pn_pengelola":"00139644"
            "pn_pengelola" => request()->header( 'pn' ),
            "cif" => '' //Informasi nomor CIF
        ];
        $request += $this->additional_parameters;

        if( $step_id == 1 ) {
            $post_cif_bri = Asmx::setEndpoint( 'InsertDataCif' )->setBody( [
                'request' => json_encode( $request )
            ] )->post( 'form_params' );
            if( $post_to_bri[ 'code' ] == 200 ) {
                $this->additional_parameters += $this->additional_parameters + [ 'fid_cif_las' => $post_to_bri[ 'contents' ] ];
                $this->save();
                return true;
            }
        } else if( $step_id == 2 ) {
            $post_cifsdn_bri = Asmx::setEndpoint( 'InsertDataCifSdn' )->setBody( [
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
        return false;
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