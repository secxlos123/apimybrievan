<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class DIRRPC extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'dirrpc';

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
	 'debt_name','maintance','pemutus_name','jabatan','pemeriksa','jabatan_pemeriksa','action','maintance','id','no'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'id' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->id;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
	  try {        

        $gimmick = ( new static )->newQuery()->create($data);
            return $gimmick;
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

    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "id" ) {
            $sort = ['id', 'asc'];
        }

		 $dir = $query->where( function( $dir ) use( $request ) {
            if ( $request->has('gimmick_name') ) {
                $dir = $dir->where('gimmick.gimmick_name', $request->input('gimmick_name'));
			}
        } );
		 $dir = $dir->leftJoin('gimmick', 'gimmick.dir_rpc', '=', 'dirrpc.no');
        $dir = $dir->orderBy('dirrpc.'.$sort[0], $sort[1]);

        \Log::info($dir->toSql());
        \Log::info($dir->getBindings());

        return $dir;
    }

  

}
