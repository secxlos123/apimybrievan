<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\EForm;
use App\Models\ApiLas;
use App\Models\Dropbox;
use Asmx;

class BRIGUNA extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'briguna';

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
        'NIP','Status_Pekerjaan','Nama_atasan_Langsung','Jabatan_atasan',
		'NPWP_nasabah','KK','SLIP_GAJI','SK_AWAL',
        'SK_AKHIR','REKOMENDASI','SKPG',
		//'SK_PERTAMA', 'SK_TERAKHIR','NPWP','REKOMENDASI_ATASAN',
        'eform_id', 'tujuan_penggunaan_id', 
        'mitra_id', 
        'jenis_pinjaman_id',  'year',
		'request_amount', 'angsuran_usulan', 'maksimum_plafond',
		'uid','uid_pemrakarsa','tp_produk','id_aplikasi','cif_las',
		'Tgl_perkiraan_pensiun','Sifat_suku_bunga','Briguna_profesi',
		'Pendapatan_profesi'.'Potongan_per_bulan,Plafond_briguna_existing',
		'Angsuran_briguna_existing','Suku_bunga','Jangka_waktu','Baki_debet','Plafond_usulan',
		'Rek_simpanan_bri','Riwayat_pinjaman','Penguasaan_cashflow','Payroll','Gaji_bersih_per_bulan',
		'Maksimum_angsuran','Tujuan_membuka_rek','Briguna_smart','Kode_fasilitas',
		'Tujuan_penggunaan_kredit','Penggunaan_kredit','Provisi_kredit',
		'Biaya_administrasi','Penalty','Perusahaan_asuransi','Premi_asuransi_jiwa',
		'Premi_beban_bri','Premi_beban_debitur','Flag_promo','Fid_promo',
		'Pengadilan_terdekat','Bupln','Agribisnis','Sandi_stp',
		'Sifat_kredit','Jenis_penggunaan','Sektor_ekonomi_sid','Jenis_kredit_lbu',
		'Sifat_kredit_lbu','Kategori_kredit_lbu','Jenis_penggunaan_lbu','Sumber_aplikasi','Sektor_ekonomi_lbu',
		'id_Status_gelar','Status_gelar','score','grade','cutoff','definisi'
	];
	
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
    try {        
        \Log::info($data);
        $data[ 'mitra_id' ] = $data[ 'idMitrakerja' ];
		$data[ 'tujuan_penggunaan_id' ] = $data[ 'tujuan_penggunaan' ];
        $data[ 'mitra' ] = $data[ 'mitra_name' ];
        $data[ 'tujuan_penggunaan' ] = $data[ 'tujuan_penggunaan_name' ];
        if(isset($data[ 'angsuran_usulan' ])){
            $data[ 'angsuran_usulan' ] =  $data[ 'request_amount' ];
        }else{
            $data[ 'angsuran_usulan' ] = "0";
        }
        $data[ 'Status_Pekerjaan' ] = $data[ 'job_type' ];

        $data[ 'maksimum_plafond' ] = '0';
        $data[ 'NIP' ] = $data[ 'nip' ];
        $data[ 'Nama_atasan_Langsung' ] = '';
        $data[ 'Jabatan_atasan' ] = '';
        $data[ 'SK_AKHIR' ] = '';
        $data[ 'REKOMENDASI' ] = '';
	/*    if(isset($data[ 'maksimum_plafond' ])){
            $data[ 'maksimum_plafond' ] =  $data[ 'maksimum_plafond' ];
	    }else{
	       $data[ 'maksimum_plafond' ] = "0";
	    }*/
	    /*if(isset($data['jenis_pinjaman_id'])){
            $data[ 'jenis_pinjaman_id' ] = $data[ 'jenis_pinjaman' ];
	        $data[ 'jenis_pinjaman' ] = $data[ 'jenis_pinjaman_name' ];
	    }else{
	        $data[ 'jenis_pinjaman_id' ] = "0";
            $data[ 'jenis_pinjaman' ] = "";
	    }*/

        $eform = EForm::create( $data );
        \Log::info($eform);
        // Start Code Insert to API LAS and Dropbox
        $briguna = ( new static )->newQuery()->create( [ 'eform_id' => $eform->id ] + $data );
        // $ApiLas  = new ApiLas();
        $Dropbox = new Dropbox();
        $customer        = $eform->customer;
        $customer_detail = $customer->detail;
        // print_r($customer);
        // print_r($customer_detail);exit();
        $kecamatan = '';
        $kabupaten = '';
        $kodepos   = '';
        $kelurahan = '';

        \Log::info($briguna);

        if (!empty($customer_detail->address)) {
            $address = explode('=', $customer_detail->address);
            // print_r($address);
                if (count($address) > 1) {
                    $kel     = explode(' ', $address[1]);
                    $kec     = explode(',', $address[2]);
                    $kecamatan = $kec[0];
                    $kabupaten = $kec[1];
                    $kodepos   = $kec[2];
                    $kelurahan = $kel[0];
                }
            }

        $content_insert_dropbox = [
            "cif"       => "",
            "nik"       => $eform->nik,
            "nama"      => $customer->first_name.' '.$customer->last_name,
            "kelamin"   => $customer->gender,
            "tmp_lahir" => $customer_detail->birth_place,
            "tgl_lahir" => $customer_detail->birth_date,
            "ibu"       => $customer_detail->mother_name,
            "email"     => $customer->email,
            "kontak"    => $customer->mobile_phone,
            "kawin"     => $customer_detail->status,
            "hist"      => "tidak",
            "nama_bank" => "",
            "alamat"    => $customer_detail->address,
            "kodepos"   => $kodepos,
            "provinsi"  => $kabupaten,
            "kabupaten" => $kabupaten,
            "kecamatan" => $kecamatan,
            "kelurahan" => $kelurahan,
            "jenis"     => $eform->jenis_pinjaman,
            "amount"    => $customer_detail->loan_installment,
            "tujuan"    => $eform->tujuan_penggunaan,
            "agunan"    => $eform->mitra,
            "jangka"    => ($briguna->year * 12),
            "email_atasan" => "aswin.taopik@gmail.com",
            "npwp"      => $customer_detail->npwp,
            "mitra"     => $data['mitra_name'],
            "nip"       => $data['nip'],
           "status_pekerjaan" => $data['job_type']
        ];

        $postData = [
            'requestMethod' => 'insertSkpp',
            'requestData'   => json_encode([
                'branch'  => $eform->branch_id,
                'appname' => 'MBR',
                'jenis'   => 'BG',
                'expdate' => date('Y-m-d'),
                'content' => $content_insert_dropbox,
                'status'  => '1',
            ])
        ];
        $data_dropbox = $Dropbox->insertDropbox($postData);
        \Log::info($data_dropbox);
        // dd($data_dropbox);
        if( $data_dropbox['responseCode'] == "01" ) {
            $briguna['ref_number_new'] = $data_dropbox['refno'];
            $update_data  = [
                'ref_number' => $data_dropbox['refno']
            ];
            $eforms = EForm::findOrFail($eform->id);
            $eforms->update($update_data);
            /*$kecamatan_domisili = '';
            $kabupaten_domisili = '';
            $kodepos_domisili   = '';
            $kelurahan_domisili = '';
            $kecamatan_usaha    = '';
            $kabupaten_usaha    = '';
            $kodepos_usaha      = '';
            $kelurahan_usaha    = '';
            if (!empty($customer_detail->address_domisili)) {
                $address_domisili   = explode('=', $customer_detail->address_domisili);
                if (count($address_domisili) > 1) {
                    $kel                = explode(' ', $address_domisili[1]);
                    $kec                = explode(',', $address_domisili[2]);
                    $kecamatan_domisili = $kec[0];
                    $kabupaten_domisili = $kec[1];
                    $kodepos_domisili   = $kec[2];
                    $kelurahan_domisili = $kel[0];
                }
            } 

            if (!empty($customer_detail->office_address)) {
                $address_usaha   = explode('=', $customer_detail->office_address);
                // print_r($address_usaha);exit();
                if (count($address_usaha) > 1) {
                    $kel             = explode(' ', $address_usaha[1]);
                    $kec             = explode(',', $address_usaha[2]);
                    $kecamatan_usaha = $kec[0];
                    $kabupaten_usaha = $kec[1];
                    $kodepos_usaha   = $kec[2];
                    $kelurahan_usaha = $kel[0];
                }
            }

            $portofolio = 175;
            if ($data['product_type'] == 'kpr') {
                $portofolio = 172;
            }
            $content_las_debt = [
                "tp_produk"              => "10",
                "uid"                    => "10740",
                "kode_cabang"            => $eform->branch_id,
                "cif_las"                => "11039307",
                "no_ktp"                 => $eform->nik,
                "expired_ktp"            => "31122899",
                "nama_debitur_1"   => $customer->first_name.' '. $customer->last_name,
                "nama_tanpa_gelar" => $customer->first_name.' '. $customer->last_name,
                "nama_debitur_2"         => "",
                "nama_debitur_3"         => "",
                "nama_debitur_4"         => "",
                "tgl_lahir"              => $customer_detail->birth_date,
                "tempat_lahir"           => $customer_detail->birth_place_id,
                "status_perkawinan"      => $customer_detail->status,
                "nama_pasangan"          => $customer_detail->couple_name,
                "tgl_lahir_pasangan"     => $customer_detail->couple_birth_date,
                "no_ktp_pasangan"        => $customer_detail->couple_nik,
                "perjanjian_pisah_harta" => "0",
                "jumlah_tanggungan"      => $customer_detail->dependent_amount,
                "bidang_usaha"           => $customer_detail->job_field_id,
                "status_gelar"           => "0100",
                "keterangan_status_gelar"=> "ST",
                "jenis_kelamin"          => $customer->gender,
                "nama_ibu"               => $customer_detail->mother_name,
                "alamat"                 => $customer_detail->address,
                "kelurahan"              => $kelurahan,
                "kecamatan"              => $kecamatan,
                "kabupaten"              => $kabupaten,
                "kode_pos"               => $kodepos,
                "fixed_line"             => $customer->phone,
                "no_hp"                  => $customer->mobile_phone,
                "lama_menetap"           => "2",
                "email"                  => $customer->email,
                "tgl_mulai_usaha"        => date('d-m-Y'),
                "kewarganegaraan"        => $customer_detail->citizenship_id,
                "negara_domisili"        => $customer_detail->citizenship_id,
                "kepemilikan_tempat_tinggal" => $customer_detail->address_status,
                "kategori_portofolio"    => $portofolio,
                "golongan_debitur_sid"   => "907", //hardcode dari las
                "golongan_debitur_lbu"   => "886", //hardcode dari las
                "nama_kelg"              => "squad consumer",
                "telp_kelg"              => "02198349480",
                "tgl_mulai_debitur"      => date('d-m-Y'),
                "jenis_rekening"         => "3",
                "nama_bank_lain"         => "",
                "pekerjaan_debitur"      => $customer_detail->job_id,
                "alamat_usaha"           => $customer_detail->office_address,
                "nama_perusahaan"        => $customer_detail->company_name,
                "resident_flag"          => "Y",
                "customer_type"          => "I", //hardcode dari las
                "hub_bank"               => "9900",
                "pernah_pinjam"          => "Ya",
                "sumber_utama"           => "2",
                "federal_wh_code"        => "1",
                "sub_customer_type"      => "I", //hardcode dari las
                "segmen_bisnis_bri"      => "RITEL", //hardcode dari las
                "transaksi_normal_harian" => "2",
                "alias"                  => "Squad enam",
                "agama"                  => "ISL",
                "ket_agama"              => "",
                "alamat_domisili"        => $customer_detail->address_domisili,
                "kodepos_domisili"       => $kodepos_domisili,
                "kelurahan_domisili"     => $kelurahan_domisili,
                "kecamatan_domisili"     => $kecamatan_domisili,
                "kota_domisili"          => $kabupaten_domisili,
                "propinsi_domisili"      => $kabupaten_domisili,
                "jenis_pekerjaan"        => $customer_detail->job_type_id,
                "ket_pekerjaan"          => $customer_detail->job_field_id,
                "jabatan"                => $customer_detail->position,
                "kelurahan_usaha"        => $kelurahan_usaha,
                "kecamatan_usaha"        => $kecamatan_usaha,
                "kota_usaha"             => $kabupaten_usaha,
                "propinsi_usaha"         => $kabupaten_usaha,
                "kodepos_usaha"          => $kodepos_usaha,
                "tujuan_membuka_rekening"=> "ZZ",
                "ket_buka_rekening"      => "Pinjaman",
                "penghasilan_per_bulan"  => "G1",
                "usia_mpp"               => "58",
                "id_instansi"            => "1"
            ];

            $insertDebitur = $ApiLas->insertDataDebtPerorangan($content_las_debt);
            \Log::info($eforms);
            \Log::info($insertDebitur);*/
            return $briguna;
        } else {
            throw new \Exception( "Error Processing Request", 1 );
        }
     } catch (Exception $e) {
            return $e;    
    }

        // End insert
        
        // $customer = $eform->customer;
        // $customer_detail = $customer->detail;
        // // Contoh
        // // {"nik_pemohon":"3174062507890007", "nama_pemohon":"Gilang Bikin WS", "tempat_lahir_pemohon":"Jambi", "tanggal_lahir_pemohon":"1989-07-25", "alamat_pemohon":"ini alamat pemohon", "jenis_kelamin_pemohon":"l", "kewarganegaraan_pemohon":"ID", "pekerjaan_pemohon_value":"001", "status_pernikahan_pemohon_value":"2", "status_pisah_harta_pemohon":"Pisah Harta", "nik_pasangan":"3174062507891237", "nama_pasangan":"Nama Bojo", "status_tempat_tinggal_value":"0", "telepon_pemohon":"123456789", "hp_pemohon":"082177777669", "email_pemohon":"prayantaalfian@gmail.com", "jenis_pekerjaan_value":"17", "pekerjaan_value":"18", "nama_perusahaan":"Nama Perusahaan 19", "bidang_usaha_value":"20", "jabatan_value":"21", "lama_usaha":"12", "alamat_usaha":"ini alamat usaha", "jenis_penghasilan":"Singe Income", "gaji_bulanan_pemohon":"8100000", "pendapatan_lain_pemohon":"7100000", "gaji_bulanan_pasangan":"2100000", "pendapatan_lain_pasangan":"1100000", "angsuran":"500000", "jenis_kpp_value":"KPR Perorangan PNS / BUMN", "permohonan_pinjaman":"151000000", "uang_muka":"51000000", "jangka_waktu":"240", "jenis_dibiayai_value":"123456789", "sektor_ekonomi_value":"123456789", "project_value":"1086", "program_value":"27", "pihak_ketiga_value":"1016", "sub_pihak_ketiga_value":"1", "nama_keluarga":"siSepupu", "hubungan_keluarga":"Sepupu", "telepon_keluarga":"123456789", "jenis_kredit":"KPR", "tujuan_penggunaan_value":"3", "tujuan_penggunaan":"Pembelian Rumah Baru", "kode_cabang":"0206", "id_prescreening":"12", "nama_ibu":"Ibu Terbaik", "npwp_pemohon":"36.930.247.6-409.000","nama_pengelola":"Oblag","pn_pengelola":"00139644"}
        // $request = [
        //     "nik_pemohon" => $eform->nik,
        //     "nama_pemohon" => $eform->customer_name,
        //     "tempat_lahir_pemohon" => $customer_detail->birth_place,
        //     "tanggal_lahir_pemohon" => $customer_detail->birth_date,
        //     "alamat_pemohon" => $customer_detail->address,
        //     "jenis_kelamin_pemohon" => $customer->gender, // L harusnya 0 atau 1 atau 2 atau 3
        //     "kewarganegaraan_pemohon" => $customer_detail->citizenship, // Value belum sama dengan pihak BRI
        //     "pekerjaan_pemohon_value" => $customer_detail->work,
        //     "status_pernikahan_pemohon_value" => $customer_detail->status, // Belum sama dengan value dari BRI
        //     "status_pisah_harta_pemohon" => "Pisah Harta", // Tidak ada di design dan database
        //     "nik_pasangan" => "3174062507891237", // Tidak ada di design dan database
        //     "nama_pasangan" => "Nama Bojo", // Tidak ada di design dan database
        //     "status_tempat_tinggal_value" => $customer_detail->address_status,
        //     "telepon_pemohon" => $customer->phone,
        //     "hp_pemohon" => $customer->mobile_phone,
        //     "email_pemohon" => $customer->email,
        //     "jenis_pekerjaan_value" => $customer_detail->work_type,
        //     "pekerjaan_value" => $customer_detail->work,
        //     "nama_perusahaan" => $customer_detail->company_name,
        //     "bidang_usaha_value" => $customer_detail->work_field,
        //     "jabatan_value" => $customer_detail->position,
        //     "lama_usaha" => $customer_detail->work_duration,
        //     "alamat_usaha" => $customer_detail->office_address,
        //     "jenis_penghasilan" => "Single Income", // Tidak ada di design dan database
        //     "gaji_bulanan_pemohon" => $customer_detail->salary,
        //     "pendapatan_lain_pemohon" => $customer_detail->other_salary,
        //     "gaji_bulanan_pasangan" => "2100000", // Belum ada
        //     "pendapatan_lain_pasangan" => "1100000", // Belum ada
        //     "angsuran" => $customer_detail->loan_installment,
        //     "jenis_kpp_value" => "KPR Perorangan PNS / BUMN", // Tidak ada di design dan database, ada dropdownnya GetJenisKPP
        //     "permohonan_pinjaman" => $kpr->request_amount,
        //     "uang_muka" => ( ( $kpr->request_amount * $kpr->dp ) / 100 ),
        //     "jangka_waktu" => ( $kpr->year * 12 ),
        //     "jenis_dibiayai_value" => "123456789", // Tidak ada di design dan database
        //     "sektor_ekonomi_value" => "123456789", // Tidak ada di design dan database
        //     "project_value" => "1086", // Tidak ada di design dan database
        //     "program_value" => "27", // Tidak ada di design dan database
        //     "pihak_ketiga_value" => "1016", // Tidak ada di design dan database
        //     "sub_pihak_ketiga_value" => "1", // Tidak ada di design dan database
        //     "nama_keluarga" => "siSepupu", // Tidak ada di design dan database
        //     "hubungan_keluarga" => $customer_detail->emergency_relation,
        //     "telepon_keluarga" => $customer_detail->emergency_contact,
        //     "jenis_kredit" => $eform->product_type,
        //     "tujuan_penggunaan_value" => "3", // Tidak ada di design dan database
        //     "tujuan_penggunaan" => "Pembelian Rumah Baru", // Tidak ada di design dan database
        //     "kode_cabang" => $eform->office_id, // Value belum sama dengan pihak BRI
        //     "id_prescreening" => "12", // Tidak ada di design dan database dan perlu sync dengan BRI
        //     "nama_ibu" => $customer_detail->mother_name,
        //     "npwp_pemohon" => "36.930.247.6-409.000", // Tidak ada di design dan database
        //     "nama_pengelola" => "Oblag", // Nama AO
        //     "pn_pengelola" => request()->header( 'pn' ),
        //     "cif" => '' //Informasi nomor CIF
        // ];

        // $post_cif_bri = Asmx::setEndpoint( 'InsertDataCif' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $request + [ 'fid_cif_las' => $post_cif_bri[ 'contents' ] ];
        // $post_cifsdn_bri = Asmx::setEndpoint( 'InsertDataCifSdn' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $post_application_bri = Asmx::setEndpoint( 'InsertDataAplikasi' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $request + [ 'fid_aplikasi' => $post_application_bri[ 'contents' ] ];
        // // Perlu tambah data prescreening (id_prescreening)
        // $post_prescreening_bri = Asmx::setEndpoint( 'InsertDataPrescreening' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $post_scoring_kpr_bri = Asmx::setEndpoint( 'InsertDataScoringKpr' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $post_credit_bri = Asmx::setEndpoint( 'InsertDataTujuanKredit' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // $post_data_master_bri = Asmx::setEndpoint( 'InsertDataMaster' )->setBody( [
        //     'request' => json_encode( $request )
        // ] )->post( 'form_params' );
        // // kondisi OK baru bisa lanjut

        // if( $post_data_master_bri[ 'code' ] == 200 ) {
            // return $kpr;
        // } else {
            // throw new \Exception( "Error Processing Request", 1 );
        // }
    }

	  public static function update( $eform_id, $request )
    {
        $eform = static::findOrFail( $eform_id );
        if ( !empty($eform) ) {
                $eform->update( $request );
            }
    }

}
