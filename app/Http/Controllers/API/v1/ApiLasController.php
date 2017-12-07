<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KodePos;
use App\Models\ApiLas;
use App\Models\EForm;

class ApiLasController extends Controller
{
    public function index(Request $request) {        
    	// print_r($request);exit();
    	$ApiLas  = new ApiLas();
    	$respons = $request->all();
    	$method  = $respons['requestMethod'];
        $data    = '';
        if (!empty($respons['requestData'])) {
            $data = $respons['requestData'];
            // print_r($data);exit();
        }
        
    	switch ($method) {
    		case 'insertDataDebtPerorangan':
                $data['kodepos']            = '';
                $data['kelurahan']          = '';
                $data['kecamatan']          = '';
                $data['kabupaten']          = '';
                $data['kodepos_domisili']   = '';
                $data['kelurahan_domisili'] = '';
                $data['kecamatan_domisili'] = '';
                $data['kabupaten_domisili'] = '';
                $data['kota_domisili']      = '';
                $data['propinsi_domisili']  = '';
                $data['kodepos_usaha']      = '';
                $data['kelurahan_usaha']    = '';
                $data['kecamatan_usaha']    = '';
                $data['kabupaten_usaha']    = '';
                $data['kota_usaha']         = '';
                $data['propinsi_usaha']     = '';                

                if (!empty($data['kode_pos'])) {
                    $kode_pos = ['key' => $data['kode_pos']];
                    $kodepos  = KodePos::filter($kode_pos)->get();
                    $pos      = $kodepos->toArray();
                    if (!empty($pos)) {
                        foreach ($pos as $index => $value) {
                            // print_r($value);
                            $data['kodepos']   = $value['postal_code'];
                            $data['kelurahan'] = $value['Kelurahan'];
                            $data['kecamatan'] = $value['Kecamatan'];
                            $data['kabupaten'] = $value['Kota'];
                        }
                    }
                }
                
                if (!empty($data['kode_pos_domisili'])) {
                    $kode_pos_dom = ['key' => $data['kode_pos_domisili']];
                    $kodepos_dom  = KodePos::filter($kode_pos_dom)->get();
                    $pos_dom      = $kodepos_dom->toArray();
                    if (!empty($pos_dom)) {
                        foreach ($pos_dom as $index => $value) {
                            // print_r($value);exit();
                            $data['kodepos_domisili']   = $value['postal_code'];
                            $data['kelurahan_domisili'] = $value['Kelurahan'];
                            $data['kecamatan_domisili'] = $value['Kecamatan'];
                            $data['kabupaten_domisili'] = $value['Kota'];
                            $data['kota_domisili']      = $value['Kota'];
                            $data['propinsi_domisili']  = $value['Propinsi'];
                            $data['kodepos_usaha']      = $value['postal_code'];
                            $data['kelurahan_usaha']    = $value['Kelurahan'];
                            $data['kecamatan_usaha']    = $value['Kecamatan'];
                            $data['kabupaten_usaha']    = $value['Kota'];
                            $data['kota_usaha']         = $value['Kota'];
                            $data['propinsi_usaha']     = $value['Propinsi'];
                        }
                    }
                }
                // print_r($data);exit();
                $this->insertAllAnalisa($data);
		        // $insert = $ApiLas->insertDataDebtPerorangan($data);
    			// return $insert;
                break;

            case 'insertPrescreeningBriguna':
                $insert = $ApiLas->insertPrescreeningBriguna($data);
                return $insert;
                break;
    	
            case 'insertPrescoringBriguna':
                $insert = $ApiLas->insertPrescoringBriguna($data);
                return $insert;
                break;

            case 'insertDataKreditBriguna':
                $insert = $ApiLas->insertDataKreditBriguna($data);
                return $insert;
                break;

            case 'insertAgunanLainnya':
                $insert = $ApiLas->insertAgunanLainnya($data);
                return $insert;
                break;

            case 'hitungCRSBrigunaKarya':
                $hitung = $ApiLas->hitungCRSBrigunaKarya($data);
                return $hitung;
                break;

            case 'kirimPemutus':
                $kirim = $ApiLas->kirimPemutus($data);
                return $kirim;
                break;

            case 'getStatusInterface':
                $getData = $ApiLas->getStatusInterface($data);
                return $getData;
                break;

            case 'putusSepakat':
                $this->putusan($data);
                // $putus = $ApiLas->putusSepakat($data);
                // return $putus;
                break;

            case 'inquiryInstansiBriguna':
                $kirim = $ApiLas->inquiryInstansiBriguna($data);
                return $kirim;
                break;

            case 'inquirySifatKredit':
                $kirim = $ApiLas->inquirySifatKredit($data);
                return $kirim;
                break;

            case 'inquiryListPutusan':
                $pn      = substr('00000000'. $data, -8 );
                $inquiryUserLAS = $ApiLas->inquiryUserLAS($pn);
                $uid     = $inquiryUserLAS['items'][0]['uid'];
                $inquiry = $ApiLas->inquiryListPutusan($uid);
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryGelar':
                $inquiry = $ApiLas->inquiryGelar();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;
                
            case 'inquiryLoantype':
                $inquiry = $ApiLas->inquiryLoantype();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryJenisPenggunaan':
                $inquiry = $ApiLas->inquiryJenisPenggunaan();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryJenisPenggunaanLBU':
                $inquiry = $ApiLas->inquiryJenisPenggunaanLBU();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquirySektorEkonomiLBU':
                $inquiry = $ApiLas->inquirySektorEkonomiLBU();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquirySifatKreditLBU':
                $inquiry = $ApiLas->inquirySifatKreditLBU();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryJenisKreditLBU':
                $inquiry = $ApiLas->inquiryJenisKreditLBU();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryPromoBriguna':
                $inquiry = $ApiLas->inquiryPromoBriguna();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryTujuanPenggunaan':
                $inquiry = $ApiLas->inquiryTujuanPenggunaan();
                $conten  = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryBidangUsaha':
                $inquiry = $ApiLas->inquiryBidangUsaha();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryBank':
                $inquiry = $ApiLas->inquiryBank();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryHubunganBank':
                $inquiry = $ApiLas->inquiryHubunganBank();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryPekerjaan':
                $inquiry = $ApiLas->inquiryPekerjaan();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryJabatan':
                $inquiry = $ApiLas->inquiryJabatan();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

            case 'inquiryJenisPekerjaan':
                $inquiry = $ApiLas->inquiryJenisPekerjaan();
                $conten = $this->return_conten($inquiry);
                return $conten;
                break;

    		default:
    			return [
                    'code' => 05, 
                    'descriptions' => 'Uknown request method',
                    'contents' => [
                        'data' => ''
                    ]
                ];
    			break;
    	}
    }

    public function return_conten($respons){
        $conten = [
            'code'         => $respons['statusCode'],
            'descriptions' => $respons['statusDesc'],
            'contents' => [
                'data' => $respons['items']
            ]
        ];
        return $conten;
    }

    public function putusan($data) {
        $user_pn = request()->header('pn');
        $pn      = substr('00000000'. $user_pn, -8 );
        $inquiryUserLAS = $ApiLas->inquiryUserLAS($pn);
        $uid   = $inquiryUserLAS['items'][0]['uid'];
        print_r($inquiryUserLAS);exit();
        $conten_putusan = [
            "id_aplikasi" => $data['id_aplikasi'],
            "uid"         => $uid,
            "flag_putusan"=> "6",
            "catatan"     => "testis"
        ];

        $putus = $ApiLas->putusSepakat($conten_putusan);
        return $putus;
    }

    public function insertAllAnalisa($request) {
        $ApiLas  = new ApiLas();
        $user_pn = request()->header('pn');
        $pn      = substr('00000000'. $user_pn, -8 );
        $inquiryUserLAS = $ApiLas->inquiryUserLAS($pn);
        $uid     = $inquiryUserLAS['items'][0]['uid'];
        $uker    = substr($inquiryUserLAS['items'][0]['kode_cabang'], -5);
        // print_r($uker);
        // print_r($request);exit();

        // insert data debitur
        if ($request['transaksi_normal_harian'] == '1') {
            $gaji = "G1";
        } else if ($request['transaksi_normal_harian'] == '2') {
            $gaji = "G3";
        } else if ($request['transaksi_normal_harian'] == '3') {
            $gaji = "G4";
        } else if ($request['transaksi_normal_harian'] == '4' || $request['transaksi_normal_harian'] == '5') {
            $gaji = "G5";
        } else {
            $gaji = "G2";
        }

        $content_las_debt = [
            "uid"                   => $uid, // inquiry user las
            "kode_cabang"           => $uker, // inquiry user las
            "penghasilan_per_bulan" => $gaji,
            "nama_debitur_1"        => $request['nama_debitur'],
            "nama_tanpa_gelar"      => $request['nama_debitur'],
            "alias"                 => $request['nama_debitur'],
            "tgl_lahir"             => $request['tgl_lahir'],
            "id_instansi"           => $request['instansi'],
            "nama_pasangan"         => $request['nama_pasangan'],
            "tgl_lahir_pasangan"    => $request['tgl_lahir_pasangan'],
            "no_ktp_pasangan"       => $request['no_ktp_pasangan'],
            "perjanjian_pisah_harta"=> $request['perjanjian_pisah_harta'],
            "status_gelar"          => $request['status_gelar'],
            "keterangan_status_gelar"=> $request['keterangan_status_gelar'],
            "nama_ibu"              => $request['nama_ibu'],
            "jenis_kelamin"         => $request['jenis_kelamin'],
            "no_ktp"                => $request['no_ktp'],
            "tempat_lahir"          => $request['tempat_lahir'],
            "usia_mpp"              => $request['usia_mpp'],
            "alamat"                => $request['alamat'],
            "alamat_usaha"          => $request['alamat_domisili'],
            "alamat_domisili"       => $request['alamat_domisili'],
            "fixed_line"            => $request['no_tlp'],
            "no_hp"                 => $request['no_hp'],
            "lama_menetap"          => $request['lama_menetap'],
            "email"                 => $request['email'],
            "tgl_mulai_usaha"       => $request['tgl_mulai_bekerja'],
            "kepemilikan_tempat_tinggal" => $request['kepemilikan_tempat_tinggal'],
            "jumlah_tanggungan"     => $request['jumlah_tanggungan'],
            "nama_kelg"             => $request['nama_keluarga'],
            "telp_kelg"             => $request['no_tlp_keluarga'],
            "status_perkawinan"     => $request['status_perkawinan'],
            "jenis_rekening"        => $request['jenis_rekening'],
            "nama_bank_lain"        => $request['nama_bank_lain'],
            "pekerjaan_debitur"     => $request['pekerjaan_debitur'],
            "pernah_pinjam"         => $request['pernah_pinjam'],
            "transaksi_normal_harian"=> $request['transaksi_normal_harian'],
            "agama"                 => $request['agama'],
            "ket_agama"             => $request['ket_agama'],
            "nama_perusahaan"       => $request['company_name'],
            "bidang_usaha"          => $request['job_field_id'],   
            "jenis_pekerjaan"       => $request['job_type_id'],
            "ket_pekerjaan"         => $request['job_field_id'],
            "jabatan"               => $request['position'],
            "kode_pos"              => $request['kodepos'],
            "kodepos_usaha"         => $request['kodepos_usaha'],
            "kodepos_domisili"      => $request['kodepos_domisili'],
            "kelurahan"             => $request['kelurahan'],
            "kelurahan_domisili"    => $request['kelurahan_domisili'],
            "kelurahan_usaha"       => $request['kelurahan_usaha'],
            "kecamatan"             => $request['kecamatan'],
            "kecamatan_domisili"    => $request['kecamatan_domisili'],
            "kecamatan_usaha"       => $request['kecamatan_usaha'],
            "kabupaten"             => $request['kabupaten'],
            "kota_domisili"         => $request['kota_domisili'],
            "propinsi_domisili"     => $request['propinsi_domisili'],
            "kota_usaha"            => $request['kota_usaha'],
            "propinsi_usaha"        => $request['propinsi_usaha'],
            "nama_debitur_2"        => "",
            "nama_debitur_3"        => "",
            "nama_debitur_4"        => "",
            "sumber_utama"          => "1", // hardcode gaji dari mybri
            "tp_produk"             => "1", // hardcode dari las
            "cif_las"               => "0", // hardcode debitur baru
            "expired_ktp"           => "31122899", // hardcode
            "kategori_portofolio"   => "175", // hardcode las
            "kewarganegaraan"       => "ID", // hardcode dari las
            "negara_domisili"       => "ID", // hardcode dari las
            "golongan_debitur_sid"  => "907", // hardcode dari las
            "golongan_debitur_lbu"  => "886", // hardcode dari las
            "customer_type"         => "I", // hardcode dari las
            "sub_customer_type"     => "I", // hardcode dari las
            "hub_bank"              => "9900", // hardcode dari las
            "segmen_bisnis_bri"     => "RITEL", // hardcode dari las
            "tgl_mulai_debitur"     => date('d-m-Y'), // hardcode tgl prakarsa
            "federal_wh_code"       => "1", // hardcode dari las
            "resident_flag"         => "Y", // hardcode dari las
            "tujuan_membuka_rekening"=> "ZZ", // hardcode
            "ket_buka_rekening"     => "Pinjaman" // hardcode

        ];

        $insertDebitur = $ApiLas->insertDataDebtPerorangan($content_las_debt);
        print_r("-------- masuk insert debitur ---------");
        \Log::info($insertDebitur);
        if ($insertDebitur['statusCode'] == '01') {
            // prescreening
            $content_prescreening = [
                "Fid_aplikasi"           => $insertDebitur['items']['ID_APLIKASI'],
                "Ps_krd"                 => "0",
                "Pks"                    => "0",
                "Daftar_hitam_bi"        => "0",
                "Daftar_kredit_macet_bi" => "0",
                "Daftar_hitam_bri"       => "0",
                "Tunggakan_di_bri"       => "0",
                "Npl_instansi"           => "0",
                "Sicd"                   => "0",
                "Hasil_prescreening"     => "Diproses lebih lanjut"
            ];

            $insertPrescreening = $ApiLas->insertPrescreeningBriguna($content_prescreening);
            print_r("-------- masuk insert prescreening ---------");
            \Log::info($insertPrescreening);
            if ($insertPrescreening['statusCode'] == '01') {
                // prescoring
                $content_las_prescoring = [
                    "Fid_aplikasi"              => $insertDebitur['items']['ID_APLIKASI'],
                    "Fid_cif_las"               => $insertDebitur['items']['CIF_LAS'],
                    "Tgl_perkiraan_pensiun"     => $request['Tgl_perkiraan_pensiun'],
                    "Sifat_suku_bunga"          => $request['Sifat_suku_bunga'],
                    "Briguna_profesi"           => $request['Briguna_profesi'],
                    "Gaji_per_bulan"            => $request['Gaji_per_bulan'],
                    "Pendapatan_profesi"        => $request['Pendapatan_profesi'],
                    "Potongan_per_bulan"        => $request['Potongan_per_bulan'],
                    "Plafond_briguna_existing"  => $request['Plafond_briguna_existing'],
                    "Angsuran_briguna_existing" => $request['Angsuran_briguna_existing'],
                    "Suku_bunga"                => $request['Suku_bunga'],
                    "Jangka_waktu"              => $request['Jangka_waktu'],
                    "Maksimum_plafond"          => $request['Maksimum_plafond'],
                    "Permohonan_kredit"         => $request['Permohonan_kredit'],
                    "Baki_debet"                => $request['Baki_debet'],
                    "Plafond_usulan"            => $request['Plafond_usulan'],
                    "Angsuran_usulan"           => $request['Angsuran_usulan'],
                    "Rek_simpanan_bri"          => $request['Rek_simpanan_bri'],
                    "Riwayat_pinjaman"          => $request['Riwayat_pinjaman'],
                    "Penguasaan_cashflow"       => $request['Penguasaan_cashflow'],
                    "Payroll"                   => $request['Payroll'],
                    "Gaji_bersih_per_bulan"     => $request['Gaji_bersih_per_bulan'],
                    "Maksimum_angsuran"         => $request['Maksimum_angsuran'],
                    "Angsuran_lainnya"          => "0",                    
                    "Tp_produk"                 => "1",
                    "Briguna_smart"             => "0",
                    "Kelengkapan_dokumen"       => "1"
                ];

                $insertPrescoring = $ApiLas->insertPrescoringBriguna($content_las_prescoring);
                print_r("-------- masuk insert prescoring ---------");
                \Log::info($insertPrescoring);
                if ($insertPrescoring['statusCode'] == '01') {
                    $jangka = $request['Jangka_waktu'];
                    $tgl_jatuh_tempo = date('d-m-Y',strtotime('+'.$jangka.' months'));
                    // insert dataKredit
                    $content_insertKreditBriguna = [
                        "Fid_aplikasi"                 => $insertDebitur['items']['ID_APLIKASI'],
                        "Cif_las"                      => $insertDebitur['items']['CIF_LAS'],
                        "Pemrakarsa1"                  => $uid,
                        "Uker_pemrakarsa"              => $uker,
                        "Tanggal_jatuh_tempo"          => $tgl_jatuh_tempo,
                        "Tujuan_membuka_rek"           => $request['Tujuan_membuka_rek'],
                        "Jangka_waktu"                 => $request['Jangka_waktu'],
                        "Briguna_smart"                => $request['Briguna_smart'],
                        "Kode_fasilitas"               => $request['Kode_fasilitas'],
                        "Tujuan_penggunaan_kredit"     => $request['Tujuan_penggunaan_kredit'],
                        "Penggunaan_kredit"            => $request['Penggunaan_kredit'],
                        "Provisi_kredit"               => $request['Provisi_kredit'],
                        "Biaya_administrasi"           => $request['Biaya_administrasi'],                        
                        "Penalty"                      => $request['Penalty'],
                        "Perusahaan_asuransi"          => $request['Nama_perusahaan_asuransi'],
                        "Premi_asuransi_jiwa"          => $request['Premi_asuransi_jiwa'],
                        "Premi_beban_bri"              => $request['Premi_beban_bri'],
                        "Premi_beban_debitur"          => $request['Premi_beban_debitur'],
                        "Flag_promo"                   => $request['promo'],
                        "Fid_promo"                    => $request['nama_program_promo'],
                        "Pengadilan_terdekat"          => $request['Pengadilan_terdekat'],
                        "Bupln"                        => $request['Bupln'],
                        "Agribisnis"                   => $request['Agribisnis'],
                        "Sandi_stp"                    => $request['Sandi_stp'],
                        "Sifat_kredit"                 => $request['Sifat_kredit'],
                        "Jenis_penggunaan"             => $request['Jenis_penggunaan'],
                        "Sektor_ekonomi_sid"           => $request['Sektor_ekonomi'],
                        "Jenis_kredit_lbu"             => $request['Jenis_kredit_lbu'],
                        "Sifat_kredit_lbu"             => $request['Sifat_kredit_lbu'],
                        "Kategori_kredit_lbu"          => $request['Kategori_kredit_lbu'],
                        "Jenis_penggunaan_lbu"         => $request['Jenis_penggunaan_lbu'],
                        "Sumber_aplikasi"              => $request['Sumber_aplikasi'],                        
                        "Sektor_ekonomi_lbu"           => $request['Sektor_ekonomi'],
                        "Maksimum_plafond"             => $request['Maksimum_plafond'],
                        "Plafon_induk"                 => "0", // hardcode las
                        "Tp_produk"                    => "1", // hardcode las
                        "Id_kredit"                    => "0", // hardcode las
                        "Baru_perpanjangan"            => "0", // hardcode las
                        "Jenis_fasilitas"              => "0605", // hardcode las
                        "Sisa_jangka_waktu_sd_penyesuaian"=> "0", // hardcode
                        "Valuta"                       => "idr", // hardcode
                        "Segmen_owner"                 => "RITEL", // hardcode
                        "Sub_segmen_owner"             => "RITEL", // hardcode
                        "Kode_jangka_waktu"            => "M", // hardcode las
                        "Interest_payment_frequency"   => "1", // hardcode las
                        "Sifat_suku_bunga"             => "FIXED", // hardcode
                        "Discount"                     => "0", // hardcode las
                        "Golongan_kredit"              => "20", // hardcode las
                        "Orientasi_penggunaan"         => "9", // hardcode las
                        "Lokasi_proyek"                => "0591", // hardcode las
                        "Nilai_proyek"                 => "0", // hardcode las
                        "Fasilitas_penyedia_dana"      => "1999", // hardcode las
                        "Baki_debet"                   => "0", // hardcode las
                        "Original_amount"              => "0", // hardcode las
                        "Kelonggaran_tarik"            => "0", // hardcode las
                        "Denda"                        => "0", // hardcode las
                        "Grace_period"                 => "0", // hardcode las
                        "Status_takeover"              => "0", // hardcode las
                        "Bank_asal_takeover"           => "", // hardcode las
                        "Data2"                        => "" // kosongin aja
                    ];

                    $insertKredit = $ApiLas->insertDataKreditBriguna($content_insertKreditBriguna);
                    print_r("-------- masuk insert kredit ---------");
                    \Log::info($insertKredit);
                    if ($insertKredit['statusCode'] == '01') {
                        // Hitung CRS
                        $hitung = $ApiLas->hitungCRSBrigunaKarya($insertDebitur['items']['ID_APLIKASI']);
                        print_r("-------- masuk hitungCRS ---------");
                        \Log::info($hitung);
                        if ($hitung['statusCode'] == '01') {
                            $override = 'Y';
                            if ($hitung['items']['cutoff'] == 'Y') {
                                $override = 'N';
                            }
                            // Kirim Pemutus
                            $conten = [
                                'id_aplikasi'   => $insertDebitur['items']['ID_APLIKASI'],
                                'uid'           => $uid,
                                'flag_override' => $override
                            ];
                            $kirim = $ApiLas->kirimPemutus($conten);
                            print_r("-------- masuk kirimPemutus ---------");
                            \Log::info($kirim);
                        } else {
                            return $kirim;
                        }
                    } else {
                        return $hitung;
                    }
                } else {
                    return $insertKredit;
                }
            } else {
                return $insertPrescreening;
            }

            $params = [
                "uid"                   => $uid, // inquiry user las
                "uid_pemrakarsa"        => $uker, // inquiry user las
                "tp_produk"             => "1", // hardcode dari las
                "id_aplikasi"           => $insertDebitur['items']['ID_APLIKASI'],
                "cif_las"               => $insertDebitur['items']['CIF_LAS']


                /*"nama_tanpa_gelar"      => $request['nama_debitur'],
                "status_gelar"          => $request['status_gelar'],
                "keterangan_status_gelar"=> $request['keterangan_status_gelar'],
                "jenis_kelamin"         => $request['jenis_kelamin'],
                "no_ktp"                => $request['no_ktp'],
                "tgl_lahir"             => $request['tgl_lahir'],
                "tempat_lahir"          => $request['tempat_lahir'],
                "usia_mpp"              => $request['usia_mpp'],
                "alamat"                => $request['alamat'],
                "kode_pos"              => $request['kodepos'],
                "lama_menetap"          => $request['lama_menetap'],
                "kepemilikan_tempat_tinggal" => $request['kepemilikan_tempat_tinggal'],
                "penghasilan_per_bulan" => $gaji,
                "nama_pasangan"         => $request['nama_pasangan'],
                "tgl_lahir_pasangan"    => $request['tgl_lahir_pasangan'],
                "no_ktp_pasangan"       => $request['no_ktp_pasangan'],
                "nama_ibu"              => $request['nama_ibu'],
                "email"                 => $request['email'],
                "tgl_mulai_usaha"       => $request['tgl_mulai_bekerja'],
                "kelurahan"             => $request['kelurahan'],
                "kecamatan"             => $request['kecamatan'],
                "fixed_line"            => $request['no_tlp'],
                "no_hp"                 => $request['no_hp'],
                "kewarganegaraan"       => "ID", // hardcode dari las
                "expired_ktp"           => "31122899", // hardcode
                "sumber_utama"          => "1", // hardcode gaji dari mybri
                "kategori_portofolio"   => "175", // hardcode las   
                "negara_domisili"       => "ID", // hardcode dari las
                "golongan_debitur_sid"  => "907", // hardcode dari las
                "golongan_debitur_lbu"  => "886", // hardcode dari las
                "customer_type"         => "I", // hardcode dari las
                "sub_customer_type"     => "I", // hardcode dari las
                "hub_bank"              => "9900", // hardcode dari las
                "segmen_bisnis_bri"     => "RITEL", // hardcode dari las
                "tgl_mulai_debitur"     => date('d-m-Y'), // hardcode tgl prakarsa
                "federal_wh_code"       => "1", // hardcode dari las
                "resident_flag"         => "Y", // hardcode dari las
                "tujuan_membuka_rekening"=> "ZZ", // hardcode
                "ket_buka_rekening"     => "Pinjaman" // hardcode*/
            ];

            return $insertDebitur;
        } else {
            return $insertDebitur;
        }
    }

    public function updateBriguna($request) {
        $params = [
            "sumber_utama"          => "1", // hardcode gaji dari mybri
            "tp_produk"             => "1", // hardcode dari las
            "cif_las"               => "0", // hardcode debitur baru
            "expired_ktp"           => "31122899", // hardcode
            "kategori_portofolio"   => "175", // hardcode las
            "kewarganegaraan"       => "ID", // hardcode dari las
            "negara_domisili"       => "ID", // hardcode dari las
            "golongan_debitur_sid"  => "907", // hardcode dari las
            "golongan_debitur_lbu"  => "886", // hardcode dari las
            "customer_type"         => "I", // hardcode dari las
            "sub_customer_type"     => "I", // hardcode dari las
            "hub_bank"              => "9900", // hardcode dari las
            "segmen_bisnis_bri"     => "RITEL", // hardcode dari las
            "tgl_mulai_debitur"     => date('d-m-Y'), // hardcode tgl prakarsa
            "federal_wh_code"       => "1", // hardcode dari las
            "resident_flag"         => "Y", // hardcode dari las
            "tujuan_membuka_rekening"=> "ZZ", // hardcode
            "ket_buka_rekening"     => "Pinjaman" // hardcode
        ];
    }
}
