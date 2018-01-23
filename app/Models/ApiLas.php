<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
    public function scopeFilter($query, $id) {
        $eforms = $query->where( function( $eforms ) use( $request, &$user ) {  
            $kode = $id;
            $eforms->Where('eforms.id', $kode);
        });

        $eforms->join('briguna', 'eform_id', '=', 'eforms.id');
        $eforms = $eforms->select([
            'eforms.*','briguna.*',
            \DB::Raw("case when eforms.id is not null then 2 else 1 end as new_order")
        ]);
        \Log::info("query berhasil");
        return $eforms;
    }

    public function eform_briguna($branch = null) {
        if (!empty($branch)) {
            $eforms = DB::table('eforms')
                 ->select('eforms.ref_number','eforms.created_at','eforms.ao_id',
                    'eforms.ao_name','eforms.ao_position','eforms.pinca_name',
                    'eforms.pinca_position','briguna.id','briguna.id_aplikasi',
                    'briguna.no_rekening','briguna.request_amount','briguna.Plafond_usulan',
                    'briguna.is_send','briguna.eform_id','briguna.tp_produk',
                    'customer_details.nik','customer_details.birth_date',
                    'customer_details.address','customer_details.mother_name',
                    'users.first_name','users.last_name','users.mobile_phone','users.gender'
                   )
                 ->join('briguna', 'eforms.id', '=', 'briguna.eform_id')
                 ->join('customer_details', 'customer_details.user_id', '=', 'eforms.user_id')
                 ->join('users', 'users.id', '=', 'eforms.user_id')
                 // ->where('eforms.branch_id', '=', $branch)
                 ->where(\DB::Raw("TRIM(LEADING '0' FROM eforms.branch_id)"), (string) intval($branch))
                 ->orderBy('eforms.created_at', 'desc')
                 ->get();
        } else {
            $eforms = DB::table('eforms')
                 ->select('eforms.ref_number','eforms.created_at','eforms.ao_id',
                    'eforms.ao_name','eforms.ao_position','eforms.pinca_name',
                    'eforms.pinca_position','briguna.id','briguna.id_aplikasi',
                    'briguna.no_rekening','briguna.request_amount','briguna.Plafond_usulan',
                    'briguna.is_send','briguna.eform_id','briguna.tp_produk',
                    'customer_details.nik','customer_details.birth_date',
                    'customer_details.address','customer_details.mother_name',
                    'users.first_name','users.last_name','users.mobile_phone','users.gender'
                   )
                 ->join('briguna', 'eforms.id', '=', 'briguna.eform_id')
                 ->join('customer_details', 'customer_details.user_id', '=', 'eforms.user_id')
                 ->join('users', 'users.id', '=', 'eforms.user_id')
                 ->orderBy('eforms.created_at', 'desc')
                 ->get();
        }
        
        $eforms = $eforms->toArray();
        $eforms = json_decode(json_encode($eforms), True);
        
        \Log::info("query berhasil");
        return $eforms;
    }

    public function insertDataDebtPerorangan($data) {
        \Log::info("parameter data debitur perorangan masuk");
        try {
            /*$content_las_debt = [
                "tp_produk"             => $data['tp_produk'],
                "uid"                   => $data['uid'],
                "kode_cabang"           => $data['kode_cabang'],
                "cif_las"               => $data['cif_las'],
                "no_ktp"                => $data['no_ktp'],
                "expired_ktp"           => $data['expired_ktp'],
                "nama_debitur_1"        => $data['nama_debitur_1'],
                "nama_tanpa_gelar"      => $data['nama_tanpa_gelar'],
                "nama_debitur_2"        => $data['nama_debitur_2'],
                "nama_debitur_3"        => $data['nama_debitur_3'],
                "nama_debitur_4"        => $data['nama_debitur_4'],
                "tgl_lahir"             => $data['tgl_lahir'],
                "tempat_lahir"          => $data['tempat_lahir'],
                "status_perkawinan"     => $data['status_perkawinan'],
                "nama_pasangan"         => $data['nama_pasangan'],
                "tgl_lahir_pasangan"    => $data['tgl_lahir_pasangan'],
                "no_ktp_pasangan"       => $data['no_ktp_pasangan'],
                "perjanjian_pisah_harta"=> $data['perjanjian_pisah_harta'],
                "jumlah_tanggungan"     => $data['jumlah_tanggungan'],
                "bidang_usaha"          => $data['bidang_usaha'],
                "status_gelar"          => $data['status_gelar'],
                "keterangan_status_gelar" => $data['keterangan_status_gelar'],
                "jenis_kelamin"         => $data['jenis_kelamin'],
                "nama_ibu"              => $data['nama_ibu'],
                "alamat"                => $data['alamat'],
                "kelurahan"             => $data['kelurahan'],
                "kecamatan"             => $data['kecamatan'],
                "kabupaten"             => $data['kabupaten'],
                "kode_pos"              => $data['kode_pos'],
                "fixed_line"            => $data['fixed_line'],
                "no_hp"                 => $data['no_hp'],
                "lama_menetap"          => $data['lama_menetap'],
                "email"                 => $data['email'],
                "tgl_mulai_usaha"       => $data['tgl_mulai_usaha'],
                "kewarganegaraan"       => $data['kewarganegaraan'],
                "negara_domisili"       => $data['negara_domisili'],
                "kepemilikan_tempat_tinggal" => $data['kepemilikan_tempat_tinggal'],
                "kategori_portofolio"   => $data['kategori_portofolio'],
                "golongan_debitur_sid"  => $data['golongan_debitur_sid'],
                "golongan_debitur_lbu"  => $data['golongan_debitur_lbu'],
                "nama_kelg"             => $data['nama_kelg'],
                "telp_kelg"             => $data['telp_kelg'],
                "tgl_mulai_debitur"     => $data['tgl_mulai_debitur'],
                "jenis_rekening"        => $data['jenis_rekening'],
                "nama_bank_lain"        => $data['nama_bank_lain'],
                "pekerjaan_debitur"     => $data['pekerjaan_debitur'],
                "alamat_usaha"          => $data['alamat_usaha'],
                "nama_perusahaan"       => $data['nama_perusahaan'],
                "resident_flag"         => $data['resident_flag'],
                "customer_type"         => $data['customer_type'],
                "hub_bank"              => $data['hub_bank'],
                "pernah_pinjam"         => $data['pernah_pinjam'],
                "sumber_utama"          => $data['sumber_utama'],
                "federal_wh_code"       => $data['federal_wh_code'],
                "sub_customer_type"     => $data['sub_customer_type'],
                "segmen_bisnis_bri"     => $data['segmen_bisnis_bri'],
                "transaksi_normal_harian" => $data['transaksi_normal_harian'],
                "alias"                 => $data['alias'],
                "agama"                 => $data['agama'],
                "ket_agama"             => $data['ket_agama'],
                "alamat_domisili"       => $data['alamat_domisili'],
                "kodepos_domisili"      => $data['kodepos_domisili'],
                "kelurahan_domisili"    => $data['kelurahan_domisili'],
                "kecamatan_domisili"    => $data['kecamatan_domisili'],
                "kota_domisili"         => $data['kota_domisili'],
                "propinsi_domisili"     => $data['propinsi_domisili'],
                "jenis_pekerjaan"       => $data['jenis_pekerjaan'],
                "ket_pekerjaan"         => $data['ket_pekerjaan'],
                "jabatan"               => $data['jabatan'],
                "kelurahan_usaha"       => $data['kelurahan_usaha'],
                "kecamatan_usaha"       => $data['kecamatan_usaha'],
                "kota_usaha"            => $data['kota_usaha'],
                "propinsi_usaha"        => $data['propinsi_usaha'],
                "kodepos_usaha"         => $data['kodepos_usaha'],
                "tujuan_membuka_rekening" => $data['tujuan_membuka_rekening'],
                "ket_buka_rekening"     => $data['ket_buka_rekening'],
                "penghasilan_per_bulan" => $data['penghasilan_per_bulan'],
                "usia_mpp"              => $data['usia_mpp'],
                "id_instansi"           => $data['id_instansi']
            ];*/
            // print_r($content_las_debt);exit();
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
            /*$content_insertKreditBriguna = [
                "Fid_aplikasi"                     => !isset($data['Fid_aplikasi']) ? "" : $data['Fid_aplikasi'],
                "Cif_las"                          => !isset($data['Cif_las']) ? "" : $data['Cif_las'],
                "Tp_produk"                        => !isset($data['Tp_produk']) ? "" : $data['Tp_produk'],
                "Id_kredit"                        => !isset($data['Id_kredit']) ? "" : $data['Id_kredit'],
                "Baru_perpanjangan"                => !isset($data['Baru_perpanjangan']) ? "" : $data['Baru_perpanjangan'],
                "Jenis_fasilitas"                  => !isset($data['Jenis_fasilitas']) ? "" : $data['Jenis_fasilitas'],
                "Tujuan_membuka_rek"               => !isset($data['Tujuan_membuka_rek']) ? "" : $data['Tujuan_membuka_rek'],
                "Segmen_owner"                     => !isset($data['Segmen_owner']) ? "" : $data['Segmen_owner'],
                "Sub_segmen_owner"                 => !isset($data['Sub_segmen_owner']) ? "" : $data['Sub_segmen_owner'],
                "Kode_jangka_waktu"                => !isset($data['Kode_jangka_waktu']) ? "" : $data['Kode_jangka_waktu'],
                "Jangka_waktu"                     => !isset($data['Jangka_waktu']) ? "" : $data['Jangka_waktu'],
                "Sisa_jangka_waktu_sd_penyesuaian" => !isset($data['Sisa_jangka_waktu_sd_penyesuaian']) ? "" : $data['Sisa_jangka_waktu_sd_penyesuaian'],
                "Penggunaan_kredit"                => !isset($data['Penggunaan_kredit']) ? "" : $data['Penggunaan_kredit'],
                "Tujuan_penggunaan_kredit"         => !isset($data['Tujuan_penggunaan_kredit']) ? "" : $data['Tujuan_penggunaan_kredit'],
                "Valuta"                           => !isset($data['Valuta']) ? "" : $data['Valuta'],
                "Maksimum_plafond"                 => !isset($data['Maksimum_plafond']) ? "" : $data['Maksimum_plafond'],
                "Plafon_induk"                     => !isset($data['Plafon_induk']) ? "" : $data['Plafon_induk'],
                "Provisi_kredit"                   => !isset($data['Provisi_kredit']) ? "" : $data['Provisi_kredit'],
                "Biaya_administrasi"               => !isset($data['Biaya_administrasi']) ? "" : $data['Biaya_administrasi'],
                "Penalty"                          => !isset($data['Penalty']) ? "" : $data['Penalty'],
                "Interest_payment_frequency"       => !isset($data['Interest_payment_frequency']) ? "" : $data['Interest_payment_frequency'],
                "Pemrakarsa1"                      => !isset($data['Pemrakarsa1']) ? "" : $data['Pemrakarsa1'],
                "Uker_pemrakarsa"                  => !isset($data['Uker_pemrakarsa']) ? "" : $data['Uker_pemrakarsa'],
                "Sifat_suku_bunga"                 => !isset($data['Sifat_suku_bunga']) ? "" : $data['Sifat_suku_bunga'],
                "Discount"                         => !isset($data['Discount']) ? "" : $data['Discount'],
                "Pengadilan_terdekat"              => !isset($data['Pengadilan_terdekat']) ? "" : $data['Pengadilan_terdekat'],
                "Bupln"                            => !isset($data['Bupln']) ? "" : $data['Bupln'],
                "Agribisnis"                       => !isset($data['Agribisnis']) ? "" : $data['Agribisnis'],
                "Sifat_kredit"                     => !isset($data['Sifat_kredit']) ? "" : $data['Sifat_kredit'],
                "Golongan_kredit"                  => !isset($data['Golongan_kredit']) ? "" : $data['Golongan_kredit'],
                "Kode_fasilitas"                   => !isset($data['Kode_fasilitas']) ? "" : $data['Kode_fasilitas'],
                "Sandi_stp"                        => !isset($data['Sandi_stp']) ? "" : $data['Sandi_stp'],
                "Jenis_penggunaan"                 => !isset($data['Jenis_penggunaan']) ? "" : $data['Jenis_penggunaan'],
                "Orientasi_penggunaan"             => !isset($data['Orientasi_penggunaan']) ? "" : $data['Orientasi_penggunaan'],
                "Sektor_ekonomi_sid"               => !isset($data['Sektor_ekonomi_sid']) ? "" : $data['Sektor_ekonomi_sid'],
                "Sektor_ekonomi_lbu"               => !isset($data['Sektor_ekonomi_lbu']) ? "" : $data['Sektor_ekonomi_lbu'],
                "Jenis_kredit_lbu"                 => !isset($data['Jenis_kredit_lbu']) ? "" : $data['Jenis_kredit_lbu'],
                "Sifat_kredit_lbu"                 => !isset($data['Sifat_kredit_lbu']) ? "" : $data['Sifat_kredit_lbu'],
                "Kategori_kredit_lbu"              => !isset($data['Kategori_kredit_lbu']) ? "" : $data['Kategori_kredit_lbu'],
                "Jenis_penggunaan_lbu"             => !isset($data['Jenis_penggunaan_lbu']) ? "" : $data['Jenis_penggunaan_lbu'],
                "Lokasi_proyek"                    => !isset($data['Lokasi_proyek']) ? "" : $data['Lokasi_proyek'],
                "Nilai_proyek"                     => !isset($data['Nilai_proyek']) ? "" : $data['Nilai_proyek'],
                "Fasilitas_penyedia_dana"          => !isset($data['Fasilitas_penyedia_dana']) ? "" : $data['Fasilitas_penyedia_dana'],
                "Baki_debet"                       => !isset($data['Baki_debet']) ? "" : $data['Baki_debet'],
                "Original_amount"                  => !isset($data['Original_amount']) ? "" : $data['Original_amount'],
                "Kelonggaran_tarik"                => !isset($data['Kelonggaran_tarik']) ? "" : $data['Kelonggaran_tarik'],
                "Denda"                            => !isset($data['Denda']) ? "" : $data['Denda'],
                "Sumber_aplikasi"                  => !isset($data['Sumber_aplikasi']) ? "" : $data['Sumber_aplikasi'],
                "Premi_asuransi_jiwa"              => !isset($data['Premi_asuransi_jiwa']) ? "" : $data['Premi_asuransi_jiwa'],
                "Perusahaan_asuransi"              => !isset($data['Perusahaan_asuransi']) ? "" : $data['Perusahaan_asuransi'],
                "Premi_beban_bri"                  => !isset($data['Premi_beban_bri']) ? "" : $data['Premi_beban_bri'],
                "Premi_beban_debitur"              => !isset($data['Premi_beban_debitur']) ? "" : $data['Premi_beban_debitur'],
                "Tanggal_jatuh_tempo"              => !isset($data['Tanggal_jatuh_tempo']) ? "" : $data['Tanggal_jatuh_tempo'],
                "Grace_period"                     => !isset($data['Grace_period']) ? "" : $data['Grace_period'],
                "Flag_promo"                       => !isset($data['Flag_promo']) ? "" : $data['Flag_promo'],
                "Fid_promo"                        => !isset($data['Fid_promo']) ? "" : $data['Fid_promo'],
                "Status_takeover"                  => !isset($data['Status_takeover']) ? "" : $data['Status_takeover'],
                "Bank_asal_takeover"               => !isset($data['Bank_asal_takeover']) ? "" : $data['Bank_asal_takeover'],
                "Data2"                            => !isset($data['Data2']) ? "" : $data['Data2']
            ];*/

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
