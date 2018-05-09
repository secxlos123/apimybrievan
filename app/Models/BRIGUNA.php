<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
        'NIP','Status_Pekerjaan','Nama_atasan_Langsung','Jabatan_atasan','mitra',
        'tujuan_penggunaan','NPWP_nasabah','KK','SLIP_GAJI','SK_AWAL',
        'SK_AKHIR','REKOMENDASI','SKPG',
        'eform_id','tujuan_penggunaan_id','mitra_id','jenis_pinjaman_id','year','is_send',
		'request_amount', 'angsuran_usulan', 'maksimum_plafond',
		'uid','uid_pemrakarsa','tp_produk','id_aplikasi','cif_las',
		'Tgl_perkiraan_pensiun','Sifat_suku_bunga','Briguna_profesi',
		'Pendapatan_profesi'.'Potongan_per_bulan,Plafond_briguna_existing',
		'Angsuran_briguna_existing','Suku_bunga','Jangka_waktu','Baki_debet','Plafond_usulan',
		'Rek_simpanan_bri','Riwayat_pinjaman','Penguasaan_cashflow','Payroll',
        'Gaji_bersih_per_bulan','Maksimum_angsuran','Tujuan_membuka_rek','Briguna_smart',
        'Kode_fasilitas','Tujuan_penggunaan_kredit','Penggunaan_kredit','Provisi_kredit',
		'Biaya_administrasi','Penalty','Perusahaan_asuransi','Premi_asuransi_jiwa',
		'Premi_beban_bri','Premi_beban_debitur','Flag_promo','Fid_promo',
		'Pengadilan_terdekat','Bupln','Agribisnis','Sandi_stp',
		'Sifat_kredit','Jenis_penggunaan','Sektor_ekonomi_sid','Jenis_kredit_lbu',
		'Sifat_kredit_lbu','Kategori_kredit_lbu','Jenis_penggunaan_lbu','Sumber_aplikasi',
        'Sektor_ekonomi_lbu', 'id_Status_gelar','Status_gelar','score','grade','cutoff',
        'definisi','no_npwp','no_dan_tanggal_sk_awal','no_dan_tanggal_sk_akhir',
        'branch_name','baru_atau_perpanjang','total_exposure','program_asuransi',
        'kredit_take_over','pemrakarsa_name','agama','npl_instansi','npl_unitkerja',
        'gimmick','jumlah_pekerja','jumlah_debitur','scoring_mitra','is_verified',
        'catatan_kk','catatan_ktp','catatan_couple_ktp','catatan_npwp',
        'catatan_sk_awal','catatan_sk_akhir','catatan_skpu','catatan_rekomendasi',
        'catatan_gaji','flag_kk','flag_ktp','flag_couple_ktp','flag_npwp','flag_sk_awal',
        'flag_sk_akhir','flag_skpu','flag_rekomendasi','flag_slip_gaji','id_foto','no_rek_simpanan','gaji_pensiun'
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
    public function getIdAttribute( $value ) {
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
            $data[ 'IsFinish' ] = 'false';
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
    	    /*if(isset($data[ 'maksimum_plafond' ])){
                $data[ 'maksimum_plafond' ] =  $data[ 'maksimum_plafond' ];
    	    }else{
    	       $data[ 'maksimum_plafond' ] = "0";
    	    }
    	    if(isset($data['jenis_pinjaman_id'])){
                $data[ 'jenis_pinjaman_id' ] = $data[ 'jenis_pinjaman' ];
    	        $data[ 'jenis_pinjaman' ] = $data[ 'jenis_pinjaman_name' ];
    	    }else{
    	        $data[ 'jenis_pinjaman_id' ] = "0";
                $data[ 'jenis_pinjaman' ] = "";
    	    }*/

            $eform = EForm::create( $data );
            \Log::info($eform);
            // Start Code Insert to Dropbox
            $briguna = ( new static )->newQuery()->create( [ 'eform_id' => $eform->id ] + $data );

            $Dropbox = new Dropbox();
            $customer        = $eform->customer;
            $customer_detail = $customer->detail;
            // print_r($customer);
            // print_r($customer_detail);exit();
            $kecamatan = '';
            $kabupaten = '';
            $kodepos   = '';
            $kelurahan = '';
            $kecamatan_dom = '';
            $kabupaten_dom = '';
            $kodepos_dom   = '';
            $kelurahan_dom = '';

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

            if (!empty($customer_detail->current_address)) {
                $address = explode('=', $customer_detail->current_address);
                // print_r($address);
                if (count($address) > 1) {
                    $kel     = explode(' ', $address[1]);
                    $kec     = explode(',', $address[2]);
                    $kecamatan_dom = $kec[0];
                    $kabupaten_dom = $kec[1];
                    $kodepos_dom   = $kec[2];
                    $kelurahan_dom = $kel[0];
                }
            }
            
			$couple_gender = 'L';
			if($customer->gender=='L'){
				$couple_gender = 'P';
			}
            $content_insert_dropbox = [
                "cif"       => "",
                "nik"       => empty($eform->nik) ? "" : $eform->nik,
                "nama"      => empty($customer->first_name) ? "" : $customer->first_name.' '.$customer->last_name,
                "kelamin"   => empty($customer->gender) ? "" : $customer->gender,
                "tmp_lahir" => empty($customer_detail->birth_place) ? "" : $customer->birth_place,
                "tgl_lahir" => empty($customer_detail->birth_date) ? "" : $customer_detail->birth_date,
                "ibu"       => empty($customer_detail->mother_name) ? "" : $customer_detail->mother_name,
                "email"     => empty($customer->email) ? "" : $customer->email,
                "kontak"    => empty($customer->mobile_phone) ? "" : $customer->mobile_phone,
                "kawin"         => empty($customer_detail->status) ? "" : $customer_detail->status,
                "hist"          => "tidak",
                "nama_bank"     => "",
				"nama_pasangan" => empty($customer->couple_name) ? "" : $customer->couple_name,
				"nik_pasangan"        => empty($customer->couple_nik) ? "" : $customer->couple_nik,
				"tmp_lahir_pasangan" => empty($customer->couple_birth_place_id) ? "" : $customer->couple_birth_place_id,
				"tgl_lahir_pasangan" => empty($customer->couple_birth_date) ? "" : $customer->couple_birth_date,
				"ibu_pasangan"     => empty($customer->mother_name) ? "" : $customer->mother_name,
				"kelamin_pasangan" => empty($couple_gender) ? "" : $couple_gender,
				"alamat_pasangan"  => empty($customer_detail->address) ? "" : $customer_detail->address,
                "alamat"    => empty($customer_detail->address) ? "" : $customer_detail->address,
                "kodepos"   => empty($kodepos) ? "" : $kodepos,
                "provinsi"  => empty($kabupaten) ? "" : $kabupaten,
                "kabupaten" => empty($kabupaten) ? "" : $kabupaten,
                "kecamatan" => empty($kecamatan) ? "" : $kecamatan,
                "kelurahan" => empty($kelurahan) ? "" : $kelurahan,
                "alamat_dom"    => empty($customer_detail->current_address) ? "" : $customer_detail->current_address,
                // "kodepos_dom"   => empty($kodepos) ? "" : $kodepos,
                "provinsi_dom"  => empty($kabupaten_dom) ? "" : $kabupaten_dom,
                "kabupaten_dom" => empty($kabupaten_dom) ? "" : $kabupaten_dom,
                "kecamatan_dom" => empty($kecamatan_dom) ? "" : $kecamatan_dom,
                "kelurahan_dom" => empty($kelurahan_dom) ? "" : $kelurahan_dom,
                "jenis"     => 'Karya',
                "amount"    => empty($customer_detail->loan_installment) ? "" : $customer_detail->loan_installment,
                "tujuan"    => empty($eform->tujuan_penggunaan) ? "" : $eform->tujuan_penggunaan,
                "agunan"    => empty($eform->mitra) ? "" : $eform->mitra,
                "jangka"    => empty($briguna->year) ? "" : $briguna->year,
                "npwp"      => "",
                "mitra"     => empty($data['mitra_name']) ? "" : $data['mitra_name'],
                "nip"       => empty($data['nip']) ? "" : $data['nip'],
                "email_atasan"     => "",
                "status_pekerjaan" => empty($data['job_type']) ? "" : $data['job_type'],
                "uker"      => empty($eform->branch) ? "" : $eform->branch_id.';'.$eform->branch
            ];

            $postData = [
                'requestMethod' => 'insertSkpp',
                'requestData'   => json_encode([
                    'branch'    => $eform->branch_id,
                    'appname'   => 'MyBRI',
                    'jenis'     => 'BG',
                    'expdate'   => '2099-12-31 00:00:00',
                    'expdate_pimpinan' => '2099-12-31 00:00:00',
                    'content'   => $content_insert_dropbox,
                    'status'    => '1',
                ])
            ];
            $data_dropbox = $Dropbox->insertDropbox($postData);
            \Log::info($data_dropbox);
            // dd($data_dropbox);
            $data_dropbox['eform_id'] = $eform->id;
            if (!empty($data_dropbox)) {
                if( $data_dropbox['responseCode'] == "01" ) {
                    $briguna['ref_number_new'] = $data_dropbox['refno'];
                    $eforms = EForm::findOrFail($data_dropbox['eform_id']);
                    $base_request["ref_number"] = $data_dropbox['refno'];
					$eform['ref_number'] = $data_dropbox['refno'];
                    $eforms->update($base_request);
                    return $eform;
                }
            }
            throw new \Exception( "Error Processing Request", 1 );
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
}