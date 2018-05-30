<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use AsmxLas;
use DB;

class ApiLas extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'eforms';

    public function scopeFilter( $query, Request $request ) {
        // \Log::info($request->all());
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['created_at', 'asc'];
        $search = $request->input('search');
        \Log::info($search);
        // $eform = $query->where( function( $eform ) use( $request, &$user ) {
        //     if( $request->has( 'status' ) ) {
        //         if( $request->status == 'Submit' ) {
        //             $eform->whereIsApproved( true );

        //         } else if( $request->status == 'Initiate' ) {
        //             $eform->has( 'visit_report' )->whereIsApproved( false );

        //         } else if( $request->status == 'Dispose' ) {
        //             $eform->whereNotNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );

        //         } else if( $request->status == 'Rekomend' ) {
        //             $eform->whereNull( 'ao_id' )->has( 'visit_report', '<', 1 )->whereIsApproved( false );

        //         } elseif ($request->status == 'Rejected' || $request->status == 'Approval1' || $request->status == 'Approval2') {
        //             $eform->where('status_eform', $request->status);

        //         }
        //     }
        // });

        // if ($request->has('search')) {
        //     $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
        //         ->where( function( $eform ) use( $request, &$user ) {
        //             $eform->orWhere('users.last_name', 'ilike', '%'.strtolower($request->input('search')).'%')
        //                 ->orWhere('users.first_name', 'ilike', '%'.strtolower($request->input('search')).'%')
        //                 ->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('search').'%');
        //         } );
        // } else {
        //     if ($request->has('customer_name')){
        //         $eform = $eform->leftJoin('users', 'users.id', '=', 'eforms.user_id')
        //             ->where( function( $eform ) use( $request, &$user ) {
        //                 $eform->orWhere(\DB::raw("LOWER(concat(users.first_name,' ', users.last_name))"), "like", "%".strtolower($request->input('customer_name'))."%");
        //             });
        //     }

        //     if ($request->has('ref_number')) {
        //         $eform = $eform->where( function( $eform ) use( $request, &$user ) {
        //             $eform->orWhere('eforms.ref_number', 'ilike', '%'.$request->input('ref_number').'%');
        //         } );
        //     }
        // }
        $sortir = ['eforms.created_at', 'asc'];
        if ($sort[0] == "tgl_pengajuan" || $sort[0] == "action" || $sort[0] == "STATUS" || $sort[0] == "status_screening" || $sort[0] == "fid_tp_produk") {
            $sortir = ['eforms.created_at', $sort[1]];
        } else if ($sort[0] == "cif") {
            $sortir = ['briguna.cif', $sort[1]];
        } else if ($sort[0] == "id_aplikasi") {
            $sortir = ['briguna.id_aplikasi', $sort[1]];
        } else if ($sort[0] == "ref_number") {
            $sortir = ['eforms.ref_number', $sort[1]];
        } else if ($sort[0] == "nama_pegawai") {
            $sortir = ['eforms.ao_name', $sort[1]];
        } else if ($sort[0] == "namadeb") {
            $sortir = ['users.first_name', $sort[1]];
        } else if ($sort[0] == "request_amount") {
            $sortir = ['briguna.request_amount', $sort[1]];
        }

        $eform = DB::table('eforms')
                 ->select('eforms.ref_number','eforms.created_at','eforms.prescreening_status',
                    'eforms.ao_name','eforms.ao_position','eforms.pinca_name','eforms.is_screening',
                    'eforms.pinca_position','briguna.id','briguna.id_aplikasi',
                    'briguna.no_rekening','briguna.request_amount','briguna.Plafond_usulan',
                    'briguna.is_send','briguna.eform_id','briguna.tp_produk',
                    'briguna.tgl_analisa','briguna.tgl_putusan','briguna.cif',
                    'customer_details.nik','customer_details.is_verified',
                    'customer_details.address','customer_details.mother_name',
                    'customer_details.birth_date','users.first_name','users.last_name',
                    'users.mobile_phone','users.gender'
                   )
                 ->join('briguna', 'eforms.id', '=', 'briguna.eform_id')
                 ->join('customer_details', 'customer_details.user_id', '=', 'eforms.user_id')
                 ->join('users', 'users.id', '=', 'eforms.user_id')
                 // ->where('eforms.branch_id', '=', $branch)
                 ->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($request['branch_id']))
                 ->orWhere('eforms.created_at', 'ilike', '%'.$search.'%')
                 ->orWhere('briguna.id_aplikasi', 'ilike', '%'.$search.'%')
                 ->orWhere('eforms.ref_number', 'ilike', '%'.$search.'%')
                 ->orWhere('eforms.ao_name', 'ilike', '%'.$search.'%')
                 ->orWhere('briguna.cif', 'ilike', '%'.$search.'%')
                 ->orWhere('users.first_name', 'ilike', '%'.$search.'%')
                 ->orWhere('briguna.request_amount', 'ilike', '%'.$search.'%')
                 ->orderBy($sortir)
                 ->limit($request['limit'])
                 ->offset($request['start'])
                 ->get();
        
        $eform = $eform->toArray();
        // \Log::info($eform);
        $eform = json_decode(json_encode($eform), True);
        \Log::info("query select briguna berhasil");
        return $eform;
    }

    public function eform_briguna($branch = null, $id_aplikasi = null) {
        if (!empty($branch) && !empty($id_aplikasi)) {
            $eforms = DB::table('eforms')
                 ->select(['eforms.ref_number','eforms.created_at','eforms.prescreening_status',
                    'eforms.ao_name','eforms.ao_position','eforms.pinca_name','eforms.is_screening',
                    'eforms.pinca_position','briguna.id','briguna.id_aplikasi',
                    'briguna.no_rekening','briguna.request_amount','briguna.Plafond_usulan',
                    'briguna.is_send','briguna.eform_id','briguna.tp_produk','briguna.tgl_pencairan',
                    'briguna.tgl_analisa','briguna.tgl_putusan','briguna.cif','briguna.keterangan',
                    'customer_details.nik','customer_details.is_verified',
                    'customer_details.address','customer_details.mother_name',
                    'customer_details.birth_date','users.first_name','users.last_name',
                    'users.mobile_phone','users.gender',
                    \DB::Raw("case when eforms.ao_id is not null or eforms.ao_id != '' 
                        then 'Disposisi Pengajuan'
                        else 'Pengajuan Kredit' end as status_pengajuan"),
                    \DB::Raw("case when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '1') 
                        then 'Briguna Karya'
                        when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '2') 
                        then 'Briguna Umum'
                        when briguna.tp_produk = '2' then 'Briguna Purna'
                        when briguna.tp_produk = '10' then 'Briguna Micro'
                        when briguna.tp_produk = '22' then 'Briguna Talangan'
                        when briguna.tp_produk = '28' then 'Briguna Pekerja BRI'
                        else 'Lainnya' end as product"),
                    \DB::Raw("case when briguna.is_send = 0 then 'APPROVAL'
                        when briguna.is_send = 1 then 'APPROVED'
                        when briguna.is_send = 2 then 'UNAPPROVED'
                        when briguna.is_send = 3 then 'DITOLAK'
                        when briguna.is_send = 4 then 'VOID ADK'
                        when briguna.is_send = 5 then 'APPROVED PENCAIRAN'
                        when briguna.is_send = 6 then 'DISBURSED'
                        when briguna.is_send = 7 then 'SENT TO BRINETS'
                        when briguna.is_send = 8 then 'AGREE BY MP'
                        when briguna.is_send = 9 then 'DITOLAK'
                        when briguna.is_send = 10 then 'AGREE BY AMP'
                        when briguna.is_send = 11 then 'AGREE BY PINCAPEM'
                        when briguna.is_send = 12 then 'AGREE BY PINCA'
                        when briguna.is_send = 13 then 'AGREE BY WAPINWIL'
                        when briguna.is_send = 14 then 'AGREE BY WAPINCASUS'
                        when briguna.is_send = 15 then 'NAIK KETINGKAT LEBIH TINGGI BY AMP'
                        when briguna.is_send = 16 then 'NAIK KETINGKAT LEBIH TINGGI BY MP'
                        when briguna.is_send = 17 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCAPEM'
                        when briguna.is_send = 18 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCA'
                        when briguna.is_send = 19 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINWIL'
                        when briguna.is_send = 20 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINCASUS'
                        when briguna.is_send = 21 then 'MENGEMBALIKAN DATA KE AO'
                        else '-' end as status_putusan")
                    ])
                 ->join('briguna', 'eforms.id', '=', 'briguna.eform_id')
                 ->join('customer_details', 'customer_details.user_id', '=', 'eforms.user_id')
                 ->join('users', 'users.id', '=', 'eforms.user_id')
                 ->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($branch))
                 ->whereIn('briguna.id_aplikasi', $id_aplikasi)
                 ->orderBy('eforms.created_at', 'desc')
                 ->get();
        } else {
            $eforms = DB::table('eforms')
                 ->select(['eforms.ref_number','eforms.created_at','eforms.prescreening_status',
                    'eforms.ao_name','eforms.ao_position','eforms.pinca_name','eforms.is_screening',
                    'eforms.pinca_position','briguna.id','briguna.id_aplikasi',
                    'briguna.no_rekening','briguna.request_amount','briguna.Plafond_usulan',
                    'briguna.is_send','briguna.eform_id','briguna.tp_produk','briguna.tgl_pencairan',
                    'briguna.tgl_analisa','briguna.tgl_putusan','briguna.cif','briguna.keterangan',
                    'customer_details.nik','customer_details.is_verified',
                    'customer_details.address','customer_details.mother_name',
                    'customer_details.birth_date','users.first_name','users.last_name',
                    'users.mobile_phone','users.gender',
                    \DB::Raw("case when eforms.ao_id is not null or eforms.ao_id != '' 
                        then 'Disposisi Pengajuan'
                        else 'Pengajuan Kredit' end as status_pengajuan"),
                    \DB::Raw("case when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '1')
                        then 'Briguna Karya'
                        when (briguna.tp_produk = '1' and briguna.jenis_pinjaman_id = '2') 
                        then 'Briguna Umum'
                        when briguna.tp_produk = '2' then 'Briguna Purna'
                        when briguna.tp_produk = '10' then 'Briguna Micro'
                        when briguna.tp_produk = '22' then 'Briguna Talangan'
                        when briguna.tp_produk = '28' then 'Briguna Pekerja BRI'
                        else 'Lainnya' end as product"),
                    \DB::Raw("case when briguna.is_send = 0 then 'APPROVAL'
                        when briguna.is_send = 1 then 'APPROVED'
                        when briguna.is_send = 2 then 'UNAPPROVED'
                        when briguna.is_send = 3 then 'DITOLAK'
                        when briguna.is_send = 4 then 'VOID ADK'
                        when briguna.is_send = 5 then 'APPROVED PENCAIRAN'
                        when briguna.is_send = 6 then 'DISBURSED'
                        when briguna.is_send = 7 then 'SENT TO BRINETS'
                        when briguna.is_send = 8 then 'AGREE BY MP'
                        when briguna.is_send = 9 then 'DITOLAK'
                        when briguna.is_send = 10 then 'AGREE BY AMP'
                        when briguna.is_send = 11 then 'AGREE BY PINCAPEM'
                        when briguna.is_send = 12 then 'AGREE BY PINCA'
                        when briguna.is_send = 13 then 'AGREE BY WAPINWIL'
                        when briguna.is_send = 14 then 'AGREE BY WAPINCASUS'
                        when briguna.is_send = 15 then 'NAIK KETINGKAT LEBIH TINGGI BY AMP'
                        when briguna.is_send = 16 then 'NAIK KETINGKAT LEBIH TINGGI BY MP'
                        when briguna.is_send = 17 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCAPEM'
                        when briguna.is_send = 18 then 'NAIK KETINGKAT LEBIH TINGGI BY PINCA'
                        when briguna.is_send = 19 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINWIL'
                        when briguna.is_send = 20 then 'NAIK KETINGKAT LEBIH TINGGI BY WAPINCASUS'
                        when briguna.is_send = 21 then 'MENGEMBALIKAN DATA KE AO'
                        else '-' end as status_putusan")
                    ])
                 ->join('briguna', 'eforms.id', '=', 'briguna.eform_id')
                 ->join('customer_details', 'customer_details.user_id', '=', 'eforms.user_id')
                 ->join('users', 'users.id', '=', 'eforms.user_id')
                 ->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($branch))
                 ->orderBy('eforms.created_at', 'desc')
                 ->get();
        }
        
        $eforms = $eforms->toArray();
        // \Log::info($eforms);
        $eforms = json_decode(json_encode($eforms), True);
                            
        \Log::info("query select briguna berhasil");
        return $eforms;
    }

    public function insertDataDebtPerorangan($data) {
        \Log::info("parameter data debitur perorangan masuk");
        try {
            $insertDebitur = AsmxLas::setEndpoint('insertDataDebtPerorangan')
                ->setBody([
                    'JSONData' => json_encode($data),
                    'flag_sp' => 1
                ])->post('form_params');

            return $insertDebitur;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertPrescreeningBriguna($data) {
        \Log::info("parameter data prescreening masuk");
        try {
            $insertPrescreening = AsmxLas::setEndpoint('insertPrescreeningBriguna')
                ->setBody([
                    'JSON' => json_encode($data)
                ])->post('form_params');

            return $insertPrescreening;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertPrescoringBriguna($data) {
        \Log::info("parameter data prescoring masuk");
        try {
            /*$content_las_prescoring = [
                "Fid_aplikasi"              => $data['Fid_aplikasi'],
                "Fid_cif_las"               => $data['Fid_cif_las'],
                "Tgl_perkiraan_pensiun"     => '07122039',
                "Sifat_suku_bunga"     =>'annuitas',
                "Briguna_profesi"     =>'1',
                "Gaji_per_bulan"     =>'10000000',
                "Pendapatan_profesi"     =>'0',
                "Potongan_per_bulan"     =>'0',
                "Plafond_briguna_existing"     =>'0',
                "Angsuran_briguna_existing"     =>'0',
                "Suku_bunga"     =>'12',
                "Jangka_waktu"     =>'24',
                "Maksimum_plafond"     =>'159325404',
                "Permohonan_kredit"     =>'120000000',
                "Baki_debet"     =>'0',
                "Plafond_usulan"     =>'120000000',
                "Angsuran_usulan"     =>'5648817',
                "Rek_simpanan_bri"     =>'1',
                "Riwayat_pinjaman"     =>'0',
                "Penguasaan_cashflow"     =>'2',
                "Payroll"     =>'1',
                "Gaji_bersih_per_bulan"     =>'10000000',
                "Maksimum_angsuran"     =>'7500000',
                "pembayaran_gaji"     =>'1',
                "Angsuran_lainnya"          => "0",                    
                "Tp_produk"                 => "1",
                "Briguna_smart"             => "0",
                "Kelengkapan_dokumen"       => "1"
            ];*/
            $insertPrescoring = AsmxLas::setEndpoint('insertPrescoringBriguna')
                ->setBody([
                    'JSON' => json_encode($data)
                ])->post('form_params');

            return $insertPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertDataKreditBriguna($data) {
        \Log::info("parameter data kredit masuk");
        try {
            $insertKreditBriguna = AsmxLas::setEndpoint('insertDataKreditBriguna')
                ->setBody([
                    'JSON' => json_encode($data)
                ])->post('form_params');

            return $insertKreditBriguna;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertAgunanLainnya($data) {
        \Log::info("parameter data masuk");
        try {
            $content_insertAgunanLainnya = [
                "id_aplikasi"                       => "42067",
                "id_kredit"                         => "0",
                "id_agunan"                         => "0",
                "nama_debitur"                      => "aswin",
                "jenis_agunan"                      => "29",
                "deskripsi"                         => "agunan lain",
                "jenis_mata_uang"                   => "IDR",
                "nama_barang_dagangan"              => "agunan lain",
                "atas_nama_pemilik"                 => "aswin taopik zaenudin",
                "nomor_bukti_kepemilikan"           => "bukti01",
                "tanggal_bukti_kepemilikan"         => "28022015",
                "alamat_pemilik_agunan"             => "jln menteng tenggulun",
                "kelurahan"                         => "menteng",
                "kecamatan"                         => "menteng",
                "lokasi_dati_2"                     => "6110",
                "nilai_pasar_wajar"                 => "1000000",
                "nilai_likuidasi"                   => "1000000",
                "proyeksi_nilai_pasar_wajar"        => "1000000",
                "proyeksi_nilai_likuidasi"          => "1000000",
                "paripasu"                          => "true",
                "eligibility"                       => "Eligible",
                "penilaian_agunan_oleh"             => "bank",
                "tanggal_penilaian_agunan_terakhir" => "28022010",
                "penilai_independent"               => "",
                "jenis_pengikatan"                  => "06",
                "no_sertifikat_pengikatan"          => "06ikat",
                "flag_asuransi"                     => "tidak",
                "nama_perusahaan_asuransi"          => "",
                "nilai_asuransi"                    => "0",
                "nilai_likuidasi_saat_realisasi"    => "1000000",
                "nilai_pengikatan"                  => "1000000",
                "fid_cif"                           => "11036586",
                "nilai_agunan_bank"                 => "1000000",
                "bukti_kepemilikan"                 => "Kwitansi/Faktur/Invoice",
                "nilai_pengurang_ppap"              => "0",
                "klasifikasi_agunan"                => "tambahan",
                "porsi_agunan"                      => "100"
            ];

            $insertAgunanLainnya = AsmxLas::setEndpoint('insertAgunanLainnya')
                ->setBody([
                    'JSONData' => json_encode($content_insertAgunanLainnya)
                ])->post('form_params');

            return $insertAgunanLainnya;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function hitungCRSBrigunaKarya($data) {
        \Log::info("parameter data hitung crs masuk");
        try {
            $Id_aplikasi = $data;

            $hitungPrescoring = AsmxLas::setEndpoint('hitungCRSBrigunaKarya')
                ->setBody([
                    'id_Aplikasi' => !isset($Id_aplikasi) ? "" : $Id_aplikasi
                ])->post('form_params');

            return $hitungPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function kirimPemutus($data) {
        \Log::info("parameter data kirim pemutus masuk");
        try {
            $kirim = AsmxLas::setEndpoint('kirimPemutus')
                ->setBody([
                    'id_aplikasi'   => !isset($data['id_aplikasi']) ? "" : $data['id_aplikasi'],
                    'uid'           => !isset($data['uid']) ? "" : $data['uid'],
                    'flag_override' => !isset($data['flag_override']) ? "" : $data['flag_override']
                ])->post('form_params');

            return $kirim;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function getStatusInterface($data) {
        \Log::info("parameter data getStatus Interface masuk");
        try {
            $get = AsmxLas::setEndpoint('getStatusInterface')
                ->setBody([
                    'id_aplikasi'   => !isset($data) ? "" : $data
                ])->post('form_params');

            return $get;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function putusSepakat($data) {
        \Log::info("parameter data putus sepakat masuk");
        try {
            $conten_putusan = [
                "id_aplikasi" => !isset($data['id_aplikasi']) ? "" : $data['id_aplikasi'],
                "uid"         => !isset($data['uid']) ? "" : $data['uid'],
                "flag_putusan"=> !isset($data['flag_putusan']) ? "" : $data['flag_putusan'],
                "catatan"     => !isset($data['catatan']) ? "" : $data['catatan']
            ];
            // print_r($data);
            // print_r($conten_putusan);exit();
            $putusan = AsmxLas::setEndpoint('putusSepakat')
                ->setBody([
                    'JSONData'   => json_encode($conten_putusan)
                ])->post('form_params');
                
            return $putusan;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryInstansiBriguna($data) {
        \Log::info("parameter data instansi briguna masuk");
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryInstansiBriguna')
                ->setBody([
                    'branch'   => !isset($data) ? "" : $data
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquirySifatKredit($data) {
        \Log::info("parameter data sifat kredit masuk");
        try {
            $inquiry = AsmxLas::setEndpoint('inquirySifatKredit')
                ->setBody([
                    'param'   => !isset($data) ? "" : $data
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryHistoryDebiturPerorangan($data) {
        \Log::info("parameter data history debitur masuk");
        try {
            $conten = [
                'nik'           => !isset($data['nik']) ? "" : $data['nik'],
                'tp_produk'     => !isset($data['tp_produk']) ? "" : $data['tp_produk'],
                'uid_pemrakarsa'=> !isset($data['uid_pemrakarsa']) ? "" : $data['uid_pemrakarsa']
            ];

            $inquiry = AsmxLas::setEndpoint('inquiryHistoryDebiturPerorangan')
                ->setBody([
                    'JSONData' => json_encode($conten)
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryListPutusan($data) {
        \Log::info("parameter data list putusan masuk");
        try {
            $uid = $data;

            $inquiryListPutusan = AsmxLas::setEndpoint('inquiryListPutusan')
                ->setBody([
                    'uid' => !isset($uid) ? "" : $uid
                ])->post('form_params');

            return $inquiryListPutusan;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryListVerputADK($data) {
        \Log::info("parameter data verput adk masuk");
        try {
            $inquiryListADK = AsmxLas::setEndpoint('inquiryListVerputADK')
                ->setBody([
                    'branch' => !isset($data) ? "" : $data
                ])->post('form_params');

            return $inquiryListADK;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryPremiAJKO($data) {
        \Log::info("parameter data inquiry premiajko masuk");
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryPremiAJKO')
                ->setBody([
                    'JSONData' => json_encode($data)
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryUserLAS($data) {
        \Log::info("parameter data user las masuk");
        try {
            $pn = $data;

            $inquiryUserLAS = AsmxLas::setEndpoint('inquiryUserLAS')
                ->setBody([
                    'PN' => !isset($pn) ? "" : $pn
                ])->post('form_params');

            return $inquiryUserLAS;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryGelar() {
        try {
            $inquiryGelar = AsmxLas::setEndpoint('inquiryGelar')
                ->post('form_params');

            return $inquiryGelar;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryLoantype() {
        try {
            $inquiryLoantype = AsmxLas::setEndpoint('inquiryLoantype')
                ->post('form_params');

            return $inquiryLoantype;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryJenisPenggunaan() {
        try {
            $inquiryLoantype = AsmxLas::setEndpoint('inquiryJenisPenggunaan')
                ->post('form_params');

            return $inquiryLoantype;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryJenisPenggunaanLBU() {
        try {
            $inquiryLoantype = AsmxLas::setEndpoint('inquiryJenisPenggunaanLBU')
                ->post('form_params');

            return $inquiryLoantype;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquirySektorEkonomiLBU() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquirySektorEkonomiLBU')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquirySifatKreditLBU() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquirySifatKreditLBU')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryJenisKreditLBU() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryJenisKreditLBU')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryPromoBriguna() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryPromoBriguna')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryTujuanPenggunaan() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryTujuanPenggunaan')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryBidangUsaha() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryBidangUsaha')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryBank() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryBank')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryHubunganBank() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryHubunganBank')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryPekerjaan() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryPekerjaan')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryJabatan() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryJabatan')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryJenisPekerjaan() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryJenisPekerjaan')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryDati2() {
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryDati2')
                ->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }
}
