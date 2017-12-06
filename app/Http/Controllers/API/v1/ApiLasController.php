<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\EFormController;
use App\Models\ApiLas;
use App\Models\EForm;
use Illuminate\Http\Request;
use Auth;

class ApiLasController extends Controller
{
    public function index(Request $request) {        
    	// print_r($request);exit();
    	$ApiLas  = new ApiLas();
    	$respons = $request->all();
    	$method  = $respons['requestMethod'];
        if (!empty($respons['requestData'])) {
            $data = $respons['requestData'];
        }

    	switch ($method) {
    		case 'insertDataDebtPerorangan':
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
                $inquiry = $ApiLas->inquiryListPutusan($data);
                // $conten = $this->return_conten($inquiry);
                return $inquiry;
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
        // $EForm = new EFormController();
        // $eform = $EForm->show('int','1');
        // $customer        = $eform->customer;
        // $customer_detail = $customer->detail;
        // print_r($eform);exit();
        $user_pn = request()->header('pn');
        $pn      = substr('00000000'. $user_pn, -8 );
        $inquiryUserLAS = $ApiLas->inquiryUserLAS($pn);
        $uid   = $inquiryUserLAS['items'][0]['uid'];
        $uker  = substr($inquiryUserLAS['items'][0]['kode_cabang'], -5);
        print_r($uker);
        print_r($inquiryUserLAS);exit();
        // insert data debitur
        $kecamatan_domisili = '';
        $kabupaten_domisili = '';
        $kodepos_domisili   = '';
        $kelurahan_domisili = '';
        $kecamatan_usaha    = '';
        $kabupaten_usaha    = '';
        $kodepos_usaha      = '';
        $kelurahan_usaha    = '';
        $kecamatan          = '';
        $kabupaten          = '';
        $kodepos            = '';
        $kelurahan          = '';

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

        // $gaji = "G1";
        // if ($request['transaksi_normal_harian'] == '2') {
        //     # code...
        // }

        $content_las_debt = [
            "uid"                   => $uid,
            "kode_cabang"           => $uker,// inquiry user las
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

            "kode_pos"              => $kodepos,//?
            "kodepos_usaha"         => $kodepos_usaha,
            "kodepos_domisili"      => $kodepos_domisili,
            "kelurahan"             => $kelurahan,//?
            "kelurahan_domisili"    => $kelurahan_domisili,
            "kelurahan_usaha"       => $kelurahan_usaha,
            "kecamatan"             => $kecamatan,//?
            "kecamatan_domisili"    => $kecamatan_domisili,
            "kecamatan_usaha"       => $kecamatan_usaha,
            "kabupaten"             => $kabupaten,//?
            "kota_domisili"         => $kabupaten_domisili,
            "propinsi_domisili"     => $kabupaten_domisili,            
            "kota_usaha"            => $kabupaten_usaha,
            "propinsi_usaha"        => $kabupaten_usaha,
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
            "ket_buka_rekening"     => "Pinjaman", // hardcode
            "penghasilan_per_bulan" => "G1"
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
                    "Briguna_profesi"           => $request['Briguna_profesi'],
                    "Tgl_perkiraan_pensiun"     => $request['Tgl_perkiraan_pensiun'],
                    "Payroll"                   => $request['Payroll'],
                    "Pendapatan_profesi"        => $request['Pendapatan_profesi'],
                    "Potongan_per_bulan"        => $request['Potongan_per_bulan'],
                    "Plafond_briguna_existing"  => $request['Plafond_briguna_existing'],
                    "Angsuran_briguna_existing" => $request['Angsuran_briguna_existing'],
                    "Suku_bunga"                => $request['Suku_bunga'],
                    "Sifat_suku_bunga"          => $request['Sifat_suku_bunga'],
                    "Jangka_waktu"              => $request['Jangka_waktu'],
                    "Rek_simpanan_bri"          => $request['Rek_simpanan_bri'],
                    "Riwayat_pinjaman"          => $request['Riwayat_pinjaman'],
                    "Penguasaan_cashflow"       => $request['Penguasaan_cashflow'],
                    "Angsuran_lainnya"          => $customer,
                    "Gaji_per_bulan"            => $customer,
                    "Gaji_bersih_per_bulan"     => $customer,
                    "Maksimum_angsuran"         => $customer,
                    "Maksimum_plafond"          => $customer,
                    "Permohonan_kredit"         => $customer,
                    "Baki_debet"                => $customer,
                    "Plafond_usulan"            => $customer,
                    "Angsuran_usulan"           => $customer,
                    "Tp_produk"                 => "1",
                    "Briguna_smart"             => "0",
                    "Kelengkapan_dokumen"       => "1"
                ];

                $insertPrescoring = $ApiLas->insertPrescoringBriguna($content_las_prescoring);
                print_r("-------- masuk insert prescoring ---------");
                \Log::info($insertPrescoring);
                if ($insertPrescoring['statusCode'] == '01') {
                    $jangka = '24';
                    $tgl_jatuh_tempo = date('d-m-Y',strtotime('+'.$jangka.' months'));
                    // insert dataKredit
                    $content_insertKreditBriguna = [
                        "Fid_aplikasi"                 => $insertDebitur['items']['ID_APLIKASI'],
                        "Cif_las"                      => $insertDebitur['items']['CIF_LAS'],
                        "Pemrakarsa1"                  => $uid,
                        "Uker_pemrakarsa"              => $uker,
                        "Tanggal_jatuh_tempo"          => $tgl_jatuh_tempo,
                        "Kode_fasilitas"               => $request['Kode_fasilitas'],
                        "Sandi_stp"                    => $request['Sandi_stp'],
                        "Provisi_kredit"               => $request['Provisi'],
                        "Penalty"                      => $request['Penalty'],
                        "Bupln"                        => $request['Bupln'],
                        "Agribisnis"                   => $request['Agribisnis'],
                        "Sumber_aplikasi"              => $request['Sumber_aplikasi'],
                        "Pengadilan_terdekat"          => $request['Pengadilan_terdekat'],
                        "Biaya_administrasi"           => $request['Biaya_administrasi'],
                        "Perusahaan_asuransi"          => $request['Program_asuransi'],
                        "Tujuan_penggunaan_kredit"     => $request['Tujuan_penggunaan_kredit'],
                        "Penggunaan_kredit"            => $request['Penggunaan_kredit'],
                        "Jenis_penggunaan"             => $request['Jenis_penggunaan'],
                        "Sifat_kredit"                 => $request['Sifat_kredit'],
                        "Sektor_ekonomi_sid"           => $request['Sektor_ekonomi'],
                        "Sektor_ekonomi_lbu"           => $request['Sektor_ekonomi_lbu'],
                        "Sifat_kredit_lbu"             => $request['Sifat_kredit_lbu'],
                        "Jenis_kredit_lbu"             => $request['Jenis_kredit_lbu'],
                        "Kategori_kredit_lbu"          => $request['Kategori_kredit_lbu'],
                        "Jenis_penggunaan_lbu"         => $request['Jenis_penggunaan_lbu'],
                        "Maksimum_plafond"             => $eform['Maksimum_plafond'],
                        "Plafon_induk"                 => $eform['Plafon_induk'],
                        "Tujuan_membuka_rek"           => "3",
                        "Jangka_waktu"                 => "24",
                        "Tp_produk"                    => "1",
                        "Id_kredit"                    => "0",
                        "Baru_perpanjangan"            => "0",
                        "Jenis_fasilitas"              => "0605",
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
                        "Lokasi_proyek"                => "0591",
                        "Nilai_proyek"                 => "0", // hardcode las
                        "Fasilitas_penyedia_dana"      => "1999", // hardcode
                        "Baki_debet"                   => "0",
                        "Original_amount"              => "0",
                        "Kelonggaran_tarik"            => "0",
                        "Denda"                        => "0", // hardcode las
                        "Premi_asuransi_jiwa"          => "0.75",
                        "Premi_beban_bri"              => "0",
                        "Premi_beban_debitur"          => "0.75",
                        "Grace_period"                 => "0", // hardcode las
                        "Flag_promo"                   => "1",
                        "Fid_promo"                    => "4",
                        "Status_takeover"              => "0",
                        "Bank_asal_takeover"           => "",
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
            return $insertDebitur;
        } else {
            return $insertDebitur;
        }
    }
}
