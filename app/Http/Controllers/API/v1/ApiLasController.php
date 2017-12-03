<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\ApiLas;
use App\Models\EForm;
use Illuminate\Http\Request;
use Auth;

class ApiLasController extends Controller
{
    public function index(Request $request) {
    	// print_r($request->header('pn'));exit();
    	$ApiLas  = new ApiLas();
        $pn      = $request->header('pn');
    	$respons = $request->all();
    	$method  = $respons['requestMethod'];
    	$data	 = $respons['requestData'];

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

            case 'inquiryListPutusan':
                $inquiry = $ApiLas->inquiryListPutusan($data);
                return $inquiry;
                break;

    		default:
    			return array('status' => 400, 'message' => 'Uknown request method');
    			break;
    	}
    }

    public function insertAllAnalisa($request) {
        $ApiLas = new ApiLas();
        $pn     = request()->header('pn');
        $inquiryUserLAS = $ApiLas->inquiryUserLAS('00066777');
        print_r($inquiryUserLAS);exit();
        $eform  = EForm::findOrFail($request->id);
        $customer        = $eform->customer;
        $customer_detail = $customer->detail;

        
        print_r($data);exit();
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
            "hub_bank"               => empty($request['hub_bank'])? "9900" : $request['hub_bank'],
            "tgl_mulai_usaha"        => $request['tgl_mulai_usaha'],
            "pernah_pinjam"          => $request['pernah_pinjam'],
            "sumber_utama"           => $request['sumber_utama'],
            "usia_mpp"               => $request['usia_mpp'],
            "transaksi_normal_harian"=> $request['transaksi_normal_harian'],
            "keterangan_status_gelar"=> $request['keterangan_status_gelar'],
            "federal_wh_code"        => "1",
            "sub_customer_type"      => "I", //hardcode dari las
            "segmen_bisnis_bri"      => "RITEL", //hardcode dari las
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
            "id_instansi"            => "1"
        ];

        $insertDebitur = $ApiLas->insertDataDebtPerorangan($content_las_debt);

        
        // prescreening
        $content_prescreening = [
            "Fid_aplikasi"           => $insertDebitur['ID_APLIKASI'],
            "Ps_krd"                 => $request['Ps_krd'],
            "Pks"                    => $request['Pks'],
            "Daftar_hitam_bi"        => $request['Daftar_hitam_bi'],
            "Daftar_kredit_macet_bi" => $request['Daftar_kredit_macet_bi'],
            "Daftar_hitam_bri"       => $request['Daftar_hitam_bri'],
            "Tunggakan_di_bri"       => $request['Tunggakan_di_bri'],
            "Npl_instansi"           => $request['Npl_instansi'],
            "Sicd"                   => $request['Sicd'],
            "Hasil_prescreening"     => $request['Hasil_prescreening']
        ];

        $insertPrescreening = $ApiLas->insertPrescreeningBriguna($content_prescreening);

        // prescoring
        // $gaji_bersih = ($request['Gaji_per_bulan'] + $request['Pendapatan_profesi']) - $request['Potongan_per_bulan'];
        $content_las_prescoring = [
            "Fid_aplikasi"              => $insertDebitur['ID_APLIKASI'],
            "Fid_cif_las"               => $insertDebitur['CIF_LAS'],
            "Tp_produk"                 => "1",
            "Briguna_smart"             => "0",
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
            "Angsuran_lainnya"          => $data,
            "Gaji_per_bulan"            => $data,
            "Gaji_bersih_per_bulan"     => $data,
            "Maksimum_angsuran"         => $data,
            "Maksimum_plafond"          => $data,
            "Permohonan_kredit"         => $data,
            "Baki_debet"                => $data,
            "Plafond_usulan"            => $data,
            "Angsuran_usulan"           => $data,
            "Kelengkapan_dokumen"       => "1"
        ];

        $insertPrescoring = $ApiLas->insertPrescoringBriguna($content_las_prescoring);

        $content_insertKreditBriguna = [
            "Fid_aplikasi"                    => $insertDebitur['ID_APLIKASI'],
            "Cif_las"                         => $insertDebitur['CIF_LAS'],
            "Kode_fasilitas"                  => $request['Kode_fasilitas'],
            "Sandi_stp"                       => $request['Sandi_stp'],
            "Provisi_kredit"                  => $request['Provisi'],
            "Penalty"                         => $request['Penalty'],
            "Bupln"                           => $request['Bupln'],
            "Agribisnis"                      => $request['Agribisnis'],
            "Sumber_aplikasi"                 => $request['Sumber_aplikasi'],
            "Pengadilan_terdekat"             => $request['Pengadilan_terdekat'],
            "Biaya_administrasi"              => $request['Biaya_administrasi'],
            "Perusahaan_asuransi"             => $request['Program_asuransi'],
            "Tujuan_penggunaan_kredit"        => $request['Tujuan_penggunaan_kredit'],
            "Penggunaan_kredit"               => $request['Penggunaan_kredit'],
            "Jenis_penggunaan"                => $request['Jenis_penggunaan'],
            "Sifat_kredit"                    => $request['Sifat_kredit'],
            "Sektor_ekonomi_sid"              => $request['Sektor_ekonomi'],
            "Sifat_kredit_lbu"                => $request['Sifat_kredit_lbu'],
            "Jenis_kredit_lbu"                => $request['Jenis_kredit_lbu'],
            "Kategori_kredit_lbu"             => $request['Kategori_kredit_lbu'],
            "Jenis_penggunaan_lbu"            => $request['Jenis_penggunaan_lbu'],
            "Tp_produk"                       => "1",
            "Id_kredit"                       => $data['Id_kredit'],
            "Baru_perpanjangan"               => $data['Baru_perpanjangan'],
            "Jenis_fasilitas"                 => $data['Jenis_fasilitas'],
            "Tujuan_membuka_rek"              => $data['Tujuan_membuka_rek'],
            "Segmen_owner"                    => $data['Segmen_owner'],
            "Sub_segmen_owner"                => $data['Sub_segmen_owner'],
            "Kode_jangka_waktu"               => $data['Kode_jangka_waktu'],
            "Jangka_waktu"                    => $data['Jangka_waktu'],
            "Sisa_jangka_waktu_sd_penyesuaian"=> $data['Sisa_jangka_waktu_sd_penyesuaian'],
            "Valuta"                          => $data['Valuta'],
            "Maksimum_plafond"                => $data['Maksimum_plafond'],
            "Plafon_induk"                    => $data['Plafon_induk'],
            "Interest_payment_frequency"      => $data['Interest_payment_frequency'],
            "Pemrakarsa1"                     => $data['Pemrakarsa1'],
            "Uker_pemrakarsa"                 => $data['Uker_pemrakarsa'],
            "Sifat_suku_bunga"                => $data['Sifat_suku_bunga'],
            "Discount"                        => $data['Discount'],
            "Golongan_kredit"                 => $data['Golongan_kredit'],
            "Orientasi_penggunaan"            => "9",
            "Sektor_ekonomi_lbu"              => "11126",
            "Lokasi_proyek"                   => "0591",
            "Nilai_proyek"                    => "0",
            "Fasilitas_penyedia_dana"         => "1999",
            "Baki_debet"                      => "0",
            "Original_amount"                 => "0",
            "Kelonggaran_tarik"               => "0",
            "Denda"                           => "0",
            "Premi_asuransi_jiwa"             => "0.75",
            "Premi_beban_bri"                 => "0",
            "Premi_beban_debitur"             => "0.75",
            "Tanggal_jatuh_tempo"             => "29112099",
            "Grace_period"                    => "0",
            "Flag_promo"                      => "1",
            "Fid_promo"                       => "4",
            "Status_takeover"                 => "0",
            "Bank_asal_takeover"              => "",
            "Data2"                           => ""
        ];

        $insertKredit = $ApiLas->insertDataKreditBriguna($content_insertKreditBriguna);
    }
}
