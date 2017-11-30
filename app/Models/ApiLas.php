<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use AsmxLas;

class ApiLas extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = '';

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
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function insertDataDebtPerorangan($data) {
        \Log::info($data);
        try {
            /*print_r($data['tp_produk']);
            $inquiryUserLAS = AsmxLas::setEndpoint('inquiryUserLAS')
                ->setBody([
                    'PN' => $pn
                ])->post('form_params');
            print_r($data);exit();*/

            $content_las_debt = [
                "tp_produk"             => empty($data['tp_produk']) ? "10" : $data['tp_produk'],
                "uid"                   => empty($data['uid']) ? "10740" : $data['uid'],
                "kode_cabang"           => empty($data['kode_cabang']) ? "3143" : $data['kode_cabang'],
                "cif_las"               => empty($data['cif_las']) ? "11039307" : $data['cif_las'],
                "no_ktp"                => empty($data['no_ktp']) ? "3273240401890005" : $data['no_ktp'],
                "expired_ktp"           => empty($data['expired_ktp']) ? "31122097" : $data['expired_ktp'],
                "nama_debitur_1"        => empty($data['nama_debitur_1']) ? "" : $data['nama_debitur_1'],
                "nama_tanpa_gelar"      => empty($data['nama_tanpa_gelar']) ? "" : $data['nama_tanpa_gelar'],
                "nama_debitur_2"        => empty($data['nama_debitur_2']) ? "" : $data['nama_debitur_2'],
                "nama_debitur_3"        => empty($data['nama_debitur_3']) ? "" : $data['nama_debitur_3'],
                "nama_debitur_4"        => empty($data['nama_debitur_4']) ? "" : $data['nama_debitur_4'],
                "tgl_lahir"             => empty($data['tgl_lahir']) ? "" : $data['tgl_lahir'],
                "tempat_lahir"          => empty($data['tempat_lahir']) ? "" : $data['tempat_lahir'],
                "status_perkawinan"     => empty($data['status_perkawinan']) ? "" : $data['status_perkawinan'],
                "nama_pasangan"         => empty($data['nama_pasangan']) ? "" : $data['nama_pasangan'],
                "tgl_lahir_pasangan"    => empty($data['tgl_lahir_pasangan']) ? "" : $data['tgl_lahir_pasangan'],
                "no_ktp_pasangan"       => empty($data['no_ktp_pasangan']) ? "" : $data['no_ktp_pasangan'],
                "perjanjian_pisah_harta"=> empty($data['perjanjian_pisah_harta']) ? "" : $data['perjanjian_pisah_harta'],
                "jumlah_tanggungan"     => empty($data['jumlah_tanggungan']) ? "" : $data['jumlah_tanggungan'],
                "bidang_usaha"          => empty($data['bidang_usaha']) ? "" : $data['bidang_usaha'],
                "status_gelar"          => empty($data['status_gelar']) ? "" : $data['status_gelar'],
                "keterangan_status_gelar" => empty($data['keterangan_status_gelar']) ? "" : $data['keterangan_status_gelar'],
                "jenis_kelamin"         => empty($data['jenis_kelamin']) ? "" : $data['jenis_kelamin'],
                "nama_ibu"              => empty($data['nama_ibu']) ? "" : $data['nama_ibu'],
                "alamat"                => empty($data['alamat']) ? "" : $data['alamat'],
                "kelurahan"             => empty($data['kelurahan']) ? "" : $data['kelurahan'],
                "kecamatan"             => empty($data['kecamatan']) ? "" : $data['kecamatan'],
                "kabupaten"             => empty($data['kabupaten']) ? "" : $data['kabupaten'],
                "kode_pos"              => empty($data['kode_pos']) ? "" : $data['kode_pos'],
                "fixed_line"            => empty($data['fixed_line']) ? "" : $data['fixed_line'],
                "no_hp"                 => empty($data['no_hp']) ? "" : $data['no_hp'],
                "lama_menetap"          => empty($data['lama_menetap']) ? "" : $data['lama_menetap'],
                "email"                 => empty($data['email']) ? "" : $data['email'],
                "tgl_mulai_usaha"       => empty($data['tgl_mulai_usaha']) ? "" : $data['tgl_mulai_usaha'],
                "kewarganegaraan"       => empty($data['kewarganegaraan']) ? "" : $data['kewarganegaraan'],
                "negara_domisili"       => empty($data['negara_domisili']) ? "" : $data['negara_domisili'],
                "kepemilikan_tempat_tinggal" => empty($data['kepemilikan_tempat_tinggal']) ? "" : $data['kepemilikan_tempat_tinggal'],
                "kategori_portofolio"   => empty($data['kategori_portofolio']) ? "" : $data['kategori_portofolio'],
                "golongan_debitur_sid"  => empty($data['golongan_debitur_sid']) ? "" : $data['golongan_debitur_sid'],
                "golongan_debitur_lbu"  => empty($data['golongan_debitur_lbu']) ? "" : $data['golongan_debitur_lbu'],
                "nama_kelg"             => empty($data['nama_kelg']) ? "" : $data['nama_kelg'],
                "telp_kelg"             => empty($data['telp_kelg']) ? "" : $data['telp_kelg'],
                "tgl_mulai_debitur"     => empty($data['tgl_mulai_debitur']) ? "" : $data['tgl_mulai_debitur'],
                "jenis_rekening"        => empty($data['jenis_rekening']) ? "" : $data['jenis_rekening'],
                "nama_bank_lain"        => empty($data['nama_bank_lain']) ? "" : $data['nama_bank_lain'],
                "pekerjaan_debitur"     => empty($data['pekerjaan_debitur']) ? "" : $data['pekerjaan_debitur'],
                "alamat_usaha"          => empty($data['alamat_usaha']) ? "" : $data['alamat_usaha'],
                "nama_perusahaan"       => empty($data['nama_perusahaan']) ? "" : $data['nama_perusahaan'],
                "resident_flag"         => empty($data['resident_flag']) ? "" : $data['resident_flag'],
                "customer_type"         => empty($data['customer_type']) ? "" : $data['customer_type'],
                "hub_bank"              => empty($data['hub_bank']) ? "" : $data['hub_bank'],
                "pernah_pinjam"         => empty($data['pernah_pinjam']) ? "" : $data['pernah_pinjam'],
                "sumber_utama"          => empty($data['sumber_utama']) ? "" : $data['sumber_utama'],
                "federal_wh_code"       => empty($data['federal_wh_code']) ? "" : $data['federal_wh_code'],
                "sub_customer_type"     => empty($data['sub_customer_type']) ? "" : $data['sub_customer_type'],
                "segmen_bisnis_bri"     => empty($data['segmen_bisnis_bri']) ? "" : $data['segmen_bisnis_bri'],
                "transaksi_normal_harian" => "2",
                "alias"                 => empty($data['alias']) ? "" : $data['alias'],
                "agama"                 => empty($data['agama']) ? "" : $data['agama'],
                "ket_agama"             => empty($data['ket_agama']) ? "" : $data['ket_agama'],
                "alamat_domisili"       => empty($data['alamat_domisili']) ? "" : $data['alamat_domisili'],
                "kodepos_domisili"      => empty($data['kodepos_domisili']) ? "" : $data['kodepos_domisili'],
                "kelurahan_domisili"    => empty($data['kelurahan_domisili']) ? "" : $data['kelurahan_domisili'],
                "kecamatan_domisili"    => empty($data['kecamatan_domisili']) ? "" : $data['kecamatan_domisili'],
                "kota_domisili"         => empty($data['kota_domisili']) ? "" : $data['kota_domisili'],
                "propinsi_domisili"     => empty($data['propinsi_domisili']) ? "" : $data['propinsi_domisili'],
                "jenis_pekerjaan"       => empty($data['jenis_pekerjaan']) ? "" : $data['jenis_pekerjaan'],
                "ket_pekerjaan"         => empty($data['ket_pekerjaan']) ? "" : $data['ket_pekerjaan'],
                "jabatan"               => empty($data['jabatan']) ? "" : $data['jabatan'],
                "kelurahan_usaha"       => empty($data['kelurahan_usaha']) ? "" : $data['kelurahan_usaha'],
                "kecamatan_usaha"       => empty($data['kecamatan_usaha']) ? "" : $data['kecamatan_usaha'],
                "kota_usaha"            => empty($data['kota_usaha']) ? "" : $data['kota_usaha'],
                "propinsi_usaha"        => empty($data['propinsi_usaha']) ? "" : $data['propinsi_usaha'],
                "kodepos_usaha"         => empty($data['kodepos_usaha']) ? "" : $data['kodepos_usaha'],
                "tujuan_membuka_rekening" => empty($data['tujuan_membuka_rekening']) ? "" : $data['tujuan_membuka_rekening'],
                "ket_buka_rekening"     => empty($data['ket_buka_rekening']) ? "" : $data['ket_buka_rekening'],
                "penghasilan_per_bulan" => empty($data['penghasilan_per_bulan']) ? "" : $data['penghasilan_per_bulan'],
                "usia_mpp"              => empty($data['usia_mpp']) ? "" : $data['usia_mpp'],
                "id_instansi"           => empty($data['id_instansi']) ? "" : $data['id_instansi']
            ];

            $insertDebitur = AsmxLas::setEndpoint('insertDataDebtPerorangan')
                ->setBody([
                    'JSONData' => json_encode($content_las_debt),
                    'flag_sp' => 1
                ])->post('form_params');

            return $insertDebitur;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertPrescreeningBriguna($data) {
        \Log::info($data);
        try {
            $content_las_prescreening = [
                "Fid_aplikasi"           => "124",
                "Ps_krd"                 => "0", 
                "Pks"                    => "0",
                "Daftar_hitam_bi"        => "0",
                "Daftar_kredit_macet_bi" => "0",
                "Daftar_hitam_bri"       => "0",
                "Tunggakan_di_bri"       => "0",
                "Npl_instansi"           => "0",
                "Sicd"                   => "0",  
                "Hasil_prescreening"     => "Diproses lebih lanjut ya"
            ];

            $insertPrescreening = AsmxLas::setEndpoint('insertPrescreeningBriguna')
                ->setBody([
                    'JSON' => json_encode($content_las_prescreening)
                ])->post('form_params');

            return $insertPrescreening;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertPrescoringBriguna($data) {
        \Log::info($data);
        try {
            $content_las_prescoring = [
                "Fid_aplikasi"              => "123",
                "Fid_cif_las"               => "1234",
                "Tp_produk"                 => "1",
                "Briguna_smart"             => "0",
                "Briguna_profesi"           => "0",
                "Tgl_perkiraan_pensiun"     => "31122099",
                "Payroll"                   => "1",
                "Gaji_per_bulan"            => "10000000",
                "Pendapatan_profesi"        => "0",
                "Potongan_per_bulan"        => "0",
                "Angsuran_lainnya"          => "0",
                "Plafond_briguna_existing"  => "0",
                "Angsuran_briguna_existing" => "0",
                "Gaji_bersih_per_bulan"     => "10000000",
                "Maksimum_angsuran"         => "7500000",
                "Suku_bunga"                => "12",
                "Sifat_suku_bunga"          => "annuitas",
                "Jangka_waktu"              => "24",
                "Maksimum_plafond"          => "159325404",
                "Permohonan_kredit"         => "125000000",
                "Baki_debet"                => "0",
                "Plafond_usulan"            => "120000000",
                "Angsuran_usulan"           => "5648817",
                "Rek_simpanan_bri"          => "1",
                "Riwayat_pinjaman"          => "0",
                "Penguasaan_cashflow"       => "2",
                "Kelengkapan_dokumen"       => "1"
            ];

            $insertPrescoring = AsmxLas::setEndpoint('insertPrescoringBriguna')
                ->setBody([
                    'JSON' => json_encode($content_las_prescoring)
                ])->post('form_params');

            return $insertPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertDataKreditBriguna($data) {
        \Log::info($data);
        try {
            $content_insertKreditBriguna = [
                "Fid_aplikasi"                     => empty($data) ? "45016" : $data,
                "Cif_las"                          => empty($data) ? "11039374" : $data,
                "Tp_produk"                        => empty($data) ? "1" : $data,
                "Id_kredit"                        => empty($data) ? "0" : $data,
                "Baru_perpanjangan"                => empty($data) ? "0" : $data,
                "Jenis_fasilitas"                  => empty($data) ? "0605" : $data,
                "Tujuan_membuka_rek"               => empty($data) ? "3" : $data,
                "Segmen_owner"                     => empty($data) ? "RITEL" : $data,
                "Sub_segmen_owner"                 => empty($data) ? "RITEL" : $data,
                "Kode_jangka_waktu"                => empty($data) ? "M" : $data,
                "Jangka_waktu"                     => empty($data) ? "24" : $data,
                "Sisa_jangka_waktu_sd_penyesuaian" => empty($data) ? "0" : $data,
                "Penggunaan_kredit"                => empty($data) ? "14" : $data,
                "Tujuan_penggunaan_kredit"         => empty($data) ? "text" : $data,
                "Valuta"                           => empty($data) ? "idr" : $data,
                "Maksimum_plafond"                 => empty($data) ? "10000000" : $data,
                "Plafon_induk"                     => empty($data) ? "0" : $data,
                "Provisi_kredit"                   => empty($data) ? "0.1" : $data,
                "Biaya_administrasi"               => empty($data) ? "500000" : $data,
                "Penalty"                          => empty($data) ? "50" : $data,
                "Interest_payment_frequency"       => empty($data) ? "1" : $data,
                "Pemrakarsa1"                      => empty($data) ? "123" : $data,
                "Uker_pemrakarsa"                  => empty($data) ? "00206" : $data,
                "Sifat_suku_bunga"                 => empty($data) ? "FIXED" : $data,
                "Discount"                         => empty($data) ? "0" : $data,
                "Pengadilan_terdekat"              => empty($data) ? "text" : $data,
                "Bupln"                            => empty($data) ? "text" : $data,
                "Agribisnis"                       => empty($data) ? "N" : $data,
                "Sifat_kredit"                     => empty($data) ? "40" : $data,
                "Golongan_kredit"                  => empty($data) ? "20" : $data,
                "Kode_fasilitas"                   => empty($data) ? "FWL" : $data,
                "Sandi_stp"                        => empty($data) ? "A801" : $data,
                "Jenis_penggunaan"                 => empty($data) ? "10" : $data,
                "Orientasi_penggunaan"             => empty($data) ? "9" : $data,
                "Sektor_ekonomi_sid"               => empty($data) ? "1101" : $data,
                "Sektor_ekonomi_lbu"               => empty($data) ? "11126" : $data,
                "Jenis_kredit_lbu"                 => empty($data) ? "5" : $data,
                "Sifat_kredit_lbu"                 => empty($data) ? "1" : $data,
                "Kategori_kredit_lbu"              => empty($data) ? "3" : $data,
                "Jenis_penggunaan_lbu"             => empty($data) ? "10" : $data,
                "Lokasi_proyek"                    => empty($data) ? "0591" : $data,
                "Nilai_proyek"                     => empty($data) ? "0" : $data,
                "Fasilitas_penyedia_dana"          => empty($data) ? "1999" : $data,
                "Baki_debet"                       => empty($data) ? "0" : $data,
                "Original_amount"                  => empty($data) ? "0" : $data,
                "Kelonggaran_tarik"                => empty($data) ? "0" : $data,
                "Denda"                            => empty($data) ? "0" : $data,
                "Sumber_aplikasi"                  => empty($data) ? "Pengajuan Sendiri" : $data,
                "Premi_asuransi_jiwa"              => empty($data) ? "0.75" : $data,
                "Perusahaan_asuransi"              => empty($data) ? "BJS" : $data,
                "Premi_beban_bri"                  => empty($data) ? "0" : $data,
                "Premi_beban_debitur"              => empty($data) ? "0.75" : $data,
                "Tanggal_jatuh_tempo"              => empty($data) ? "29112019" : $data,
                "Grace_period"                     => empty($data) ? "0" : $data,
                "Flag_promo"                       => empty($data) ? "1" : $data,
                "Fid_promo"                        => empty($data) ? "4" : $data,
                "Status_takeover"                  => empty($data) ? "0" : $data,
                "Bank_asal_takeover"               => empty($data) ? "" : $data,
                "Data2"                            => empty($data) ? "" : $data
            ];

            $insertKreditBriguna = AsmxLas::setEndpoint('insertDataKreditBriguna')
                ->setBody([
                    'JSON' => json_encode($content_insertKreditBriguna)
                ])->post('form_params');

            return $insertKreditBriguna;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function insertAgunanLainnya($data) {
        \Log::info($data);
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

            $insertPrescoring = AsmxLas::setEndpoint('insertAgunanLainnya')
                ->setBody([
                    'JSONData' => json_encode($content_insertAgunanLainnya)
                ])->post('form_params');

            return $insertPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function hitungCRSBrigunaKarya($data) {
        \Log::info($data);
        try {
            $Id_aplikasi = $data;

            $hitungPrescoring = AsmxLas::setEndpoint('hitungCRSBrigunaKarya')
                ->setBody([
                    'id_Aplikasi' => $Id_aplikasi
                ])->post('form_params');

            return $hitungPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }    
}
