<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\EForm;
use Asmx;

class KPR extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'kpr';

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
    protected $fillable = [ 'status_property', 'eform_id', 'developer_id', 'property_id', 'price', 'building_area', 'home_location', 'year', 'active_kpr', 'dp', 'request_amount' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'eform_id' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->eform_id;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        \Log::info($data);
        $eform = EForm::create( $data );
        $data[ 'developer_id' ] = $data[ 'developer' ];
        $data[ 'property_id' ] = $data[ 'property' ];
        $kpr = ( new static )->newQuery()->create( [ 'eform_id' => $eform->id ] + $data );
        $customer = $eform->customer;
        $customer_detail = $customer->detail;
        // $post_to_bri = Asmx::setEndpoint( 'InsertDataMaster' )->setBody( [
        //     'request' => json_encode( [
        //         "nik_pemohon" => $eform->nik,
        //         "nama_pemohon" => $eform->customer_name,
        //         "tempat_lahir_pemohon" => $customer_detail->birth_place,
        //         "tanggal_lahir_pemohon" => $customer_detail->birth_date,
        //         "alamat_pemohon" => $customer_detail->address,
        //         "jenis_kelamin_pemohon" => $customer->gender, // L harusnya 0 atau 1 atau 2 atau 3
        //         "kewarganegaraan_pemohon" => $customer_detail->citizenship, // Value belum sama dengan pihak BRI
        //         "pekerjaan_pemohon_value" => $customer_detail->work,
        //         "status_pernikahan_pemohon_value" => $customer_detail->status, // Belum sama dengan value dari BRI
        //         "status_pisah_harta_pemohon" => "Pisah Harta", // Tidak ada di design dan database
        //         "nik_pasangan" => "3174062507891237", // Tidak ada di design dan database
        //         "nama_pasangan" => "Nama Bojo", // Tidak ada di design dan database
        //         "status_tempat_tinggal_value" => $customer_detail->address_status,
        //         "telepon_pemohon" => $customer->phone,
        //         "hp_pemohon" => $customer->mobile_phone,
        //         "email_pemohon" => $customer->email,
        //         "jenis_pekerjaan_value" => $customer_detail->work_type,
        //         "pekerjaan_value" => $customer_detail->work,
        //         "nama_perusahaan" => $customer_detail->company_name,
        //         "bidang_usaha_value" => $customer_detail->work_field,
        //         "jabatan_value" => $customer_detail->position,
        //         "lama_usaha" => $customer_detail->work_duration,
        //         "alamat_usaha" => $customer_detail->office_address,
        //         "jenis_penghasilan" => "Single Income", // Tidak ada di design dan database
        //         "gaji_bulanan_pemohon" => $customer_detail->salary,
        //         "pendapatan_lain_pemohon" => $customer_detail->other_salary,
        //         "gaji_bulanan_pasangan" => "2100000", // Belum ada
        //         "pendapatan_lain_pasangan" => "1100000", // Belum ada
        //         "angsuran" => $customer_detail->loan_installment,
        //         "jenis_kpp_value" => "KPR Perorangan PNS / BUMN", // Tidak ada di design dan database, ada dropdownnya GetJenisKPP
        //         "permohonan_pinjaman" => $kpr->request_amount,
        //         "uang_muka" => ( ( $kpr->request_amount * $kpr->dp ) / 100 ),
        //         "jangka_waktu" => ( $kpr->year * 12 ),
        //         "jenis_dibiayai_value" => "123456789", // Tidak ada di design dan database
        //         "sektor_ekonomi_value" => "123456789", // Tidak ada di design dan database
        //         "project_value" => "1086", // Tidak ada di design dan database
        //         "program_value" => "27", // Tidak ada di design dan database
        //         "pihak_ketiga_value" => "1016", // Tidak ada di design dan database
        //         "sub_pihak_ketiga_value" => "1", // Tidak ada di design dan database
        //         "nama_keluarga" => "siSepupu", // Tidak ada di design dan database
        //         "hubungan_keluarga" => $customer_detail->emergency_relation,
        //         "telepon_keluarga" => $customer_detail->emergency_contact,
        //         "jenis_kredit" => $eform->product_type,
        //         "tujuan_penggunaan_value" => "3", // Tidak ada di design dan database
        //         "tujuan_penggunaan" => "Pembelian Rumah Baru", // Tidak ada di design dan database
        //         "kode_cabang" => $eform->office_id, // Value belum sama dengan pihak BRI
        //         "id_prescreening" => "12", // Tidak ada di design dan database dan perlu sync dengan BRI
        //         "nama_ibu" => $customer_detail->mother_name,
        //         "npwp_pemohon" => "36.930.247.6-409.000", // Tidak ada di design dan database
        //         "nama_pengelola" => "Oblag", // Nama AO
        //         "pn_pengelola" => request()->header( 'pn' )
        //     ] )
        // ] )->post( 'form_params' );
        return $kpr;
        if( $post_to_bri[ 'code' ] == 200 ) {
            return $kpr;
        } else {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }
}
