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
            $content_las_debt = [
                "tp_produk"             => empty($data['tp_produk']) ? "1" : $data['tp_produk'],
                "uid"                   => empty($data['uid']) ? "10740" : $data['uid'],
                "kode_cabang"           => empty($data['kode_cabang']) ? "3143" : $data['kode_cabang'],
                "cif_las"               => empty($data['cif_las']) ? "11039307" : $data['cif_las'],
                "no_ktp"                => empty($data['no_ktp']) ? "3273240401890005" : $data['no_ktp'],
                "expired_ktp"           => empty($data['expired_ktp']) ? "31122897" : $data['expired_ktp'],
                "nama_debitur_1"        => empty($data['nama_debitur_1']) ? "Squad Consumer" : $data['nama_debitur_1'],
                "nama_tanpa_gelar"      => empty($data['nama_tanpa_gelar']) ? "Squad Consumer" : $data['nama_tanpa_gelar'],
                "nama_debitur_2"        => empty($data['nama_debitur_2']) ? "Squad Consumer" : $data['nama_debitur_2'],
                "nama_debitur_3"        => empty($data['nama_debitur_3']) ? "" : $data['nama_debitur_3'],
                "nama_debitur_4"        => empty($data['nama_debitur_4']) ? "" : $data['nama_debitur_4'],
                "tgl_lahir"             => empty($data['tgl_lahir']) ? "01081990" : $data['tgl_lahir'],
                "tempat_lahir"          => empty($data['tempat_lahir']) ? "Jawa Timur" : $data['tempat_lahir'],
                "status_perkawinan"     => empty($data['status_perkawinan']) ? "1" : $data['status_perkawinan'],
                "nama_pasangan"         => empty($data['nama_pasangan']) ? "Pasangan Squad" : $data['nama_pasangan'],
                "tgl_lahir_pasangan"    => empty($data['tgl_lahir_pasangan']) ? "18051992" : $data['tgl_lahir_pasangan'],
                "no_ktp_pasangan"       => empty($data['no_ktp_pasangan']) ? "3273240401890006" : $data['no_ktp_pasangan'],
                "perjanjian_pisah_harta"=> empty($data['perjanjian_pisah_harta']) ? "0" : $data['perjanjian_pisah_harta'],
                "jumlah_tanggungan"     => empty($data['jumlah_tanggungan']) ? "2" : $data['jumlah_tanggungan'],
                "bidang_usaha"          => empty($data['bidang_usaha']) ? "1111" : $data['bidang_usaha'],
                "status_gelar"          => empty($data['status_gelar']) ? "0100" : $data['status_gelar'],
                "keterangan_status_gelar" => empty($data['keterangan_status_gelar']) ? "ST" : $data['keterangan_status_gelar'],
                "jenis_kelamin"         => empty($data['jenis_kelamin']) ? "l" : $data['jenis_kelamin'],
                "nama_ibu"              => empty($data['nama_ibu']) ? "mother Squad" : $data['nama_ibu'],
                "alamat"                => empty($data['alamat']) ? "Jln Menteng" : $data['alamat'],
                "kelurahan"             => empty($data['kelurahan']) ? "Menteng" : $data['kelurahan'],
                "kecamatan"             => empty($data['kecamatan']) ? "Menteng" : $data['kecamatan'],
                "kabupaten"             => empty($data['kabupaten']) ? "Jakarta Pusat" : $data['kabupaten'],
                "kode_pos"              => empty($data['kode_pos']) ? "10310" : $data['kode_pos'],
                "fixed_line"            => empty($data['fixed_line']) ? "0274666777" : $data['fixed_line'],
                "no_hp"                 => empty($data['no_hp']) ? "082347383838" : $data['no_hp'],
                "lama_menetap"          => empty($data['lama_menetap']) ? "2" : $data['lama_menetap'],
                "email"                 => empty($data['email']) ? "squadconsumer@gmail.com" : $data['email'],
                "tgl_mulai_usaha"       => empty($data['tgl_mulai_usaha']) ? "02012010" : $data['tgl_mulai_usaha'],
                "kewarganegaraan"       => empty($data['kewarganegaraan']) ? "ID" : $data['kewarganegaraan'],
                "negara_domisili"       => empty($data['negara_domisili']) ? "ID" : $data['negara_domisili'],
                "kepemilikan_tempat_tinggal" => empty($data['kepemilikan_tempat_tinggal']) ? "1" : $data['kepemilikan_tempat_tinggal'],
                "kategori_portofolio"   => empty($data['kategori_portofolio']) ? "175" : $data['kategori_portofolio'],
                "golongan_debitur_sid"  => empty($data['golongan_debitur_sid']) ? "907" : $data['golongan_debitur_sid'],
                "golongan_debitur_lbu"  => empty($data['golongan_debitur_lbu']) ? "886" : $data['golongan_debitur_lbu'],
                "nama_kelg"             => empty($data['nama_kelg']) ? "" : $data['nama_kelg'],
                "telp_kelg"             => empty($data['telp_kelg']) ? "" : $data['telp_kelg'],
                "tgl_mulai_debitur"     => empty($data['tgl_mulai_debitur']) ? date('d-m-Y') : $data['tgl_mulai_debitur'],
                "jenis_rekening"        => empty($data['jenis_rekening']) ? "3" : $data['jenis_rekening'],
                "nama_bank_lain"        => empty($data['nama_bank_lain']) ? "" : $data['nama_bank_lain'],
                "pekerjaan_debitur"     => empty($data['pekerjaan_debitur']) ? "099" : $data['pekerjaan_debitur'],
                "alamat_usaha"          => empty($data['alamat_usaha']) ? "Jln Jatinegara" : $data['alamat_usaha'],
                "nama_perusahaan"       => empty($data['nama_perusahaan']) ? "PT Sanny Parsel" : $data['nama_perusahaan'],
                "resident_flag"         => empty($data['resident_flag']) ? "Y" : $data['resident_flag'],
                "customer_type"         => empty($data['customer_type']) ? "I" : $data['customer_type'],
                "hub_bank"              => empty($data['hub_bank']) ? "9900" : $data['hub_bank'],
                "pernah_pinjam"         => empty($data['pernah_pinjam']) ? "Ya" : $data['pernah_pinjam'],
                "sumber_utama"          => empty($data['sumber_utama']) ? "2" : $data['sumber_utama'],
                "federal_wh_code"       => empty($data['federal_wh_code']) ? "1" : $data['federal_wh_code'],
                "sub_customer_type"     => empty($data['sub_customer_type']) ? "I" : $data['sub_customer_type'],
                "segmen_bisnis_bri"     => empty($data['segmen_bisnis_bri']) ? "RITEL" : $data['segmen_bisnis_bri'],
                "transaksi_normal_harian" => "2",
                "alias"                 => empty($data['alias']) ? "Squad Enam" : $data['alias'],
                "agama"                 => empty($data['agama']) ? "ISL" : $data['agama'],
                "ket_agama"             => empty($data['ket_agama']) ? "" : $data['ket_agama'],
                "alamat_domisili"       => empty($data['alamat_domisili']) ? "Jln Cikini Ampiun" : $data['alamat_domisili'],
                "kodepos_domisili"      => empty($data['kodepos_domisili']) ? "10310" : $data['kodepos_domisili'],
                "kelurahan_domisili"    => empty($data['kelurahan_domisili']) ? "Pegangsaan" : $data['kelurahan_domisili'],
                "kecamatan_domisili"    => empty($data['kecamatan_domisili']) ? "Menteng" : $data['kecamatan_domisili'],
                "kota_domisili"         => empty($data['kota_domisili']) ? "Jakarta Pusat" : $data['kota_domisili'],
                "propinsi_domisili"     => empty($data['propinsi_domisili']) ? "DKI Jakarta" : $data['propinsi_domisili'],
                "jenis_pekerjaan"       => empty($data['jenis_pekerjaan']) ? "ADMI" : $data['jenis_pekerjaan'],
                "ket_pekerjaan"         => empty($data['ket_pekerjaan']) ? "" : $data['ket_pekerjaan'],
                "jabatan"               => empty($data['jabatan']) ? "01" : $data['jabatan'],
                "kelurahan_usaha"       => empty($data['kelurahan_usaha']) ? "Kebon Pala" : $data['kelurahan_usaha'],
                "kecamatan_usaha"       => empty($data['kecamatan_usaha']) ? "Kebon Pala" : $data['kecamatan_usaha'],
                "kota_usaha"            => empty($data['kota_usaha']) ? "Jakarta Timur" : $data['kota_usaha'],
                "propinsi_usaha"        => empty($data['propinsi_usaha']) ? "DKI Jakarta" : $data['propinsi_usaha'],
                "kodepos_usaha"         => empty($data['kodepos_usaha']) ? "10315" : $data['kodepos_usaha'],
                "tujuan_membuka_rekening" => empty($data['tujuan_membuka_rekening']) ? "ZZ" : $data['tujuan_membuka_rekening'],
                "ket_buka_rekening"     => empty($data['ket_buka_rekening']) ? "Pinjaman" : $data['ket_buka_rekening'],
                "penghasilan_per_bulan" => empty($data['penghasilan_per_bulan']) ? "G1" : $data['penghasilan_per_bulan'],
                "usia_mpp"              => empty($data['usia_mpp']) ? "50" : $data['usia_mpp'],
                "id_instansi"           => empty($data['id_instansi']) ? "1" : $data['id_instansi']
            ];
            // print_r($content_las_debt);exit();
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
                "Fid_aplikasi"           => empty($data['Fid_aplikasi']) ? "124" : $data['Fid_aplikasi'],
                "Ps_krd"                 => empty($data['Ps_krd']) ? "0" : $data['Ps_krd'],
                "Pks"                    => empty($data['Pks']) ? "0" : $data['Pks'],
                "Daftar_hitam_bi"        => empty($data['Daftar_hitam_bi']) ? "0" : $data['Daftar_hitam_bi'],
                "Daftar_kredit_macet_bi" => empty($data['Daftar_kredit_macet_bi']) ? "0" : $data['Daftar_kredit_macet_bi'],
                "Daftar_hitam_bri"       => empty($data['Daftar_hitam_bri']) ? "0" : $data['Daftar_hitam_bri'],
                "Tunggakan_di_bri"       => empty($data['Tunggakan_di_bri']) ? "0" : $data['Tunggakan_di_bri'],
                "Npl_instansi"           => empty($data['Npl_instansi']) ? "0" : $data['Npl_instansi'],
                "Sicd"                   => empty($data['Sicd']) ? "0" : $data['Sicd'],
                "Hasil_prescreening"     => empty($data['Hasil_prescreening']) ? "Diproses lebih lanjut ya" : $data['Hasil_prescreening']
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
                "Fid_aplikasi"              => empty($data['Fid_aplikasi']) ? "45045" : $data['Fid_aplikasi'],
                "Fid_cif_las"               => empty($data['Fid_cif_las']) ? "0" : $data['Fid_cif_las'],
                "Tp_produk"                 => empty($data['Tp_produk']) ? "1" : $data['Tp_produk'],
                "Briguna_smart"             => empty($data['Briguna_smart']) ? "0" : $data['Briguna_smart'],
                "Briguna_profesi"           => empty($data['Briguna_profesi']) ? "0" : $data['Briguna_profesi'],
                "Tgl_perkiraan_pensiun"     => empty($data['Tgl_perkiraan_pensiun']) ? "31122099" : $data['Tgl_perkiraan_pensiun'],
                "Payroll"                   => empty($data['Payroll']) ? "1" : $data['Payroll'],
                "Gaji_per_bulan"            => empty($data['Gaji_per_bulan']) ? "10000000" : $data['Gaji_per_bulan'],
                "Pendapatan_profesi"        => empty($data['Pendapatan_profesi']) ? "0" : $data['Pendapatan_profesi'],
                "Potongan_per_bulan"        => empty($data['Potongan_per_bulan']) ? "0" : $data['Potongan_per_bulan'],
                "Angsuran_lainnya"          => empty($data['Angsuran_lainnya']) ? "0" : $data['Angsuran_lainnya'],
                "Plafond_briguna_existing"  => empty($data['Plafond_briguna_existing']) ? "0" : $data['Plafond_briguna_existing'],
                "Angsuran_briguna_existing" => empty($data['Angsuran_briguna_existing']) ? "0" : $data['Angsuran_briguna_existing'],
                "Gaji_bersih_per_bulan"     => empty($data['Gaji_bersih_per_bulan']) ? "10000000" : $data['Gaji_bersih_per_bulan'],
                "Maksimum_angsuran"         => empty($data['Maksimum_angsuran']) ? "7500000" : $data['Maksimum_angsuran'],
                "Suku_bunga"                => empty($data['Suku_bunga']) ? "12" : $data['Suku_bunga'],
                "Sifat_suku_bunga"          => empty($data['Sifat_suku_bunga']) ? "annuitas" : $data['Sifat_suku_bunga'],
                "Jangka_waktu"              => empty($data['Jangka_waktu']) ? "24" : $data['Jangka_waktu'],
                "Maksimum_plafond"          => empty($data['Maksimum_plafond']) ? "159325404" : $data['Maksimum_plafond'],
                "Permohonan_kredit"         => empty($data['Permohonan_kredit']) ? "125000000" : $data['Permohonan_kredit'],
                "Baki_debet"                => empty($data['Baki_debet']) ? "0" : $data['Baki_debet'],
                "Plafond_usulan"            => empty($data['Plafond_usulan']) ? "120000000" : $data['Plafond_usulan'],
                "Angsuran_usulan"           => empty($data['Angsuran_usulan']) ? "5648817" : $data['Angsuran_usulan'],
                "Rek_simpanan_bri"          => empty($data['Rek_simpanan_bri']) ? "1" : $data['Rek_simpanan_bri'],
                "Riwayat_pinjaman"          => empty($data['Riwayat_pinjaman']) ? "0" : $data['Riwayat_pinjaman'],
                "Penguasaan_cashflow"       => empty($data['Penguasaan_cashflow']) ? "2" : $data['Penguasaan_cashflow'],
                "Kelengkapan_dokumen"       => empty($data['Kelengkapan_dokumen']) ? "1" : $data['Kelengkapan_dokumen'],
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
                "Fid_aplikasi"                     => empty($data['Fid_aplikasi']) ? "45016" : $data['Fid_aplikasi'],
                "Cif_las"                          => empty($data['Cif_las']) ? "0" : $data['Cif_las'],
                "Tp_produk"                        => empty($data['Tp_produk']) ? "1" : $data['Tp_produk'],
                "Id_kredit"                        => empty($data['Id_kredit']) ? "0" : $data['Id_kredit'],
                "Baru_perpanjangan"                => empty($data['Baru_perpanjangan']) ? "0" : $data['Baru_perpanjangan'],
                "Jenis_fasilitas"                  => empty($data['Jenis_fasilitas']) ? "0605" : $data['Jenis_fasilitas'],
                "Tujuan_membuka_rek"               => empty($data['Tujuan_membuka_rek']) ? "3" : $data['Tujuan_membuka_rek'],
                "Segmen_owner"                     => empty($data['Segmen_owner']) ? "RITEL" : $data['Segmen_owner'],
                "Sub_segmen_owner"                 => empty($data['Sub_segmen_owner']) ? "RITEL" : $data['Sub_segmen_owner'],
                "Kode_jangka_waktu"                => empty($data['Kode_jangka_waktu']) ? "M" : $data['Kode_jangka_waktu'],
                "Jangka_waktu"                     => empty($data['Jangka_waktu']) ? "24" : $data['Jangka_waktu'],
                "Sisa_jangka_waktu_sd_penyesuaian" => empty($data['Sisa_jangka_waktu_sd_penyesuaian']) ? "0" : $data['Sisa_jangka_waktu_sd_penyesuaian'],
                "Penggunaan_kredit"                => empty($data['Penggunaan_kredit']) ? "14" : $data['Penggunaan_kredit'],
                "Tujuan_penggunaan_kredit"         => empty($data['Tujuan_penggunaan_kredit']) ? "text" : $data['Tujuan_penggunaan_kredit'],
                "Valuta"                           => empty($data['Valuta']) ? "idr" : $data['Valuta'],
                "Maksimum_plafond"                 => empty($data['Maksimum_plafond']) ? "10000000" : $data['Maksimum_plafond'],
                "Plafon_induk"                     => empty($data['Plafon_induk']) ? "0" : $data['Plafon_induk'],
                "Provisi_kredit"                   => empty($data['Provisi_kredit']) ? "0.1" : $data['Provisi_kredit'],
                "Biaya_administrasi"               => empty($data['Biaya_administrasi']) ? "500000" : $data['Biaya_administrasi'],
                "Penalty"                          => empty($data['Penalty']) ? "50" : $data['Penalty'],
                "Interest_payment_frequency"       => empty($data['Interest_payment_frequency']) ? "1" : $data['Interest_payment_frequency'],
                "Pemrakarsa1"                      => empty($data['Pemrakarsa1']) ? "123" : $data['Pemrakarsa1'],
                "Uker_pemrakarsa"                  => empty($data['Uker_pemrakarsa']) ? "00206" : $data['Uker_pemrakarsa'],
                "Sifat_suku_bunga"                 => empty($data['Sifat_suku_bunga']) ? "FIXED" : $data['Sifat_suku_bunga'],
                "Discount"                         => empty($data['Discount']) ? "0" : $data['Discount'],
                "Pengadilan_terdekat"              => empty($data['Pengadilan_terdekat']) ? "text" : $data['Pengadilan_terdekat'],
                "Bupln"                            => empty($data['Bupln']) ? "text" : $data['Bupln'],
                "Agribisnis"                       => empty($data['Agribisnis']) ? "N" : $data['Agribisnis'],
                "Sifat_kredit"                     => empty($data['Sifat_kredit']) ? "40" : $data['Sifat_kredit'],
                "Golongan_kredit"                  => empty($data['Golongan_kredit']) ? "20" : $data['Golongan_kredit'],
                "Kode_fasilitas"                   => empty($data['Kode_fasilitas']) ? "FWL" : $data['Kode_fasilitas'],
                "Sandi_stp"                        => empty($data['Sandi_stp']) ? "A801" : $data['Sandi_stp'],
                "Jenis_penggunaan"                 => empty($data['Jenis_penggunaan']) ? "10" : $data['Jenis_penggunaan'],
                "Orientasi_penggunaan"             => empty($data['Orientasi_penggunaan']) ? "9" : $data['Orientasi_penggunaan'],
                "Sektor_ekonomi_sid"               => empty($data['Sektor_ekonomi_sid']) ? "1101" : $data['Sektor_ekonomi_sid'],
                "Sektor_ekonomi_lbu"               => empty($data['Sektor_ekonomi_lbu']) ? "11126" : $data['Sektor_ekonomi_lbu'],
                "Jenis_kredit_lbu"                 => empty($data['Jenis_kredit_lbu']) ? "5" : $data['Jenis_kredit_lbu'],
                "Sifat_kredit_lbu"                 => empty($data['Sifat_kredit_lbu']) ? "1" : $data['Sifat_kredit_lbu'],
                "Kategori_kredit_lbu"              => empty($data['Kategori_kredit_lbu']) ? "3" : $data['Kategori_kredit_lbu'],
                "Jenis_penggunaan_lbu"             => empty($data['Jenis_penggunaan_lbu']) ? "10" : $data['Jenis_penggunaan_lbu'],
                "Lokasi_proyek"                    => empty($data['Lokasi_proyek']) ? "0591" : $data['Lokasi_proyek'],
                "Nilai_proyek"                     => empty($data['Nilai_proyek']) ? "0" : $data['Nilai_proyek'],
                "Fasilitas_penyedia_dana"          => empty($data['Fasilitas_penyedia_dana']) ? "1999" : $data['Fasilitas_penyedia_dana'],
                "Baki_debet"                       => empty($data['Baki_debet']) ? "0" : $data['Baki_debet'],
                "Original_amount"                  => empty($data['Original_amount']) ? "0" : $data['Original_amount'],
                "Kelonggaran_tarik"                => empty($data['Kelonggaran_tarik']) ? "0" : $data['Kelonggaran_tarik'],
                "Denda"                            => empty($data['Denda']) ? "0" : $data['Denda'],
                "Sumber_aplikasi"                  => empty($data['Sumber_aplikasi']) ? "Pengajuan Sendiri" : $data['Sumber_aplikasi'],
                "Premi_asuransi_jiwa"              => empty($data['Premi_asuransi_jiwa']) ? "0.75" : $data['Premi_asuransi_jiwa'],
                "Perusahaan_asuransi"              => empty($data['Perusahaan_asuransi']) ? "BJS" : $data['Perusahaan_asuransi'],
                "Premi_beban_bri"                  => empty($data['Premi_beban_bri']) ? "0" : $data['Premi_beban_bri'],
                "Premi_beban_debitur"              => empty($data['Premi_beban_debitur']) ? "0.75" : $data['Premi_beban_debitur'],
                "Tanggal_jatuh_tempo"              => empty($data['Tanggal_jatuh_tempo']) ? "29112099" : $data['Tanggal_jatuh_tempo'],
                "Grace_period"                     => empty($data['Grace_period']) ? "0" : $data['Grace_period'],
                "Flag_promo"                       => empty($data['Flag_promo']) ? "1" : $data['Flag_promo'],
                "Fid_promo"                        => empty($data['Fid_promo']) ? "4" : $data['Fid_promo'],
                "Status_takeover"                  => empty($data['Status_takeover']) ? "0" : $data['Status_takeover'],
                "Bank_asal_takeover"               => empty($data['Bank_asal_takeover']) ? "" : $data['Bank_asal_takeover'],
                "Data2"                            => empty($data['Data2']) ? "" : $data['Data2']
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
        \Log::info($data);
        try {
            $Id_aplikasi = $data;

            $hitungPrescoring = AsmxLas::setEndpoint('hitungCRSBrigunaKarya')
                ->setBody([
                    'id_Aplikasi' => empty($Id_aplikasi) ? "10" : $Id_aplikasi
                ])->post('form_params');

            return $hitungPrescoring;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function kirimPemutus($data) {
        \Log::info($data);
        try {
            $kirim = AsmxLas::setEndpoint('kirimPemutus')
                ->setBody([
                    'id_aplikasi'   => empty($data['id_aplikasi']) ? "45016" : $data['id_aplikasi'],
                    'uid'           => empty($data['uid']) ? "10740" : $data['uid'],
                    'flag_override' => empty($data['flag_override']) ? "N" : $data['flag_override']
                ])->post('form_params');

            return $kirim;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function getStatusInterface($data) {
        \Log::info($data);
        try {
            $get = AsmxLas::setEndpoint('getStatusInterface')
                ->setBody([
                    'id_aplikasi'   => empty($data) ? "45016" : $data
                ])->post('form_params');

            return $get;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function putusSepakat($data) {
        \Log::info($data);
        try {
            $get = AsmxLas::setEndpoint('putusSepakat')
                ->setBody([
                    'JSONData'   => empty($data) ? "45016" : $data
                ])->post('form_params');

            return $get;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryInstansiBriguna($data) {
        \Log::info($data);
        try {
            $inquiry = AsmxLas::setEndpoint('inquiryInstansiBriguna')
                ->setBody([
                    'branch'   => empty($data) ? "45016" : $data
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquirySifatKredit($data) {
        \Log::info($data);
        try {
            $inquiry = AsmxLas::setEndpoint('inquirySifatKredit')
                ->setBody([
                    'param'   => empty($data) ? "45016" : $data
                ])->post('form_params');

            return $inquiry;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryListPutusan($data) {
        \Log::info($data);
        try {
            $uid = $data;

            $inquiryListPutusan = AsmxLas::setEndpoint('inquiryListPutusan')
                ->setBody([
                    'uid' => empty($uid) ? "10740" : $uid
                ])->post('form_params');

            return $inquiryListPutusan;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    }

    public function inquiryUserLAS($data) {
        \Log::info($data);
        try {
            $pn = $data;

            $inquiryUserLAS = AsmxLas::setEndpoint('inquiryUserLAS')
                ->setBody([
                    'PN' => empty($pn) ? "00066777" : $pn
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
}
